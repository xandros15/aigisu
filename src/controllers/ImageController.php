<?php

namespace controller;

use app\core\Controller;
use Slim\Http\Request;
use models\Image;

class ImageController extends Controller
{

    public function actionIndex(Request $request)
    {
        $images = Image::where('unit_id', $request->getAttribute('id'))->get();

        return $this->render('image/index', ['images' => $images->sortByDesc('scene')->groupBy('server')]);
    }
}