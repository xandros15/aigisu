<?php

namespace app\upload;

interface DirectServer
{

    public function setMimeTypes(array $mimeTypes);

    public function upload();

    public function setDirectory($directory, $root = false);

    public function file($fileOrUrl);

    public function setValidator(array $callback);

    public function getErrors();

    public function set_error($message);
}