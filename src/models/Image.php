<?php

namespace models;

use Main;
use models\Unit;
use Illuminate\Database\Eloquent\Model;
use traits\Validator;

/**
 * Class Image
 */

/**
 * @property string $md5
 * @property string $server
 * @property int $scene
 * @property string $google
 * @property string $delhash
 * @property Unit $unit
 */
class Image extends Model
{

    use Validator;
    protected $table    = 'image';
    public $timestamps  = false;
    protected $fillable = [
        'md5',
        'unit_id',
        'server',
        'scene',
        'google',
        'imgur',
        'delhash'
    ];
    protected $guarded  = [];

    const IMAGE_PER_SERVER = 2;
    const IMAGE_DIRECTORY  = 'images';
    const SERVER_NUTAKU    = 'nutaku';
    const SERVER_DMM       = 'dmm';

    public static function tableName()
    {
        return 'image';
    }

    public function rules()
    {
        return [
            'md5' => ['required', 'size:32'],
            'unit_id' => ['required', 'exists:unit,id'],
            'server' => ['required', 'in:' . implode(',', self::getServersNames())],
            'scene' => ['required', 'digits_between:1,2'],
            'google' => ['string'],
            'imgur' => ['string'],
            'delhash' => ['string']
        ];
    }

    public static function imageSceneToHuman($nrOfScene)
    {
        $scenes = [
            1 => 'first scene',
            2 => 'second scene'
        ];
        return (isset($scenes[$nrOfScene])) ? $scenes[$nrOfScene] : '';
    }

    public static function getServersNames()
    {
        return [self::SERVER_DMM, self::SERVER_NUTAKU];
    }

    public function unit()
    {

        return $this->belongsTo(Unit::class);
    }

    public function getLink()
    {
        return sprintf('%s%s/%d.png', Main::$app->web->siteUrl, self::IMAGE_DIRECTORY, $this->id);
    }
}