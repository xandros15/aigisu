<?php

namespace Aigisu\Api\Models;


use Aigisu\Core\Model;

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
//    public $hidden = ['pivot'];
    protected $fillable = [
        'name'
    ];
    protected $guarded  = [];

    public function units()
    {
        return $this->belongsToMany(Unit::class, null, 'tag_id', 'unit_id');
    }

    public function createOfFindByName($name)
    {
        return self::firstOrNew(['name' => $name]);
    }
}