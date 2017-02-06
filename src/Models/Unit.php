<?php

namespace Aigisu\Models;


use Aigisu\Components\Http\UploadedFile;
use Aigisu\Core\Model;
use Aigisu\Models\Handlers\UnitTagsHandler;
use Aigisu\Models\Unit\CG;
use Aigisu\Models\Unit\Tag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Slim\Http\Request;

/**
 * @property string $name
 * @property string $japanese_name
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
    const
        ICON_UPLOAD_FILE_NAME = 'icon',
        ICON_UPLOAD_CATALOG = 'icons';


    /** @var string|array */
    public $tagNames;

    /** @var array */
    protected $fillable = [
        'name',
        'japanese_name',
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
     * @param Request $request
     */
    public function saveUnitModel(Request $request)
    {
        $this->fill($request->getParams());
        $this->uploadIcon($request);
        $this->saveOrFail();
        $this->syncTags();
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function uploadIcon(Request $request)
    {
        /** @var $icon UploadedFile */
        $icon = $request->getUploadedFiles()['icon'] ?? null;
        if ($icon) {
            $this->setAttribute('icon', $icon->storeAsPublic(self::ICON_UPLOAD_CATALOG));
            return true;
        }

        return false;
    }

    public function syncTags()
    {
        $handler = new UnitTagsHandler($this);
        $handler->syncTags();
    }
}
