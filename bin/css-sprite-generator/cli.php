#!/usr/bin/env
<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-11-14
 * Time: 16:56
 */
require_once __DIR__ . './Spriter.php';
use Xandros15\Spriter;

list(, $input, $output, $url) = $argv;

$files = array_filter(glob($input . '/*'), function ($file) {
    /* icons name is wrote in md5 w/o extension */
    return is_file($file) && !pathinfo($file, PATHINFO_EXTENSION) && strlen(basename($file)) == 32;
});

if ($files) {
    if (!is_dir($output) && !@mkdir($output, 0755, true)) {
        throw new \Exception('Cant create directory ' . $output);
    }
    $spriter = new Spriter($files, [
        Spriter::OPT_SPRITE_FORMAT => Spriter::FORMAT_JPG,
        Spriter::OPT_CSS_IMAGE_URL => str_replace(['http://', 'https://'], '//', $url),
    ]);

    $spriter->save($output . '/sprite.css', $output . '/sprite');
}