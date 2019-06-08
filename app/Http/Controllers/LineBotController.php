<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LINE\LINEBot\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

class LineBotController extends Controller
{
    public function pushMessage()
    {
        $httpClient = new CurlHTTPClient(env('LINEBOT_CHANNEL_TOKEN'));
        $lineBot = new LINEBot($httpClient, ['channelSecret' => env('LINEBOT_CHANNEL_SECRET')]);
        $messageBuild = new TextMessageBuilder('Hello, test!');
        return $lineBot->pushMessage(env('LINEBOT_CHANNEL_ID'), $messageBuild);
    }

    // public function webhook(Request $request)
    // {
    //     return $request->toArray;
    // }
}
