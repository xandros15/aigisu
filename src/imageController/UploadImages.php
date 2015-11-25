<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app;

use app\validators\FileValidator;
use RedBeanPHP\Facade as R;
use stdClass;
use Exception;

/**
 * Description of UploadImages
 *
 * @author user
 */
class UploadImages extends Upload
{
    public $files;
    public $newDir;
    protected $defaultExtention = 'png';
    protected $validators;
    protected $destination;

    public function __construct(stdClass $files, $destination)
    {
        parent::__construct('tmp');
        $this->destination = $destination;
        $this->files       = $files;
    }

    public function uploadFiles()
    {
        foreach ($this->files as $input => $file) {
            if ($file['error']) {
                continue;
            }
            $this->file($file);
            $this->createValidator();
            $this->setValidators();

            $results = $this->upload();
            $errors  = $this->get_errors();
            if ($errors) {
                var_dump($errors);
            } else {
                $this->addImageToDatabase($results, $input);
            }
        }
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
        global $query;
        $image = $this->createImage($results['full_path']);
        $unit  = R::load(TB_NAME, (int) $query->post->id);

        if (!$unit) {
            throw new Exception('wrong id');
        }
        if($unit->{$input}){
            throw new Exception('Image exists');
        }
        $unit->{$input} = $image;
        R::storeAll([$unit, $image]);
        if (!rename($results['full_path'], $this->getNewName($image->getID()))) {
            throw new Exception('Can\t rename file');
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
            'methods' => ['haveCorrectSize', 'isInDatabase']
        ];
    }

    private function setValidators()
    {
        foreach ($this->validators as $validator) {
            $this->callbacks($validator['validator'], $validator['methods']);
        }
        $this->set_max_file_size(1);
        $this->set_allowed_mime_types(['image/png']);
        $this->defaultExtention = 'png';
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