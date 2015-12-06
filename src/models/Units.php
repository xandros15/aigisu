<?php

namespace models;

class Units
{
    const RARITY_BLACK    = 'black';
    const RARITY_SAPPHIRE = 'sapphire';
    const RARITY_PLATINUM = 'platinum';
    const RARITY_GOLD     = 'gold';
    const RARITY_SILVER   = 'silver';
    const RARITY_BRONZE   = 'bronze';
    const RARITY_IRON     = 'iron';

    public static function getRarities()
    {
        return [
            self::RARITY_SAPPHIRE,
            self::RARITY_BLACK,
            self::RARITY_PLATINUM,
            self::RARITY_GOLD,
            self::RARITY_SILVER,
            self::RARITY_BRONZE,
            self::RARITY_IRON
        ];
    }
}