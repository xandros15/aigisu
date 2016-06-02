<?php

namespace models;

use traits\Validator;
use Illuminate\Database\Eloquent\Model;
use models\Image;
use models\Tag;

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
 * @property Image[] $images
 */
class Unit extends Model
{

    use Validator;
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

    public function getTotalImageRequired(){
        $total = 0;
        if($this->is_male){
            return $total;
        }
        if ($this->is_only_dmm) {
            $total = Image::IMAGE_PER_SERVER;
            if ($this->has_aw_image) {
                $total++;
            }
        } else {
            $total = Image::IMAGE_PER_SERVER * count(Image::getImageSchemeArray());
        }

        return $total;
    }

    public function isImagesRequired()
    {
        return $this->images->count() != $this->getTotalImageRequired();
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
        return $this->images->where('server',$server)->contains('scene',$scene);

    }

    public function isAnyImages()
    {
        return !$this->images->isEmpty();
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function getTagsString()
    {
        return $this->tags()->lists('name')->implode(', ');
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

    private function parseTags($tagsString)
    {
        $parsedTags = [];
        $tags = explode(',', $tagsString);
        foreach($tags as $tag){
            $tag = trim($tag);
            $tag = strtolower($tag);
            $tag = str_replace(' ', '_', $tag);
            if($tag){
                $parsedTags[] = $tag;
            }
        }
        return array_unique($parsedTags);
    }
}