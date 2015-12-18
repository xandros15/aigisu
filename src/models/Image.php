<?php

namespace models;

use RedBeanPHP\OODBBean;
use RedBeanPHP\Facade as R;
use Exception;
use stdClass;
use Main;

class Image
{
    const IMAGE_DIRECTORY = 'images';
    const SERVER_NUTAKU   = 'nutaku';
    const SERVER_DMM      = 'dmm';
    const IMAGE_ALL       = 1;
    const IMAGE_AVAIABLE  = 2;
    const IMAGE_REQIRED   = 3;
    const IMAGE_LOCKED    = 4;

    /** @var int */
    public $unitId = 0;

    /** @var bool */
    public $isOnlyDmm = true;

    /** @var bool */
    public $isMale = false;

    /** @var string */
    public $rarity = 'bronze';

    /** @var int */
    public $maxImages = 4;

    /** @var int */
    public $length = 0;

    /** @var array */
    public $images = [];

    public static function tableName()
    {
        return 'images';
    }

    public static function getColumnNames()
    {
        return ['id', 'md5', 'google', 'units_id', 'server', 'scene', 'imgur', 'delhash'];
    }

    public static function imageSceneToHuman($nrOfScene)
    {
        $scenes = [
            1 => 'first scene',
            2 => 'second scene'
        ];
        return (isset($scenes[$nrOfScene])) ? $scenes[$nrOfScene] : '';
    }

    public static function getServers()
    {
        return [
            self::SERVER_DMM => 2,
            self::SERVER_NUTAKU => 2
        ];
    }

    public static function createImagelink($id)
    {
        return Main::$app->web->siteUrl . self::IMAGE_DIRECTORY . DIRECTORY_SEPARATOR . $id . '.png';
    }

    public static function imagesByUnit($unitId, Unit $modelUnits = null)
    {
        if (!$modelUnits) {
            $modelUnits = Unit::loadOne($unitId);
        }
        if (!$modelUnits->getUnits()) {
            return false;
        }
        return new Image($modelUnits, $unitId);
    }

    public function __construct(Unit $modelUnits, $unitId)
    {
        $unit = $modelUnits->getUnitById($unitId);
        if (!$unit) {
            throw new Exception('No unit found');
        }
        $this->setIsOnlyDMM($unit->isOnlyDMM);
        $this->setIsMale($unit->isMale);
        $this->setRarity($unit->rarity);
        $this->setUnitId($unit->id);

        $list = $modelUnits->getUnitImages($unit->id);
        $this->createImageList($list);
    }

    public function isAnyImagesUploaded()
    {
        return ($this->length);
    }

    public function isCompletedUpload()
    {
        return ($this->maxImages == $this->length);
    }

    public function isRequired()
    {
        return ($this->length != $this->maxImages);
    }

    public function getAllImages($mode = self::IMAGE_ALL)
    {
        return ($mode == self::IMAGE_ALL) ? $this->images :
            array_filter($this->images,
                function($imageElement) use ($mode) {
                return ($imageElement->mode == $mode);
            });
    }

    public function getSortedImages()
    {
        $sortedImages = [];
        foreach ($this->images as $image) {
            if ($image->mode != self::IMAGE_AVAIABLE) {
                continue;
            }
            $sortedImages[$image->server][$image->scene] = $image;
        }
        return $sortedImages;
    }

    protected function setIsOnlyDMM($isOnlyDMM)
    {
        if ($isOnlyDMM) {
            $this->maxImages = self::getServers()[self::SERVER_DMM];
        } else {
            $this->maxImages = array_sum(self::getServers());
        }
        $this->isOnlyDmm = (bool) $isOnlyDMM;
    }

    protected function setIsMale($isMale)
    {
        if ($isMale) {
            $this->maxImages = 0;
        }
        $this->isMale = (bool) $isMale;
    }

    protected function setUnitId($unitId)
    {
        $this->unitId = $unitId;
    }

    protected function setRarity($rarity)
    {
        $this->rarity = $rarity;
    }

    private function createImageList(array $ownImagesList)
    {
        $imagesList = [];
        $servers    = self::getServers();
        foreach ($servers as $serverName => $maxImages) {
            for ($scene = 1; $scene <= $maxImages; $scene++) {
                $imagesList[] = $this->createOrLoad($ownImagesList, $serverName, $scene);
            }
        }

        $this->images = $imagesList;
    }

    private function createOrLoad($ownImagesList, $serverName, $scene)
    {
        $resultOfSearch = null;
        foreach ($ownImagesList as $image) {
            if ($image->server == $serverName && $image->scene == $scene) {
                $resultOfSearch = $image;
                break;
            }
        }
        if ($resultOfSearch) {
            $resultOfSearch->mode = self::IMAGE_AVAIABLE;
            $this->length++;
            return $resultOfSearch;
        }
        $newBean           = new stdClass();
        $newBean->server   = $serverName;
        $newBean->scene    = $scene;
        $newBean->units_id = $this->unitId;
        $newBean->mode     = self::IMAGE_REQIRED;
        if ($this->isMale || in_array($this->rarity, ['iron', 'bronze'])) {
            $newBean->mode = self::IMAGE_LOCKED;
        }
        if ($this->isOnlyDmm && $serverName != self::SERVER_DMM) {
            $newBean->mode = self::IMAGE_LOCKED;
        }
        return $newBean;
    }
}