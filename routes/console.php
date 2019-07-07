<?php

use App\Entities\User;
use App\Entities\Channel;
/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('test', function () {
    $channels = Channel::where('id', 1)->first();
    dd(($channels->users)[0]->name);
});
