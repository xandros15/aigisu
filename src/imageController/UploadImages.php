<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app;

use app\validators\FileValidator;
use app\UrlFiles;
use app\Images;
use app\google\GoogleFile;
use RedBeanPHP\Facade as R;
use Exception;

/**
 * Description of UploadImages
 *
 * @author user
 */
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

    /** @var RedBeanPHP\OODBBean */
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
                (!$this->image) || $this->uploadOnExtendedServer($input);
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

    private function uploadOnExtendedServer($name)
    {
        $otherServer = new GoogleFile();
        $otherServer->setMimeType($this->defaultMimeType);
        $otherServer->setExtension($this->defaultExtention);
        $otherServer->setDescription('R18');
        $otherServer->setName($name);
        $otherServer->setFolderName($this->image->units->name);
        $otherServer->setFilename($this->getNewName($this->image->id));
        R::begin();
        try {
            $this->image->google = $otherServer->upload()->resultOfUpload->id;
            R::store($this->image);
            R::commit();
        } catch (Exception $exc) {
            R::rollback();
            var_dump($exc->getMessage());
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