<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-03-20
 * Time: 23:56
 */

namespace Aigisu\Web\Components;


final class UnitManager
{
    private $query;

    private $defaults = [
        'filter-rarities' => '',
        'filter-name' => '',
        'filter-missing-cg' => '',
        'filter-server' => '',
        'sort-units' => '',
    ];

    /**
     * UnitManager constructor.
     *
     * @param array $query
     */
    public function __construct(array $query)
    {
        $this->query = array_merge($this->defaults, $query);
    }

    /**
     * @return array
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param array $units
     *
     * @return array
     */
    public function sort(array $units)
    {
        if (!empty($this->query['sort-units']) && $units && array_key_exists($this->query['sort-units'], $units[0])) {
            usort($units, function ($unitA, $unitB) {
                return $unitA[$this->query['sort-units']] <=> $unitB[$this->query['sort-units']];
            });
        }

        return $units;
    }

    /**
     * @param array $units
     *
     * @return array
     */
    public function filter(array $units)
    {
        return array_filter($units, function ($unit) {
            $valid = empty($this->query['filter-rarities']) || $this->query['filter-rarities'] == $unit['rarity'];
            $valid &= empty($this->query['filter-name']) || mb_strpos(mb_strtolower($unit['name']),
                    mb_strtolower($this->query['filter-name'])) !== false;
            $valid &= empty($this->query['filter-missing-cg']) || $unit['missing_cg'];
            $valid &= empty($this->query['filter-server']) || $unit[$this->query['filter-server']];

            return $valid;
        });
    }
}
