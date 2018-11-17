<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class DiscountCard extends Model
{
    protected $fillable = ['slug', 'name', 'discount'];
}
