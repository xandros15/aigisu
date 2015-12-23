<?php

namespace controller;

use app\core\Controller;
use app\alert\Alert;
use app\google\GoogleFile;
use app\imgur\Imgur;
use app\upload\Rely;
use models\ImageFile;
use Slim\Http\Request;
use app\upload\Upload;
use Exception;

class ImageFileController extends Controller
{
    /** @var Rely */
    protected $rely;

    public function actionCreate(Request $request)
    {
        $this->uploadFiles($request->getUploadedFiles(), $request->getParams(), $request->getAttribute('id'));

        return $this->goBack();
    }

    protected function setRely()
    {
        $this->rely = new Rely();
        $this->rely->setDirectory(ImageFile::IMAGE_DIRECTORY);
        $this->rely->setExtendedServer('google', new GoogleFile(), 'uploadOnGoogleDrive');
        $this->rely->setExtendedServer('imgur', Imgur::facade(), 'uploadOnImgur');
    }

    protected function uploadFiles($files, $post, $unitId)
    {
        $this->setRely();
        $errors = [];
        foreach ($files as $key => $file) {
            if (!isset($post[$key])) {
                continue;
            }

            /* @var $uploadedFile Upload */
            if (!empty($post[$key]['url'])) {
                $uploadedFile = $this->rely->uploadFromServer($post[$key]['url'], $errors);
            } elseif ($file->getError() === UPLOAD_ERR_OK) {
                $uploadedFile = $this->rely->uploadFromClient($file, $errors);
            } else {
                continue;
            }

            if ($errors) {
                $this->showErrors($errors);
                continue;
            }

            $model = new ImageFile([
                'unit_id' => $unitId,
                'scene' => $post[$key]['scene'],
                'server' => $post[$key]['server'],
                'md5' => $uploadedFile->md5
            ]);

            $stuffForValidate = [
                'size' => $uploadedFile->filesize,
                'file' => $uploadedFile->filename,
                'mime' => $uploadedFile->mimeType,
                'height' => $uploadedFile->height,
                'width' => $uploadedFile->width
            ];

            if ($this->transaction($model, $uploadedFile, $stuffForValidate)) {
                $this->uploadToExtendetServers($model, $uploadedFile, $stuffForValidate);
            }
        }
    }

    protected function uploadToExtendetServers(ImageFile $model, Upload $uploadedFile, $stuffForValidate)
    {
        foreach ($this->rely->extendedServers as $name => $server) {
            if (!is_callable($server['callback'])) {
                continue;
            }
            $results = call_user_func($server['callback'], $model, $uploadedFile);
            if ($results instanceof Exception) {
                Alert::add("Upload on {$name} Failed. Error: " . $results->getMessage(), Alert::WARNING);
                continue;
            }
            switch ($name) {
                case 'imgur':
                    $model->imgur   = $results['data']['id'];
                    $model->delhash = $results['data']['deletehash'];
                    break;
                case 'google':
                    $model->google  = $results->id;
                    break;
            }
            if ($model->validate($stuffForValidate) && $model->save()) {
                Alert::add(
                    sprintf("Successful uploaded %s %s %s on %s", $model->unit->name, $model->server,
                        ImageFile::imageSceneToHuman($model->scene), $name));
            }
        }
    }

    private function transaction(ImageFile $model, Upload $uploadedFile, $stuffForValidate)
    {
        $model->getConnection()->beginTransaction();
        if ($model->validate($stuffForValidate) && $model->save()) {
            try {
                $uploadedFile->upload($model->id);

                $model->getConnection()->commit();

                Alert::add(sprintf("Successful uploaded %s %s %s", $model->unit->name, $model->server,
                        ImageFile::imageSceneToHuman($model->scene)));
                return true;
            } catch (Exception $exc) {
                $model->getConnection()->rollBack();
                Alert::add($exc->getTraceAsString(), Alert::ERROR);
            }
        }

        $this->deteleFile($uploadedFile->filename);
        return false;
    }

    private function deteleFile($filename)
    {
        return (is_file($filename) && is_executable($filename)) ? unlink($filename) : false;
    }

    private function showErrors(array $errors)
    {
        foreach ($errors as $message) {
            Alert::add($message, Alert::ERROR);
        }
    }
}