<?php

namespace Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Traits\Validator;

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
 * @property bool $is_male
 * @property bool $is_only_dmm
 * @property bool $has_aw_image
 * @property Collection $images
 * @property int $id
 * @property Collection $tags
 */
class Unit extends Model
{

    use Validator;
    const SEARCH_PARAM = 'q';
    const UNITS_PER_PAGE = 10;
    public $timestamps = false;
    protected $table = 'unit';

    protected $fillable = [
        'name',
        'original',
        'icon',
        'link',
        'linkgc',
        'rarity',
        'is_male',
        'is_only_dmm',
        'has_aw_image'
    ];
    protected $guarded = [];

    public static function getColumns()
    {
        return ['id', 'name', 'original', 'icon', 'link', 'linkgc', 'rarity', 'is_male', 'is_only_dmm', 'has_aw_image'];
    }

    public function rules()
    {

        return [
            'name' => ['required', 'alpha_dash'],
            'original' => ['required', 'string', 'unique:unit,original,' . $this->id],
            'icon' => ['required', 'url'],
            'link' => ['url'],
            'linkgc' => ['required', 'url'],
            'rarity' => ['required_with:' . implode(',', self::getRarities())],
            'is_male' => ['required', 'bool'],
            'is_only_dmm' => ['required', 'bool'],
            'has_aw_image' => ['required', 'bool']
        ];
    }

    public static function getRarities()
    {
        return ['black', 'sapphire', 'platinum', 'gold', 'silver', 'bronze', 'iron'];
    }

    public static function tableName()
    {
        return 'unit';
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function isImagesRequired()
    {
        return $this->images->count() != $this->getTotalImageRequired();
    }

    public function getTotalImageRequired()
    {
        $total = 0;
        if ($this->is_male) {
            return $total;
        }
        if ($this->is_only_dmm) {
            $total = Image::IMAGE_PER_SERVER;
        } else {
            $total = Image::IMAGE_PER_SERVER * count(Image::getImageSchemeArray());
        }
        if ($this->has_aw_image) {
            $total++;
        }
        return $total;
    }

    public function isImageRequired($server, $scene)
    {
        return !(($this->is_only_dmm && $server != Image::SERVER_DMM)
            || ($scene == Image::IMAGE_SPECIAL_SCENE && !$this->has_aw_image)
            || $this->isImageExist($server, $scene)
        );

    }

    public function isImageExist($server, $scene)
    {
        return $this->images->where('server', $server)->contains('scene', $scene);

    }

    public function isAnyImages()
    {
        return !$this->images->isEmpty();
    }

    public function getTagsString()
    {
        return $this->tags->implode('name', ', ');
    }

    public function addTagsToUnit($tagsString)
    {
        $tagsArray = $this->parseTags($tagsString);

        $tags = [];

        foreach ($tagsArray as $tagName) {
            $tags[] = Tag::firstOrCreate(['name' => $tagName])->id;
        }

        $this->tags()->sync($tags);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    private function parseTags($tagsString)
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