<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-01
 * Time: 21:44
 */

namespace Aigisu\Api\Models\Handlers;


use Aigisu\Api\Models\Unit;
use Aigisu\Api\Models\Unit\Tag;

class UnitTagsHandler
{

    /** @var array|null */
    private $tags;

    /** @var \Illuminate\Database\Eloquent\Relations\BelongsToMany */
    private $relationModel;

    /**
     * UnitTagsHandler constructor.
     * @param Unit $unit
     */
    public function __construct(Unit $unit)
    {
        $this->tags = $unit->tagNames;
        $this->relationModel = $unit->tags();
    }

    public function syncTags()
    {
        if ($this->tags !== null) {
            if ($this->tags) {
                $oldTags = Tag::whereIn('name', $this->tags)->get();
                $newTags = Tag::createManyByName(array_diff($this->tags, $oldTags->pluck('name')->toArray()));
                $tagsIds = array_merge($newTags->pluck('id')->toArray(), $oldTags->pluck('id')->toArray());
            } else {
                $tagsIds = [];
            }

            $this->relationModel->sync($tagsIds);
        }
    }
}