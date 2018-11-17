<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class VehicleCategory extends Model
{
    protected $fillable = ['slug', 'name', 'day_tariff', 'night_tariff', 'spaces_required'];
}
