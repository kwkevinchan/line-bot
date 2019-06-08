<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LineBotController extends Controller
{
    public function push()
    {
        return null;
    }

    public function webhook(Request $request)
    {
        return $request->toArray;
    }
}
