<?php

namespace controller;

use models\Unit;
use app\core\Controller;
use app\alert\Alert;
use app\google\GoogleFile;
use app\imgur\Imgur;
use app\upload\Rely;
use app\upload\SingleFile;
use Slim\Http\Request;
use RedBeanPHP\R;
use Exception;

class ImageFileController extends Controller
{
    public $newDir;
    public $unit;
    protected $rely;

    /** @var SingleFile */
    protected $files = [];
    protected $destination;

    public function actionCreate(Request $request)
    {
        $this->request = $request;
        $this->setRely();
        $this->upload($request->getAttribute('id'));
        return $this->goBack();
    }

    public function setRely()
    {
        $this->rely = new Rely();
        $this->rely->setExtendedServer('google', new GoogleFile(), 'uploadOnGoogleDrive');
        $this->rely->setExtendedServer('imgur', Imgur::facade(), 'uploadOnImgur');
    }

    public function upload($id)
    {
        $this->setUnit($id);
        $this->setFiles();
        $this->uploadEachFiles();
    }

    protected function uploadEachFiles()
    {
        foreach ($this->files as $file) {
            $file->upload();
            if (!$file->isErrors()) {
                Alert::add("Successful uploaded {$file->unit->name} {$file->server} {$file->scene} scene");
                $this->uploadToExtendetServers($file);
            }
        }
    }

    protected function uploadToExtendetServers(SingleFile $image)
    {
        $isChanged = false;
        foreach ($this->rely->extendedServers as $name => $server) {
            if (!is_callable($server['callback'])) {
                continue;
            }
            $results = call_user_func($server['callback'], $image);
            if ($results instanceof Exception) {
                Alert::add("Upload on {$name} Failed. Error: " . $results->getMessage(), Alert::WARNING);
                continue;
            }
            switch ($name) {
                case 'imgur':
                    $image->imageBean->imgur   = $results['data']['id'];
                    $image->imageBean->delhash = $results['data']['deletehash'];
                    break;
                case 'google':
                    $image->imageBean->google  = $results->id;
                    break;
            }
            Alert::add("Successful uploaded {$image->unit->name} {$image->server} {$image->scene} scene on {$name}");
            $isChanged = true;
        }
        return ($isChanged) ? R::store($image->imageBean) : false;
    }

    protected function setUnit($id)
    {
        if (!($unit = R::load(Unit::tableName(), $id))) {
            throw new Exception("No find unit with id:'{$id}'");
        }
        $this->unit = $unit;
    }

    protected function setFiles()
    {
        $files  = $this->request->getUploadedFiles();
        $post   = $this->request->getParams();
        $errors = [];
        foreach ($files as $key => $file) {
            if (isset($post[$key])) {
                if ($this->isFileFromUrl($post[$key])) {
                    $uploadedFile = $this->rely->uploadFromServer($post[$key]['url'], $errors);
                } elseif ($file->getError() === UPLOAD_ERR_OK) {
                    $uploadedFile = $this->rely->uploadFromClient($file, $errors);
                } else {
                    continue;
                }
                if ($errors) {
                    foreach ($errors as $message) {
                        Alert::add($message, Alert::ERROR);
                    }
                    continue;
                }

                $model = new SingleFile();
                $model->setPost($post[$key]);
                $model->setFile($uploadedFile);
                $model->setUnit($this->unit);

                if ($model->validate()) {
                    $this->setFile($key, $model);
                }
            }
        }
    }

    protected function setFile($key, SingleFile $model)
    {
        $this->files[$key] = $model;
    }

    protected function isFileFromUrl($post)
    {
        return !empty($post['url']);
    }
}