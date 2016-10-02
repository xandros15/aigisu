<?php

namespace Aigisu\Components\Google;

use Google_Service_Drive as GoogleDrive;

class GoogleDriveFilesystem extends Configurable
{

    /** @var GoogleClient */
    private $clientManager;

    /** @var GoogleDriveManager */
    private $driveManager;

    /**
     * GoogleDriveFilesystem constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->setConfig($config);
        $this->clientManager = new GoogleClientManager($this->config['client']);
        $this->driveManager = new GoogleDriveManager($this->clientManager->getClient(), $this->config['drive']);
    }

    /**
     * @param array $config
     * @throws \InvalidArgumentException
     */
    public function setConfig($config = [])
    {
        parent::setConfig($config);
        $this->config->setFallback(new Config(static::getDefaultConfig()));
    }

    /**
     * @return array
     */
    public static function getDefaultConfig() : array
    {
        return [
            'client' => [
                'application_name' => 'my app',
                'access-type' => 'offline',
                'redirect_uri' => 'http://localhost',
                'scopes' => [
                    GoogleDrive::DRIVE_METADATA
                ],
            ]
        ];
    }

    /**
     * @return GoogleClientManager
     */
    public function getClientManager() : GoogleClientManager
    {
        return $this->clientManager;
    }

    /**
     * @return GoogleDriveManager
     */
    public function getDriveManager() : GoogleDriveManager
    {
        return $this->driveManager;
    }
}