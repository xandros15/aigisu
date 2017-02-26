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

    private $default = [
        'is_male' => false,
        'is_dmm' => false,
        'is_nutaku' => false,
        'is_special_cg' => false,
    ];

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
    public function __construct($collection)
    {
        /** @var $collection Collection */
        $collection = !$collection instanceof Collection ? new Collection($collection) : $collection;
        $collection = $collection->filter(function ($cg) {
            return !$cg['archival'];
        });
        $this->collection = $collection;
    }

    /**
     * @param $params
     * @return array
     */
    public function filter($params) : array
    {
        $params = array_merge($this->default, $params);
        $missing = [];
        if ($params['is_male']) {
            return $missing;
        }

        if ($params['is_dmm']) {
            $missing = array_merge($missing, $this->filterDMM());
        }

        if ($params['is_nutaku']) {
            $missing = array_merge($missing, $this->filterNutaku());
        }

        if ($params['is_special_cg']) {
            $missing = array_merge($missing, $this->filterSpecial());
        }


        return $missing;
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
