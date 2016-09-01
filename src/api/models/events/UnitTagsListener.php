<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-01
 * Time: 21:44
 */

namespace Aigisu\Api\Models\Events;


use Aigisu\Api\Models\Tag;
use Aigisu\Api\Models\Unit;
use Aigisu\Core\Model;
use InvalidArgumentException;

class UnitTagsListener implements Event
{

    /**
     * @param Model $model
     * @return void
     */
    public function __invoke(Model $model)
    {
        if ($model instanceof Unit) {
            $this->syncTags($model);
        }
    }

    /**
     * @param Unit $unit
     * @throws InvalidArgumentException
     */
    private function syncTags(Unit $unit)
    {
        if ($unit->tagNames !== null) {
            $tagsNames = $this->getTags($unit);
            if ($tagsNames) {
                $oldTags = Tag::whereIn('name', $tagsNames)->get();
                $newTags = Tag::createManyByName(array_diff($tagsNames, $oldTags->pluck('name')->toArray()));
                $tagsIds = array_merge($newTags->pluck('id')->toArray(), $oldTags->pluck('id')->toArray());
            } else {
                $tagsIds = [];
            }

            $unit->tags()->sync($tagsIds);
        }
    }

    private function getTags(Unit $unit) : array
    {
        if (is_string($unit->tagNames)) {
            $tags = $this->tagsToArray($unit->tagNames);
        } elseif (is_array($unit->tagNames)) {
            $tags = $unit->tagNames;
        } else {
            $tags = [];
        }

        return array_filter($tags);
    }

    /**
     * @param string $tagsString
     * @return array
     */
    private function tagsToArray(string $tagsString)
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