<?php

namespace app\upload;

use app\google\GoogleFile;
use app\imgur\Imgur;
use app\upload\Upload;
use app\upload\UrlFiles;
use app\upload\validators\FileValidator;
use Exception;
use models\Images;
use RedBeanPHP\Facade as R;
use RedBeanPHP\OODBBean;

class UploadImages extends Upload
{
    public $files;
    public $post;
    public $newDir;
    protected $defaultMimeType  = 'image/png';
    protected $errors           = [];
    protected $defaultExtention = 'png';
    protected $validators;
    protected $destination;

    /** @var OODBBean */
    private $image;

    public function __construct($destination)
    {
        global $query;
        parent::__construct('tmp');
        $this->destination = $destination;
        $this->files       = $query->files;
        $this->post        = $query->post;
    }

    public function uploadFiles()
    {
        $avaibleImagesFields = Images::getTypeNames();
        foreach ($this->files as $input => $file) {
            if (!in_array($input, $avaibleImagesFields)) {
                continue;
            }
            if ($this->post->{$input}) {
                $results = $this->uploadFromServer($this->post->{$input});
            } else {
                if ($file['error']) {
                    continue;
                }
                $results = $this->uploadFromClient($file);
            }
            if ($this->errors) {
                var_dump($this->errors);
            } else {
                $this->addImageToDatabase($results, $input);
                (!$this->image) || $this->uploadOnExtendedServers();
            }
            (!isset($results['full_patch'])) || $this->deteleFile($results['full_patch']);
        }
    }

    private function uploadFromServer($url)
    {
        $file = new UrlFiles('tmp');
        $file->setMimeTypes([$this->defaultMimeType]);
        $file->loadFile($url);

        $this->errors = array_merge($this->errors, $file->getErrors());
        return $file->getFile();
    }

    private function uploadFromClient(array $file)
    {
        $this->file($file);
        $this->createValidator();
        $this->setValidators();

        $results      = $this->upload();
        $this->errors = array_merge($this->errors, $this->get_errors());
        return $results;
    }

    private function addImageToDatabase(array $results, $input)
    {
        $this->createDestination();

        R::begin();
        try {
            $this->transaction($results, $input);
            $newName = $this->getNewName($this->image->id);
            $this->moveFile($results['full_path'], $newName);
            R::commit();
        } catch (Exception $exc) {
            var_dump($exc->getMessage());
            R::rollback();
        }
    }

    private function transaction(array $results, $input)
    {
        if (!is_string($input)) {
            throw new Exception('input isn\'t string');
        }
        $unit = R::load(TB_NAME, (int) $this->post->id);

        if (!$unit) {
            throw new Exception('wrong id');
        }
        if ($unit->{$input}) {
            throw new Exception('Image exists');
        }
        if (!$unit->name) {
            throw new Exception('Unit name is null');
        }
        $this->setImage($results['full_path']);
        $this->image->type     = $input;
        $unit->ownImagesList[] = $this->image;
        R::store($unit);
    }

    private function uploadOnExtendedServers()
    {

        R::begin();
        try {
            $this->uploadOnGoogleDrive();
            R::commit();
        } catch (Exception $exc) {
            R::rollback();
            error_log($exc->getMessage());
        }
        R::begin();
        try {
            $this->uploadOnImgur();
            R::commit();
        } catch (Exception $exc) {
            R::rollback();
            error_log($exc->getMessage());
        }
    }

    private function uploadOnGoogleDrive()
    {
        $google   = new GoogleFile();
        $google->setMimeType($this->defaultMimeType);
        $google->setExtension($this->defaultExtention);
        $google->setDescription('R18');
        $google->setName($this->image->type);
        $google->setFolderName($this->image->units->name);
        $google->setFilename($this->getNewName($this->image->id));
        $response = $google->upload()->resultOfUpload;
        if ($response) {
            $this->image->google = $response->id;
            R::store($this->image);
        }
    }

    private function uploadOnImgur()
    {
        $imgur    = Imgur::facade();
        $imgur->setFilename($this->getNewName($this->image->id));
        $imgur->setTitle($this->image->units->name);
        $imgur->setDescription('R18');
        $imgur->setAlbum(rtrim($this->image->type, '12'));
        $response = $imgur->uploadFile();
        if (isset($response['data']['id']) && isset($response['data']['deletehash'])) {
            $this->image->imgur   = $response['data']['id'];
            $this->image->delhash = $response['data']['deletehash'];
            R::store($this->image);
        }
    }

    private function moveFile($current, $destiny)
    {
        if (!rename($current, $destiny)) {
            throw new Exception('Can\'t rename file');
        }
    }

    private function getNewName($id)
    {
        return $this->newDir . $id . '.' . $this->defaultExtention;
    }

    private function setImage($fullPatch)
    {
        $this->image      = R::dispense(TB_IMAGES);
        $this->image->md5 = md5_file($fullPatch);
    }

    private function createValidator()
    {
        require_once 'FileValidator.php';
        $this->validators[] = [
            'validator' => new FileValidator(),
            'methods' => ['uploadValidator']
        ];
    }

    private function setValidators()
    {
        foreach ($this->validators as $validator) {
            $this->callbacks($validator['validator'], $validator['methods']);
        }
        $this->set_allowed_mime_types(['image/png']);
    }

    private function createDestination()
    {
        $newDir = ROOT_DIR . $this->destination;
        if (!is_dir($newDir)) {
            mkdir($newDir, 755);
        }

        $this->newDir = $newDir . DIRECTORY_SEPARATOR;
    }

    private function deteleFile($filename)
    {
        if (is_file($filename) && is_executable($filename)) {
            return unlink($filename);
        }
        return false;
    }
}