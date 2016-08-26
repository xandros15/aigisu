<?php

namespace Aigisu\Api\Models;


use Aigisu\Core\Model;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class Tag
 */

/**
 * @property int $id
 * @property string $name
 * @property Unit[] $units
 */
class Tag extends Model
{
    protected $fillable = [
        'name'
    ];
    protected $guarded = [];

    public static function createManyByName(array $names) : Collection
    {
        $tags = new Collection();
        foreach ($names as $name) {
            $tag = new self(['name' => $name]);
            $tag->saveOrFail();
            $tags->add($tag);
        }

        return $tags;
    }

    public function units()
    {
        return $this->belongsToMany(Unit::class, null, 'tag_id', 'unit_id');
    }

    public function createOfFindByName($name)
    {
        return self::firstOrNew(['name' => $name]);
    }

}