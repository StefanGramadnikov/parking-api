<?php namespace App\Constants;

use ReflectionClass;

class VehicleCategory
{
    const A = 'a';
    const B = 'b';
    const C = 'c';

    public static function getValuesForValidation()
    {
        $oClass = new ReflectionClass(__CLASS__);

        return implode(',', $oClass->getConstants());
    }
}
