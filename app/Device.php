<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = [
        'username', 'computername', 'os', 'devicetype', 'licences', 'reportjson'
    ];
}
