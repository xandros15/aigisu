<?php

namespace app\google;

use Exception;
use Google_Auth_Exception;
use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Google_Service_Drive_Permission;
use Google_Service_Drive_ParentReference;

class GoogleServer
{
    const APPLICATION_NAME   = 'aigisu';
    const CREDENTIALS_PATH   = CONFIG_DIR . 'credentials.json';
    const CLIENT_SECRET_PATH = CONFIG_DIR . 'key.json';
    const SCOPES             = Google_Service_Drive::DRIVE_FILE . ' ' . Google_Service_Drive::DRIVE_METADATA;
    const FOLDER_MIME_TYPE   = 'application/vnd.google-apps.folder';
    const MAIN_FOLDER_NAME   = 'aigisu';

    /** @var Google_Service_Drive_DriveFile */
    public $file;

    /** @var Google_Service_Drive_DriveFile */
    public $resultOfUpload;

    /** @var Google_Service_Drive_Permission */
    public $resultOfPermission;

    /** @var Google_Service_Drive */
    protected $service;

    /** @var Google_Service_Drive_DriveFile */
    protected $mainFolder;

    /** @var Google_Client */
    private $client;

    public function __construct()
    {
        $this->init();
    }

    private function init()
    {
        try {
            $this->setClient();
        } catch (Google_Auth_Exception $e) {
            $error[] = $e->getMessage();
        }
        try {
            $this->setService($this->client);
        } catch (Google_Auth_Exception $e) {
            $error[] = $e->getMessage();
        }
        try {
            $this->setMainFolder($this->service);
        } catch (Google_Auth_Exception $e) {
            $error[] = $e->getMessage();
        }
        if (isset($error) && $error) {
            throw new Exception('Have some problems: ' . explode('<br>', $error));
        }
    }

    protected function setMainFolder(Google_Service_Drive $service)
    {
        $folderName = self::MAIN_FOLDER_NAME;
        $mimeType   = self::FOLDER_MIME_TYPE;
        $query      = "title = '{$folderName}' and mimeType = '{$mimeType}' and trashed = false";
        $files      = $service->files->listFiles(['q' => $query])->getItems();
        if ($files) {
            $this->mainFolder = reset($files);
            return;
        } else {
            $this->mainFolder = $this->createNewFolder($folderName, 'R18 images for aigisu');
            $this->createPermissionForFile($this->mainFolder->id, true);
        }
    }

    protected function createNewFolder($folderName = 'new folder', $description = false, $parentId = false)
    {
        $file = new Google_Service_Drive_DriveFile();
        $file->setMimeType(self::FOLDER_MIME_TYPE);
        $file->setTitle($folderName);
        (!$description) || $file->setDescription($description);
        (!$parentId) || $file->setParents([$this->createParent($parentId)]);
        return $this->service->files->insert($file);
    }

    protected function upload(GoogleFile $googleFile)
    {
        $this->file = new Google_Service_Drive_DriveFile();
        $this->file->setTitle("{$googleFile->name}.{$googleFile->extension}");
        $this->file->setShareable(true);
        $this->file->setCopyable(true);
        $this->file->setDescription($googleFile->description);
        $this->file->setMimeType($googleFile->mimeType);
        $this->file->setParents([$this->createParent($googleFile->folder->id)]);
        return $this->service->files->insert($this->file,
                ['data' => file_get_contents($googleFile->filename),
                'mimeType' => $this->mimeType,
                'uploadType' => 'media',
                'visibility' => 'DEFAULT']);
    }

    protected function createParent($folderId)
    {
        $newParent = new Google_Service_Drive_ParentReference();
        $newParent->setId($folderId);
        return $newParent;
    }

    protected function createPermissionForFile($id, $onlyLink = false)
    {
        $permission = new Google_Service_Drive_Permission();
        $permission->setValue('');
        $permission->setType('anyone');
        $permission->setRole('reader');
        $permission->setWithLink($onlyLink);
        return $this->service->permissions->insert($id, $permission);
    }

    private function setService(Google_Client $client)
    {
        $this->service = new Google_Service_Drive($client);
    }

    /**
     * Returns an authorized API client.
     * @return Google_Client the authorized client object
     */
    private function setClient()
    {
        $client          = new Google_Client();
        $client->setApplicationName(self::APPLICATION_NAME);
        $client->setScopes(self::SCOPES);
        $client->setAuthConfigFile(self::CLIENT_SECRET_PATH);
        $client->setAccessType('offline');
        // Load previously authorized credentials from a file.
        $credentialsPath = self::CREDENTIALS_PATH;
        if (file_exists($credentialsPath)) {
            $accessToken = file_get_contents($credentialsPath);
        } else {
            throw new Exception('You need to create access token by cli');
        }
        $client->setAccessToken($accessToken);

        // Refresh the token if it's expired.
        if ($client->isAccessTokenExpired()) {
            $client->refreshToken($client->getRefreshToken());
            file_put_contents($credentialsPath, $client->getAccessToken());
        }
        $this->client = $client;
    }
}