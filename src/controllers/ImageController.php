<?php

namespace controller;

use app\core\Controller;
use Slim\Http\Request;
use models\Image;

class ImageController extends Controller
{

    public function actionIndex(Request $request)
    {
        $id       = $request->getAttribute('id');
        $imageSet = Image::imagesByUnit($id);
        $images   = $imageSet->getSortedImages();

        return $this->render('image/index', ['images' => $images]);
    }
}