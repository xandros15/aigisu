<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-30
 * Time: 21:05
 */

namespace Aigisu\components\google;

use Google_Service_Drive as GoogleDrive;
use Google_Service_Drive_DriveFile as GoogleDriveFile;
use Google_Service_Drive_Permission as GoogleDrivePermission;

class GoogleDriveManager extends Configurable
{

    const FOLDER_MIME_TYPE = 'application/vnd.google-apps.folder';
    /** @var GoogleDrive */
    protected $service;
    private $drive;

    public function __construct(GoogleClient $googleClient, $config = [])
    {
        $this->drive = new GoogleDrive($googleClient);
        parent::__construct($config);
    }

    public function getDrive() : GoogleDrive
    {
        return $this->drive;
    }

    public function create(array $params = [])
    {
        $file = new GoogleDriveFile($params);
        $optParams = [];

        if (isset($params['filename'])) {
            $optParams = array_merge($optParams, $this->prepareMediaFile($params['filename']));
            unset($params['filename']);
        }

        if ($rootId = $this->config->get('rootId')) {
            $file->setParents([$rootId]);
        }

        return $this->drive->files->create($file, $optParams);
    }

    /**
     * @param string $filename
     * @return array
     */
    protected function prepareMediaFile(string $filename) : array
    {
        $mediaFile = [];
        if ($filename) {
            if (!file_exists($filename)) {
                throw new \InvalidArgumentException("File `$filename` doesn't exist");
            }

            $mediaFile = [
                'uploadType' => 'media',
                'data' => file_get_contents($filename),
                'mimeType' => (new \finfo())->file($filename, FILEINFO_MIME_TYPE),
            ];
        }

        return $mediaFile;
    }

    public function update($id, array $params = [])
    {

        $file = new GoogleDriveFile($params);
        $optParams = [];

        if (isset($params['filename'])) {
            $optParams = array_merge($optParams, $this->prepareMediaFile($params['filename']));
            unset($params['filename']);
        }

        return $this->drive->files->update($id, $file, $optParams);
    }

    public function delete($id)
    {
        return $this->drive->files->delete($id);
    }

    /**
     * empty trash
     */
    public function nuke()
    {
        return $this->drive->files->emptyTrash();
    }

    public function get($id, array $fields = [])
    {
        if ($fields) {
            $fields = implode(',', $fields);
        }

        return $this->drive->files->get($id, $fields ? ['fields' => $fields] : []);
    }

    public function anyoneWithLinkCan(GoogleDriveFile $file, string $can = 'view')
    {
        $permission = new GoogleDrivePermission([
            'type' => 'anyone',
            'role' => $this->getRole($can),
            'allowFileDiscovery' => false,
        ]);

        return $this->drive->permissions->create($file->getId(), $permission);
    }

    protected function getRole(string $can)
    {
        $roles = [
            'edit' => 'writer',
            'comment' => 'commenter',
            'view' => 'reader'
        ];

        if (!isset($roles[$can])) {
            throw new \InvalidArgumentException('Wrong accessible name. Available: ' . implode(', ',
                    array_keys($roles)));
        }

        return $roles[$can];
    }
}

