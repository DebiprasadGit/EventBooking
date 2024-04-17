<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventBookingModel extends Model
{
    use HasFactory;
    public $table='eventbooking';
    public $timestamps=false;
}
