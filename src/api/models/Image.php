<?php

namespace Aigisu\Api\Models;

use Aigisu\Core\Model;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class Image
 */

/**
 * @property string $md5
 * @property string $server
 * @property int $scene
 * @property string $google_id
 * @property string $imgur_id
 * @property string $imgur_delhash
 * @property Unit $unit
 * @property int $id
 */
class Image extends Model
{

    const IMAGE_PER_SERVER = 2;
    const IMAGE_SPECIAL_SCENE = 3;
    const IMAGE_DIRECTORY = 'images';
    const SERVER_NUTAKU = 'nutaku';
    const SERVER_DMM = 'dmm';
    protected $fillable = [
        'md5',
        'unit_id',
        'server',
        'scene',
        'google',
        'imgur',
        'delhash'
    ];
    protected $guarded = [];

    public static function getImageSchemeArray()
    {
        return [
            Image::SERVER_DMM => [1, 2, 3],
            Image::SERVER_NUTAKU => [1, 2]
        ];
    }

    public static function imageSceneToHuman($nrOfScene)
    {
        $scenes = [
            1 => 'first scene',
            2 => 'second scene',
            3 => 'special scene'
        ];
        return (isset($scenes[$nrOfScene])) ? $scenes[$nrOfScene] : '';
    }

    public static function getImageSetByUnitId($id)
    {
        /** @var $imageSet Collection */
        $imageSet = self::where('unit_id', $id)->get();
        return $imageSet->sortBy('scene')->groupBy('server');
    }

    public static function getServersNames()
    {
        return [self::SERVER_DMM, self::SERVER_NUTAKU];
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function getLink()
    {
        /**
         * http://i.imgur.com/{fieldId}.png
         * https://drive.google.com/uc?export=view&id={fileId}
         */
        return sprintf('%s/%s.png', 'http://i.imgur.com/', $this->imgur_id);
    }
}