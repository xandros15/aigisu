<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-21
 * Time: 16:10
 */

namespace Aigisu\Models\Unit;


use Aigisu\Models\Unit;
use Illuminate\Database\Eloquent\Collection;

class MissingCG
{
    /** @var Collection */
    private $collection;

    /** @var array */
    private $requiredMap = [
        'dmm' => [
            [
                'server' => CG::SERVER_DMM,
                'scene' => 1,
            ],
            [
                'server' => CG::SERVER_DMM,
                'scene' => 2,
            ],
        ],
        'special' => [
            [
                'server' => CG::SERVER_DMM,
                'scene' => 3,
            ],
        ],

        'nutaku' => [
            [
                'server' => CG::SERVER_NUTAKU,
                'scene' => 1,
            ],
            [
                'server' => CG::SERVER_NUTAKU,
                'scene' => 2,
            ],
        ],
    ];

    /**
     * MissingCG constructor.
     * @param array $collection
     */
    public function __construct(array $collection)
    {
        $this->collection = new Collection($collection);
    }

    public function filter(array $unit) : array
    {
        $missing = [];
        if ($unit['gender'] == Unit::GENDER_MALE) {
            return $missing;
        }
        $this->filterArchival();


        if ($unit['dmm']) {
            $missing = array_merge($missing, $this->filterDMM());
        }

        if ($unit['nutaku']) {
            $missing = array_merge($missing, $this->filterNutaku());
        }

        if ($unit['special_cg']) {
            $missing = array_merge($missing, $this->filterSpecial());
        }


        return $missing;
    }

    private function filterArchival() : void
    {
        $this->collection = $this->collection->filter(function ($cg) {
            return !$cg['archival'];
        });
    }

    /**
     * @return array
     */
    private function filterNutaku() : array
    {
        return $this->applyIfNotFound($this->requiredMap['nutaku']);
    }

    /**
     * @return array
     */
    private function filterDMM() : array
    {
        return $this->applyIfNotFound($this->requiredMap['dmm']);
    }

    /**
     * @return array
     */
    private function filterSpecial() : array
    {
        return $this->applyIfNotFound($this->requiredMap['special']);
    }

    /**
     * @param array $requiredMap
     * @return array
     */
    private function applyIfNotFound(array $requiredMap) : array
    {
        $missing = [];
        foreach ($requiredMap as $required) {
            $hasCG = $this->collection->contains(function ($cg) use ($required) {
                return $cg['scene'] == $required['scene'] && $cg['server'] == $required['server'];
            });
            if (!$hasCG) {
                $missing[] = $required;
            }
        }

        return $missing;
    }
}
