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

    private const RARITY_ORDER = [
        'black' => 1,
        'sapphire' => 2,
        'platinum' => 3,
        'gold' => 4,
        'silver' => 5,
        'bronze' => 6,
        'iron' => 7,
    ];

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
        if (!$units) {
            return $units;
        }

        switch ($this->query['sort-units'] ?? '') {
            case 'rarity':
                usort($units, function ($unitA, $unitB) {
                    return self::RARITY_ORDER[$unitA['rarity']] <=> self::RARITY_ORDER[$unitB['rarity']];
                });
                break;
            case 'name':
            case 'created_at':
                usort($units, function ($unitA, $unitB) {
                    return $unitA[$this->query['sort-units']] <=> $unitB[$this->query['sort-units']];
                });
                break;
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
