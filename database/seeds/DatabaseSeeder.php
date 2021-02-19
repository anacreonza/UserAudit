<?php

use Illuminate\Database\Seeder;
use App\Device;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('devices')->insert([
            'username' => Str::random(10),
            'computername' => '02CORD-'.Str::random(8),
            'reportjson' => Str::random(50),
        ]);
    }
}
