<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = ['vehicle_id'];

    public function vehicleCategory()
    {
        return $this->belongsTo(VehicleCategory::class);
    }

    public function discountCard()
    {
        return $this->belongsTo(DiscountCard::class);
    }
}
