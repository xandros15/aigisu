<?php

namespace Aigisu\Models\Unit;


use Aigisu\Core\Model;
use Aigisu\Models\Unit;
use Illuminate\Database\Eloquent\Collection;

/**
 * @property int $id
 * @property string $name
 * @property Unit[] $units
 */
class Tag extends Model
{
    /** @var array */
    protected $fillable = [
        'name'
    ];

    /**
     * @param array $names
     * @return Collection
     */
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function units()
    {
        return $this->belongsToMany(Unit::class, null, 'tag_id', 'unit_id');
    }
}
