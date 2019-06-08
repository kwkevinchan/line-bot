<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use Illuminate\Support\Facades\Storage;

class LineBotController extends Controller
{
    public function pushMessage()
    {
        $httpClient = new CurlHTTPClient(env('LINEBOT_CHANNEL_TOKEN'));
        $lineBot = new LINEBot($httpClient, ['channelSecret' => env('LINEBOT_CHANNEL_SECRET')]);
        $messageBuild = new TextMessageBuilder('Hello, test!');
        $lineBot->pushMessage(env('LINE_USER_ID'), $messageBuild);
        return 'good';
    }

    public function webhook(Request $request)
    {
        var_dump($request->toArray());
        Storage::disk('public')->append('line.log', $request->toArray());
        return response('good', 200)->withHeaders([
            "Access-Control-Allow-Origin" => "*"
        ]);
    }
}
