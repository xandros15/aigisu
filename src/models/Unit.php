<?php

namespace models;

use traits\Validator;
use Illuminate\Database\Eloquent\Model;
use models\Image;

/**
 * Class Unit
 */

/**
 * @property string $name
 * @property string $original
 * @property string $icon
 * @property string $link
 * @property string $linkgc
 * @property string $rarity
 * @property bool $isMale
 * @property bool $isOnlyDmm
 * @property Image[] $images
 */
class Unit extends Model
{

    use Validator;
    public $timestamps  = false;
    protected $table    = 'unit';
    protected $fillable = [
        'name',
        'original',
        'icon',
        'link',
        'linkgc',
        'rarity',
        'is_male',
        'is_only_dmm'
    ];
    protected $guarded  = [];

    public static function getColumns()
    {
        return ['id', 'name', 'original', 'icon', 'link', 'linkgc', 'rarity', 'is_male', 'is_only_dmm'];
    }

    public function rules()
    {

        return [
            'name' => ['required', 'alpha_dash'],
            'original' => ['required', 'string'],
            'icon' => ['required', 'url'],
            'link' => ['url'],
            'linkgc' => ['required', 'url'],
            'rarity' => ['required', 'in:' . implode(',', self::getRarities())],
            'is_male' => ['required', 'bool'],
            'is_only_dmm' => ['required', 'bool']
        ];
    }

    public static function tableName()
    {
        return 'unit';
    }

    public static function getRarities()
    {
        return ['black', 'sapphire', 'platinum', 'gold', 'silver', 'bronze', 'iron'];
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }
}