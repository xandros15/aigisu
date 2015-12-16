<?php

namespace controller;

use app\core\Controller;
use Slim\Http\Response;
use Slim\Http\Request;
use models\Image;

class ImageController extends Controller
{

    public function actionIndex(Request $request, Response $response)
    {
        $id       = $request->getAttribute('id');
        $imageSet = Image::imagesByUnit($id);
        $images   = $imageSet->getSortedImages();
        $response->write($this->render('image/index', ['images' => $images]));
        return $response;
    }
}