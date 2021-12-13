<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = [
        'report_data',
        'computer_name',
        'user_name'
    ];
    use HasFactory;
}
