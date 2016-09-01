<?php

namespace Aigisu\Api\Models;


use Aigisu\Core\Model;
use Illuminate\Database\Eloquent\Collection;
use Slim\Http\UploadedFile;

/**
 * Class Unit
 */

/**
 * @property string $name
 * @property string $original
 * @property string $link
 * @property string $linkgc
 * @property string $rarity
 * @property bool $is_male
 * @property bool $is_only_dmm
 * @property string $icon_name
 * @property bool $has_aw_image
 * @property Collection $images
 * @property int $id
 * @property Collection $tags
 */
class Unit extends Model
{

    const SEARCH_PARAM = 'q';
    const UNITS_PER_PAGE = 10;
    /** @var  UploadedFile | null */
    public $icon;
    public $tagNames;
    protected $fillable = [
        'name',
        'original',
        'link',
        'linkgc',
        'rarity',
        'is_male',
        'is_only_dmm',
        'icon_name',
        'has_aw_image',
        'tags'
    ];

    public static function getRarities()
    {
        return ['black', 'sapphire', 'platinum', 'gold', 'silver', 'bronze', 'iron'];
    }

    public function images()
    {
        return $this->hasMany(Image::class, 'unit_id', 'id');
    }

    /**
     * @param UploadedFile $icon
     */
    public function attachIcon(UploadedFile $icon)
    {
        $this->icon = $icon;
        $this->setAttribute('icon_name', md5_file($icon->file));
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, null, 'unit_id', 'tag_id');
    }

    public function setTagsAttribute($tagNames)
    {
        $this->tagNames = $tagNames;
    }
}