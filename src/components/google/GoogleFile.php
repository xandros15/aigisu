<?php

namespace app\google;

use app\google\GoogleServer;
use Exception;

class GoogleFile extends GoogleServer
{
    public $filename    = '';
    public $name        = '';
    public $extension   = '';
    public $description = '';
    public $mimeType    = '';
    public $folderName  = '';

    /** @var Google_Service_Drive_DriveFile */
    protected $folder;

    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
        return $this;
    }

    public function setFolderName($folderName)
    {
        $this->folderName = $folderName;
        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setExtension($extension)
    {
        $this->extension = $extension;
    }

    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    public function upload()
    {
        $this->validate();
        $this->setFolder();
        $this->resultOfUpload     = $this->uploadFile($this);
        $this->resultOfPermission = $this->createPermissionForFile($this->resultOfUpload->id, true);
        return $this;
    }

    private function validate()
    {
        if (!$this->mimeType || !is_string($this->mimeType)) {
            throw new Exception('Mime type is not set or no string');
        }
        if (!$this->name || !is_string($this->name)) {
            throw new Exception('Name type is not set or no string');
        }
        if (!$this->extension || !is_string($this->extension)) {
            throw new Exception('Extention type is not set or no string');
        }
        if (!$this->filename || !is_string($this->filename)) {
            throw new Exception('Filename is not set or no string');
        }
        if (!is_file($this->filename)) {
            throw new Exception("File '{$this->filename}' no exist");
        }
        if (!$this->folderName || !is_string($this->folderName)) {
            throw new Exception('Folder name is not set or no string.');
        }
    }

    public function getFilesInfo()
    {
        // Print the names and IDs for up to 10 files.
        $results = $this->service->files->listFiles(['maxResults' => 10]);


        if (count($results->getItems()) == 0) {
            print "No files found.\n";
        } else {
            print "Files:\n";
            foreach ($results->getItems() as $file) {
                /* @var $file Google_Service_Drive_DriveFile */
                printf("%s (%s)\n", $file->getTitle(), $file->getId());
            }
        }
    }

    protected function setFolder()
    {
        $mimeType = self::FOLDER_MIME_TYPE;
        $parentId = $this->mainFolder->id;
        $query    = "title = '{$this->folderName}' and mimeType = '{$mimeType}' and '{$parentId}' in parents and trashed = false";
        $files    = $this->service->files->listFiles(['q' => $query])->getItems();
        if ($files) {
            $this->folder = reset($files);
        } else {
            $this->folder = $this->createNewFolder($this->folderName, false, $parentId);
            $this->createPermissionForFile($this->folder->id, true);
        }
    }

    protected function emptyTrash()
    {
        if (!($this->service instanceof Google_Service_Drive)) {
            throw new Exception('No service set or no instance of Google_Service_Drive');
        }
        $this->service->files->trash();
    }
}