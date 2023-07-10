<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;
    protected $fillable = [
        'username', 'friendly_file_name', 'journal_entry_id', 'uploaded_by_admin_id', 'url'
    ];
}
