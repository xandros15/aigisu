<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-30
 * Time: 21:05
 */

namespace Aigisu\Components\Google;

use Google_Service_Drive as GoogleDrive;
use Google_Service_Drive_DriveFile as GoogleDriveFile;
use Google_Service_Drive_Permission as GoogleDrivePermission;

class GoogleDriveManager
{

    const FOLDER_MIME_TYPE = 'application/vnd.google-apps.folder';

    /** @var $rootId */
    private $rootId;
    /** @var GoogleDrive */
    private $drive;
    /** @var GoogleClientManager */
    private $clientManager;

    /**
     * GoogleDriveManager constructor.
     *
     * @param GoogleClientManager $googleClientManager
     * @param string $rootId
     */
    public function __construct(GoogleClientManager $googleClientManager, string $rootId = '')
    {
        $this->clientManager = $googleClientManager;
        $this->rootId        = $rootId;
        $this->drive         = new GoogleDrive($this->clientManager->getClient());
    }

    /**
     * @return GoogleClientManager
     */
    public function getClientManager(): GoogleClientManager
    {
        return $this->clientManager;
    }

    /**
     * @return GoogleDrive
     */
    public function getDrive(): GoogleDrive
    {
        return $this->drive;
    }

    /**
     * @param array $params
     *
     * @return GoogleDriveFile
     */
    public function create(array $params = [])
    {
        $file      = new GoogleDriveFile($params);
        $optParams = [];

        if (isset($params['filename'])) {
            $optParams = array_merge($optParams, $this->prepareMediaFile($params['filename']));
        }

        if ($this->rootId) {
            $file->setParents([$this->rootId]);
        }

        return $this->drive->files->create($file, $optParams);
    }

    /**
     * @param $id
     * @param array $params
     *
     * @return GoogleDriveFile
     */
    public function update($id, array $params = [])
    {

        $file      = new GoogleDriveFile($params);
        $optParams = [];

        if (isset($params['filename'])) {
            $optParams = array_merge($optParams, $this->prepareMediaFile($params['filename']));
            unset($params['filename']);
        }

        return $this->drive->files->update($id, $file, $optParams);
    }

    /**
     * @param $id
     */
    public function delete($id)
    {
        $this->drive->files->delete($id);
    }

    /**
     * empty trash
     */
    public function nuke()
    {
        return $this->drive->files->emptyTrash();
    }

    /**
     * @param $id
     * @param array $fields
     *
     * @return GoogleDriveFile
     */
    public function get($id, array $fields = [])
    {
        if ($fields) {
            $fields = implode(',', $fields);
        }

        return $this->drive->files->get($id, $fields ? ['fields' => $fields] : []);
    }

    /**
     * @param GoogleDriveFile $file
     * @param string $can
     *
     * @return GoogleDrivePermission
     */
    public function anyoneWithLinkCan(GoogleDriveFile $file, string $can = 'view')
    {
        $permission = new GoogleDrivePermission([
            'type'               => 'anyone',
            'role'               => $this->getRole($can),
            'allowFileDiscovery' => false,
        ]);

        return $this->drive->permissions->create($file->getId(), $permission);
    }

    /**
     * @param string $filename
     *
     * @return array
     */
    protected function prepareMediaFile(string $filename): array
    {
        if (!file_exists($filename)) {
            throw new \InvalidArgumentException("File `$filename` doesn't exist");
        }

        $mediaFile = [
            'uploadType' => 'media',
            'data'       => file_get_contents($filename),
            'mimeType'   => (new \finfo())->file($filename, FILEINFO_MIME_TYPE),
        ];


        return $mediaFile;
    }

    /**
     * @param string $can
     *
     * @return mixed
     */
    protected function getRole(string $can)
    {
        $roles = [
            'edit'    => 'writer',
            'comment' => 'commenter',
            'view'    => 'reader'
        ];

        if (!isset($roles[$can])) {
            throw new \InvalidArgumentException('Wrong accessible name. Available: ' .
                                                implode(', ', array_keys($roles))
            );
        }

        return $roles[$can];
    }
}

