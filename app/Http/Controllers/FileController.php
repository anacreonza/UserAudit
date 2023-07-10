<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Client;
use App\File;
use App\JournalEntry;

class FileController extends Controller
{
    public function download($id){
        $file = File::where('id', $id)->first();
        return Storage::download($file->name, $file->friendly_file_name);
    }
}
