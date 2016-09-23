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
            $tagsNames = $this->parseTags($this->tags);
            if ($tagsNames) {
                $oldTags = Tag::whereIn('name', $tagsNames)->get();
                $newTags = Tag::createManyByName(array_diff($tagsNames, $oldTags->pluck('name')->toArray()));
                $tagsIds = array_merge($newTags->pluck('id')->toArray(), $oldTags->pluck('id')->toArray());
            } else {
                $tagsIds = [];
            }

            $this->relationModel->sync($tagsIds);
        }
    }

    /**
     * @param $tags
     * @return array
     */
    private function parseTags($tags) : array
    {
        if (is_string($tags)) {
            $tags = $this->tagsToArray($tags);
        } elseif (!is_array($tags)) {
            $tags = [];
        }

        return array_filter($tags);
    }

    /**
     * @param string $tagsString
     * @return array
     */
    private function tagsToArray(string $tagsString) : array
    {
        $tags = explode(',', $tagsString);
        $tags = array_map(function ($tag) {
            $tag = trim($tag);
            $tag = strtolower($tag);
            $tag = str_replace(' ', '_', $tag);
            return $tag;
        }, $tags);
        $tags = array_unique($tags);

        return $tags;
    }
}