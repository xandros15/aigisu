<?php

namespace Controllers;

use Aigisu\Controller;
use Models\Image;
use Slim\Http\Request;

class ImageController extends Controller
{

    public function actionIndex(Request $request)
    {
        $images = Image::getImageSetByUnitId($request->getAttribute('id'));

        return $this->render('image/index', ['images' => $images]);
    }
}