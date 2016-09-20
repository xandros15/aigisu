<?php

namespace Aigisu\Api\Models;


use Aigisu\Api\Models\Unit\Tag;
use Aigisu\Components\Url\UrlModelHelper;
use Aigisu\Core\Model;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class Unit
 */

/**
 * @property string $name
 * @property string $kanji
 * @property string $link_seesaw
 * @property string $link_gc
 * @property string $rarity
 * @property bool $is_male
 * @property bool $is_only_dmm
 * @property string $icon
 * @property bool $has_aw_image
 * @property Collection $images
 * @property int $id
 * @property Collection $tags
 */
class Unit extends Model
{
    use UrlModelHelper;

    const SEARCH_PARAM = 'q';
    const UNITS_PER_PAGE = 10;

    /** @var string|array */
    public $tagNames;

    /** @var array */
    protected $fillable = [
        'name',
        'kanji',
        'link_seesaw',
        'link_gc',
        'rarity',
        'is_male',
        'is_only_dmm',
        'icon',
        'has_aw_image',
        'tags'
    ];

    /** @var array */
    protected $hidden = [
        'link_gc',
        'link_seesaw',
    ];

    /** @var array */
    protected $casts = [
        'is_male' => 'bool',
        'is_only_dmm' => 'bool',
        'has_aw_image' => 'bool',
    ];

    /** @var array */
    protected $appends = [
        'links'
    ];

    /**
     * @return array
     */
    public static function getRarities()
    {
        return ['black', 'sapphire', 'platinum', 'gold', 'silver', 'bronze', 'iron'];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany(Image::class, 'unit_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, null, 'unit_id', 'tag_id');
    }

    /**
     * @param string|array $tagNames
     */
    public function setTagsAttribute($tagNames)
    {
        $this->tagNames = $tagNames;
    }

    public function getLinksAttribute()
    {
        return [
            'seesaw' => $this->link_seesaw,
            'gc' => $this->link_gc
        ];
    }

    public function getIconAttribute()
    {
        $url = null;
        $icon = $this->attributes['icon'];
        if ($icon && $local = $this->urlTo('storage.images', ['path' => $icon])) {
            $url = $local;
        }

        return $url;
    }

}