<?php

use Illuminate\Database\Seeder;
use App\Entities\Channel;

class ChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Channel::create(['name' => '蘋果']);
        Channel::create(['name' => '卡提諾']);
        Channel::create(['name' => 'Yahoo']);
    }
}
