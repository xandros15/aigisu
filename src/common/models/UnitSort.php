<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-07-09
 * Time: 03:39
 */

namespace Aigisu\Common\Models;


use Aigisu\Common\Components\Sort\Sort;

class UnitSort extends Sort
{
    protected function columns() : array
    {
        return [
            'name' => [
                'default' => self::SORT_ASC
            ],
            'original' => [
                'label' => 'Original name',
                'default' => self::SORT_ASC
            ],
            'rarity' => [
                'label' => 'Rarity',
                'default' => self::SORT_ASC
            ]
        ];
    }
}