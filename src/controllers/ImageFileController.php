<?php

namespace controller;

use models\Unit;
use models\Image;
use app\core\Controller;
use app\alert\Alert;
use app\google\GoogleFile;
use app\imgur\Imgur;
use app\upload\Rely;
use app\upload\SingleFile;
use RedBeanPHP\Facade as R;
use RedBeanPHP\OODBBean;
use Exception;
use Slim\Http\Response;
use Slim\Http\Request;

class ImageFileController extends Controller
{
    public $newDir;
    public $unitId;
    protected $rely;

    /** @var SingleFile */
    protected $files = [];
    protected $destination;

    /** @var OODBBean */
    private $image;

    public function actionCreate(Request $request, Response $response)
    {
        $this->setRely(Image::IMAGE_DIRECTORY);
        $this->upload();
        return $response->withRedirect('/');
    }

    public function setRely($destination)
    {
        $this->destination = $destination;
        $this->rely        = new Rely();
        $this->rely->setMimeType('image/png');
        $this->rely->setExtendedServer('google', new GoogleFile());
        $this->rely->setExtendedServer('imgur', Imgur::facade());
    }

    public function upload()
    {
        global $query;
        $this->setFiles((array) $query->files, (array) $query->post);
        $this->uploadEachFiles();
    }

    protected function uploadEachFiles()
    {
        foreach ($this->files as $key => $file) {
            $errorHolder = [];
            if (!isset($file->file)) {
                continue;
            } elseif (is_string($file->file)) {
                $this->files[$key]->setResults($this->rely->uploadFromServer($file->file, $errorHolder));
            } elseif (is_array($file->file)) {
                $this->files[$key]->setResults($this->rely->uploadFromClient($file->file, $errorHolder));
            }
            if ($errorHolder) {
                foreach ($errorHolder as $error) {
                    Alert::add("{$file['name']}: $error", Alert::ERROR);
                }
                continue;
            }
            $this->files[$key]->setObject();
            if ($this->addImageToDatabase($this->files[$key])) {
                $this->uploadToExtendetServers($this->files[$key]);
                $this->completeUpload($this->files[$key]);
                Alert::add("File: '{$this->files[$key]->name}' sucessful uploaded");
            } else {
                R::trash($this->image);
                Alert::add("File: '{$this->files[$key]->name}' error occurent. Can't upload to server.");
                $this->deteleFile($file->full_path);
            }
            $this->image = null;
        }
    }

    private function setFiles(array $files, array $post)
    {
        if (!empty($post['id'])) {
            $this->unitId = $post['id'];
        } else {
            throw new Exception('No unit Id');
        }
        foreach ($files as $key => $file) {
            if (isset($post[$key]) && ($newFileObject = SingleFile::loadFile($file, $post[$key]))) {
                $this->files[$key] = $newFileObject;
            }
        }
    }

    protected function uploadToExtendetServers(SingleFile $file)
    {
        $isChanged = false;
        if (!$this->image instanceof OODBBean) {
            return false;
        }
        $google = $this->rely->uploadOnGoogleDrive($this->image, $file->object);
        if ($google instanceof Exception) {
            Alert::add("Upload on Google Failed. Error: " . $google->getMessage(), Alert::WARNING);
        } elseif (isset($google->id)) {
            $this->image->google = $google->id;

            $isChanged = true;
            Alert::add("Upload on Google Successful.", Alert::SUCCESS);
        }
        $imgur = $this->rely->uploadOnImgur($this->image, $file->object);
        if ($imgur instanceof Exception) {
            Alert::add("Upload on Imgur Failed. Error: " . $imgur->getMessage(), Alert::WARNING);
        } elseif (isset($imgur['data']['id']) && isset($imgur['data']['deletehash'])) {
            $this->image->imgur   = $imgur['data']['id'];
            $this->image->delhash = $imgur['data']['deletehash'];

            $isChanged = true;
            Alert::add("Upload on Imgur Successful.", Alert::SUCCESS);
        }
        return ($isChanged) ? R::store($this->image) : false;
    }

    private function addImageToDatabase(SingleFile $file)
    {
        R::begin();
        try {
            $this->transaction($file);
            R::commit();
        } catch (Exception $exc) {
            Alert::add("{$file->name}: " . $exc->getMessage(), Alert::ERROR);
            R::rollback();
            return false;
        }
        return true;
    }

    private function transaction(SingleFile $file)
    {
        $unit = R::load(Unit::tableName(), $this->unitId);
        if (!$unit->name) {
            throw new Exception("File: '{$file->name}' Unit name is null");
        }
        $isRecordExist = (bool) R::find(Image::tableName(),
                ' `units_id` = :id and `scene` = :scene and `server` = :server ',
                [':id' => $unit->id, ':scene' => $file->scene, ':server' => $file->server]);
        if ($isRecordExist) {
            throw new Exception("File: '{$file->name}' Image exist");
        }
        $this->image           = R::dispense(Image::tableName());
        $this->image->md5      = md5_file($file->full_path);
        $this->image->server   = $file->server;
        $this->image->scene    = $file->scene;
        $unit->ownImagesList[] = $this->image;
        R::store($unit);
    }

    private function createDestination()
    {
        $newDir = ROOT_DIR . $this->destination;
        if (!is_dir($newDir)) {
            mkdir($newDir, 755);
        }

        $this->newDir = $newDir . DIRECTORY_SEPARATOR;
    }

    private function completeUpload(SingleFile $file)
    {
        $this->createDestination();
        $file->object->move($this->newDir, sprintf('%d.%s', $this->image->id, $file->object->guessExtension()));
        $this->deteleFile($file->full_path);
    }

    private function deteleFile($filename)
    {
        return (is_file($filename) && is_executable($filename)) ? unlink($filename) : false;
    }
}