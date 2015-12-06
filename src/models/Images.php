<?php

namespace models;

use RedBeanPHP\OODBBean;
use RedBeanPHP\Facade as R;

class Images
{
    const IMAGE_DIRECTORY = 'images';
    const SERVER_NUTAKU   = 'nutaku';
    const SERVER_DMM      = 'dmm';

    public $unitId;
    public $isOnlyDmm;
    public $isMale;
    public $rarity;

    /** @var OODBBean */
    public $dmm1;

    /** @var OODBBean */
    public $dmm2;

    /** @var OODBBean */
    public $nutaku1;

    /** @var OODBBean */
    public $nutaku2;

    public static function load(OODBBean $unit)
    {
        return new Images($unit);
    }

    public static function tableName()
    {
        return 'images';
    }

    public static function getColumnNames()
    {
        return ['id', 'md5', 'google', 'units_id', 'type', 'imgur', 'delhash'];
    }

    public static function imageNumberToHuman($string)
    {
        return str_replace(['#1', '#2'], ['first scene', 'second scene'], $string);
    }

    public static function getImageLabels()
    {
        return [
            'dmm1' => 'DMM #1',
            'dmm2' => 'DMM #2',
            'nutaku1' => 'Nutaku #1',
            'nutaku2' => 'Nutaku #2'
        ];
    }

    public static function getTypeNames()
    {
        return [
            'dmm1',
            'dmm2',
            'nutaku1',
            'nutaku2'
        ];
    }

    public function __construct(OODBBean $unit)
    {
        foreach ($unit->ownImagesList as $image) {
            if (property_exists($this, $image->type)) {
                $this->{$image->type} = $image;
            }
        }
        $this->setIsMale($unit->isMale);
        $this->setIsOnlyDMM($unit->isOnlyDMM);
        $this->setRarity($unit->rarity);
        $this->setUnitId($unit->id);
    }

    public function setIsOnlyDMM($isOnlyDMM)
    {
        if ($isOnlyDMM) {
            $this->nutaku1 = $this->nutaku2 = null;
        }
        $this->isOnlyDmm = (bool) $isOnlyDMM;
    }

    public function setIsMale($isMale)
    {
        if ($isMale) {
            $this->dmm1    = $this->dmm2    = $this->nutaku1 = $this->nutaku2 = null;
        }
        $this->isMale = (bool) $isMale;
    }

    public function setUnitId($unitId)
    {
        $this->unitId = $unitId;
    }

    public function setRarity($rarity)
    {
        $this->rarity = $rarity;
    }

    public function isImage($type)
    {
        return isset($this->{$type});
    }

    public function isAnyImagesUploaded()
    {
        return ($this->dmm1 || $this->dmm2 || $this->nutaku1 || $this->nutaku2);
    }

    public function isDisabledUpload($type = false)
    {
        if ($type) {
            $response = (in_array($this->rarity, ['iron', 'bronze']) || $this->isMale ||
                ($this->isOnlyDmm && trim($type, '0...9') == self::SERVER_NUTAKU));
        } else {
            $response = (in_array($this->rarity, ['iron', 'bronze']) || $this->isMale);
        }
        return $response;
    }

    public function isCompletedUpload($type = false)
    {
        if ($type) {
            $response = ($this->isImage($type));
        } else {
            $response = true;
            foreach (self::getTypeNames() as $type) {
                if ($this->isOnlyDmm && trim($type, '0...9') == self::SERVER_NUTAKU) {
                    continue;
                }
                if (!($this->{$type})) {
                    $response = false;
                    break;
                }
            }
        }
        return $response;
    }

    public function getImageLink($type)
    {
        return ($this->isImage($type)) ? SITE_URL . 'images/' . $this->{$type}->id . '.png' : '';
    }

    public function getAllImages()
    {
        $images = [];
        $types  = self::getTypeNames();

        foreach ($types as $type) {
            if ($this->isImage($type)) {
                $images[trim($type, '0...9')][] = $this->{$type};
            }
        }

        return $images;
    }

    public static function setImagesFromUnitId($id)
    {
        $unit = R::load(TB_NAME, (int) $id);
        if ($unit->isEmpty()) {
            return [];
        }
        $images = self::load($unit);
        return $images;
    }
}