<?php

namespace app\upload;

use app\upload\Upload;

class FileFromClient extends Upload implements DirectServer
{

    public function setValidator(array $callback)
    {
        $this->callbacks($callback);
    }

    public function setDirectory($directory, $root = false)
    {
        if ($root) {
            $this->root = $root;
        } else {
            $this->root = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR;
        }
        $this->set_destination($directory);
    }

    public function setMimeTypes(array $mimeTypes)
    {
        $this->set_allowed_mime_types($mimeTypes);
    }

    public function getErrors()
    {
        return $this->get_errors();
    }
}