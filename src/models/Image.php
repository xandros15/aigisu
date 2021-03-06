<?php

namespace models;

use Illuminate\Database\Eloquent\Model;
use Main;
use traits\Validator;

/**
 * Class Image
 */

/**
 * @property string $md5
 * @property string $server
 * @property int $scene
 * @property string $google
 * @property string $imgur
 * @property string $delhash
 * @property Unit $unit
 */
class Image extends Model
{

    use Validator;
    const IMAGE_PER_SERVER = 2;
    const IMAGE_SPECIAL_SCENE = 3;
    const IMAGE_DIRECTORY = 'images';
    const SERVER_NUTAKU = 'nutaku';
    const SERVER_DMM = 'dmm';
    public $timestamps  = false;
    protected $table = 'image';
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

    public static function boot()
    {
        parent::boot();
        Main::$app->connection->validator->extend('imageExists',
            function($attribute, $value, $parameters, $validator) {

            $validator->setCustomMessages([$attribute => 'Image already exists']);

            list($scene, $server, $id) = $parameters;

            $image = Image::where([$attribute => $value, 'scene' => $scene, 'server' => $server]);
            if ($id) {
                $image = $image->where('id', '!=', $id);
            }

            return $image->count() === 0;
        });
    }

    public static function tableName()
    {
        return 'image';
    }

    public static function getImageSchemeArray(){
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
        $imageSet = self::where('unit_id', $id)->get();

        return $imageSet->sortBy('scene')->groupBy('server');
    }

    public function rules()
    {
        return [
            'md5' => ['required', 'size:32'],
            'server' => ['required', 'in:' . implode(',', self::getServersNames())],
            'scene' => ['required', 'integer', 'in:1,2,3'],
            'unit_id' => ['required', 'exists:unit,id', 'imageExists:' . implode(',',
                    [$this->scene, $this->server, $this->id])],
            'google' => ['string'],
            'imgur' => ['string'],
            'delhash' => ['string']
        ];
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
        /**
         * http://i.imgur.com/{fieldId}.png
         * https://drive.google.com/uc?export=view&id={fileId}
         */
        //return sprintf('%s/%s.png', 'http://i.imgur.com/', $this->imgur);
        return sprintf('https://drive.google.com/uc?export=view&id=%s', $this->google);
    }
}