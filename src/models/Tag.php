<?php

namespace Models;

use Aigisu\Model;

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
    public $timestamps  = false;
//    public $hidden = ['pivot'];
    protected $table    = 'tag';
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