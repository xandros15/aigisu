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
    /** @var array */
    private $tagNames;

    public static function getRarities()
    {
        return ['black', 'sapphire', 'platinum', 'gold', 'silver', 'bronze', 'iron'];
    }

    public static function arrayToTags(array $tags)
    {
        $tags = array_map(function ($item) {
            if ($item instanceof \stdClass) {
                $item = $item->name;
            } elseif (is_array($item)) {
                $item = isset($item['name']) ? $item['name'] : reset($item);
            }

            return str_replace('_', ' ', $item);
        }, $tags);

        return implode(', ', $tags);
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

    public function save(array $options = [])
    {
        parent::save($options);
        $this->syncTags($this->tagNames);
    }

    private function syncTags($tagsNames)
    {
        if ($tagsNames !== null) {
            if ($tagsNames = array_filter($tagsNames)) {
                $oldTags = Tag::whereIn('name', $tagsNames)->get();
                $newTags = Tag::createManyByName(array_diff($tagsNames, $oldTags->pluck('name')->toArray()));
                $tagsIds = array_merge($newTags->pluck('id')->toArray(), $oldTags->pluck('id')->toArray());
            } else {
                $tagsIds = [];
            }

            $this->tags()->sync($tagsIds);
        }
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, null, 'unit_id', 'tag_id');
    }

    public function setTagsAttribute($tagNames)
    {
        $this->tagNames = !is_array($tagNames) ? self::tagsToArray($tagNames) : $tagNames;
    }

    public static function tagsToArray(string $tagsString)
    {
        $parsedTags = [];
        $tags = explode(',', $tagsString);
        foreach ($tags as $tag) {
            $tag = trim($tag);
            $tag = strtolower($tag);
            $tag = str_replace(' ', '_', $tag);
            if ($tag) {
                $parsedTags[] = $tag;
            }
        }
        return array_unique($parsedTags);
    }
}