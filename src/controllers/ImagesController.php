<?php

namespace controller;

use app\core\Controller;
use Slim\Http\Response;
use Slim\Http\Request;
use models\Images;

class ImagesController extends Controller
{

    public function actionIndex(Request $request, Response $response)
    {
        $id       = $request->getAttribute('id');
        $imageSet = Images::imagesByUnit($id);
        $images   = $imageSet->getSortedImages();
        $response->write($this->render('image/index', ['images' => $images]));
        return $response;
    }
}