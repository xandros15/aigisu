<?php

use app\imgur\Imgur;
use app\upload\FileFromClient;
use Illuminate\Database\Eloquent\Builder as Query;
use models\Image;
use models\ImageFile;
use models\Tag;
use models\Unit;

defined('ROOT_DIR') || define('ROOT_DIR', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
require_once 'main.php';
$main = new Main();
$main->bootstrap();

if (php_sapi_name() != 'cli') {
    throw new Exception('This application must be run on the command line.');
}

function uploadOnImgur()
{
    $image = Image::whereNull('imgur')->get();

    $rely = new app\upload\Rely();
    $file = new FileFromClient();
    $rely->setExtendedServer('imgur', app\imgur\Imgur::facade(), 'uploadOnImgur');
    /* @var $model ImageFile */
    foreach ($image as $model) {
        $file->filename = ROOT_DIR . ImageFile::IMAGE_DIRECTORY . '/' . $model->id . '.png';
        $results = $rely->uploadOnImgur($model, $file);
        //var_dump($model->toArray());
        if ($results instanceof Exception) {
            var_dump($results->getMessage());
            die();
        }

        $model->setAttribute('imgur', $results['data']['id']);
        $model->setAttribute('delhash', $results['data']['deletehash']);
        if ($model->validate() && $model->save()) {
            var_dump($results['success']);
        }
    }
}

function createNewTag($name)
{
    $tag = new Tag(['name' => $name]);
    dump($tag->save());
}

function getAllTags()
{
    $tags = Tag::all();
    return $tags->toArray();
}

function addTagToUnit($tagId, $unitId)
{
    /* @var $unit Unit */
    $unit = Unit::find($unitId);
    $unit->tags()->attach($tagId);
}

function checkImages()
{
    /* @var $query Query */
    $query = Unit::with('images');
    $final = $query->whereDoesntHave('images')->where('is_male', '!=', '1')->get()->toArray();
    dump($final);
}

function refreshImgur()
{
    $imgur = Imgur::facade();
    $imgur->refreshTokens();
}

refreshImgur();
uploadOnImgur();