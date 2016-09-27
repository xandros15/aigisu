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
 * @property string $gender
 * @property bool $dmm
 * @property bool $nutaku
 * @property bool $special_cg
 * @property string $icon
 * @property Collection $cg
 * @property int $id
 * @property Collection $tags
 */
class Unit extends Model
{
    const SEARCH_PARAM = 'q';
    const UNITS_PER_PAGE = 10;
    const GENDER_FEMALE = 'female';
    const GENDER_MALE = 'male';


    /** @var string|array */
    public $tagNames;

    /** @var array */
    protected $fillable = [
        'name',
        'kanji',
        'link_seesaw',
        'link_gc',
        'rarity',
        'gender',
        'dmm',
        'nutaku',
        'icon',
        'special_cg',
        'tags'
    ];

    /** @var array */
    protected $hidden = [
        'link_gc',
        'link_seesaw',
    ];

    /** @var array */
    protected $casts = [
        'dmm' => 'bool',
        'nutaku' => 'bool',
        'special_cg' => 'bool',
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
     * @return array
     */
    public static function getGenders() : array
    {
        return [self::GENDER_FEMALE, self::GENDER_MALE];
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
        if ($this->gender === self::GENDER_FEMALE) {
            $missing->attachCGCollection($this->cg);
            $missing->filterArchival();

            if ($this->dmm) {
                $missing->applyDmm();
            }

            if ($this->special_cg) {
                $missing->applySpecialDmm();
            }

            if ($this->nutaku) {
                $missing->applyNutaku();
            }
        }

        return $missing->toArray();
    }
}