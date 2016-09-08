<?php

namespace Aigisu\Api\Models;

use Aigisu\Core\Model;

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
    /** @var array */
    protected $fillable = [
        'md5',
        'unit_id',
        'server',
        'scene',
        'google_id',
        'imgur_id',
        'imgur_delhash',
    ];
    /** @var array */
    protected $hidden = [
        'unit_id',
        'google_id',
        'imgur_id',
        'imgur_delhash',
    ];
    /** @var array */
    protected $appends = [
        'google',
        'imgur',
        'local',
    ];

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

    public static function getServersNames()
    {
        return [self::SERVER_DMM, self::SERVER_NUTAKU];
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function getGoogleAttribute()
    {
        return sprintf('https://drive.google.com/uc?export=view&id=%s', $this->google_id);
    }

    public function getImgurAttribute()
    {
        return sprintf('http://i.imgur.com/%s.png', $this->imgur_id);
    }

    public function getLocalAttribute()
    {
        return $this->id; //todo url for local image
    }
}