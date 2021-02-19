<?php

use Illuminate\Database\Seeder;
use App\Device;

class DeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Device::factory('DeviceFactory')->count(100)->create();
    }
}
