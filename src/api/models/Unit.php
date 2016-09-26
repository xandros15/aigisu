<?php

namespace Aigisu\Api\Models;


use Aigisu\Api\Models\Handlers\UnitTagsHandler;
use Aigisu\Api\Models\Unit\CG;
use Aigisu\Api\Models\Unit\MissingCG;
use Aigisu\Api\Models\Unit\PredefinedTags;
use Aigisu\Api\Models\Unit\Tag;
use Aigisu\Core\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
 * @property Collection $cg
 * @property int $id
 * @property Collection $tags
 */
class Unit extends Model
{
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
        'links',
        'missingCG'
    ];

    /**
     * @return array
     */
    public static function getRarities() : array
    {
        return ['black', 'sapphire', 'platinum', 'gold', 'silver', 'bronze', 'iron'];
    }

    /**
     * @return HasMany
     */
    public function cg() : HasMany
    {
        return $this->hasMany(CG::class, 'unit_id', 'id');
    }

    /**
     * @return BelongsToMany
     */
    public function tags() : BelongsToMany
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

    /**
     * @return array
     */
    public function getLinksAttribute() : array
    {
        return [
            'seesaw' => $this->link_seesaw,
            'gc' => $this->link_gc
        ];
    }

    public function syncTags()
    {
        $handler = new UnitTagsHandler($this);
        $handler->syncTags();
    }

    /**
     * @return null|string
     */
    public function getIconAttribute()
    {
        $url = null;
        $icon = $this->attributes['icon'];
        if ($icon && $local = $this->urlTo('storage.images', ['path' => $icon])) {
            $url = $local;
        }

        return $url;
    }

    /**
     * @return array
     */
    public function getMissingCGAttribute() : array
    {
        $missing = new MissingCG();
        if ($this->tags->contains('name', PredefinedTags::IS_FEMALE)) {
            $missing->attachCGCollection($this->cg);
            $missing->filterArchival();

            if ($this->tags->contains('name', PredefinedTags::HAS_DMM_IMAGES)) {
                $missing->applyDmm();
            }

            if ($this->tags->contains('name', PredefinedTags::HAS_DMM_SPECIAL_IMAGES)) {
                $missing->applySpecialDmm();
            }

            if ($this->tags->contains('name', PredefinedTags::HAS_NUTAKU_IMAGES)) {
                $missing->applyNutaku();
            }
        }

        return $missing->toArray();
    }
}