<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app;

use app\validators\FileValidator;
use app\UrlFiles;
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
    protected $errors           = [];
    protected $defaultExtention = 'png';
    protected $validators;
    protected $destination;

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
        foreach ($this->files as $input => $file) {
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
            }
        }
    }

    private function uploadFromServer($url)
    {
        $file = new UrlFiles('tmp');
        $file->setMimeTypes(['image/png']);
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
        $image = $this->createImage($results['full_path']);
        $unit  = R::load(TB_NAME, (int) $this->post->id);

        if (!$unit) {
            throw new Exception('wrong id');
        }
        if ($unit->{$input}) {
            throw new Exception('Image exists');
        }
        $unit->{$input} = $image;
        R::storeAll([$unit, $image]);
        if (!rename($results['full_path'], $this->getNewName($image->getID()))) {
            throw new Exception('Can\'t rename file');
        }
        if (is_file($results['full_path']) && is_executable($results['full_path'])) {
            unlink($results['full_path']);
        }
    }

    private function getNewName($id)
    {
        return $this->newDir . DIRECTORY_SEPARATOR . $id . '.' . $this->defaultExtention;
    }

    private function createImage($fullPatch)
    {
        $image      = R::dispense(TB_IMAGES);
        $image->md5 = md5_file($fullPatch);
        return $image;
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
        $newDir = ROOT_DIR . DIRECTORY_SEPARATOR . $this->destination;
        if (!is_dir($newDir)) {
            mkdir($newDir, 755);
        }

        $this->newDir = $newDir;
    }
}