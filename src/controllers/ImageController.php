<?php

namespace controller;

use app\core\Controller;
use Slim\Http\Request;
use models\Image;

class ImageController extends Controller
{

    public function actionIndex(Request $request)
    {
        $images = Image::getImageSetByUnitId($request->getAttribute('id'));

        return $this->render('image/index', ['images' => $images]);
    }
}