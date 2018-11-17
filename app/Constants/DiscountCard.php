<?php namespace App\Constants;

use ReflectionClass;

class DiscountCard
{
    const SILVER   = 'silver';
    const GOLD     = 'gold';
    const PLATINUM = 'platinum';

    public static function getValuesForValidation()
    {
        $oClass = new ReflectionClass(__CLASS__);

        return implode(',', $oClass->getConstants());
    }
}
