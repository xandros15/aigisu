<?php

namespace app\upload;

interface ExtedndetServer
{

    public function setDescription($description);

    public function setName($name);

    public function setCatalog($catalog);

    public function setFilename($filename);

    public function uploadFile();
}