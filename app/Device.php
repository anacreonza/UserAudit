<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $primaryKey = 'device_id';
    protected $fillable = ['device_name', 'last_user', 'report'];
}
