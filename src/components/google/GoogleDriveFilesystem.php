<?php

namespace Aigisu\Components\Google;

use Aigisu\Components\Configure\Configurable;

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
        parent::__construct($config);
        $this->clientManager = new GoogleClientManager($this->config['client']);
        $this->driveManager = new GoogleDriveManager($this->clientManager->getClient(), $this->config['drive']);
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