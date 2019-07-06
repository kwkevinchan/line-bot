<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LineBotService;
use App\Contracts\MessagePaserContract;

class LineBotController extends Controller
{
    public function pushMessage()
    {
        $lineBot = new LineBotService();
        $lineBot->pushMessage(evn('ADMIN_LINE_ID'), 'Hello World');
        return 'message send';
    }

    public function webhook(Request $request)
    {
        $lineBot = new LineBotService();
        $lineWebhookJson = $request->json()->all();
        $message = $lineBot->messageJsonParse($lineWebhookJson);
        $contract = new MessagePaserContract($message['user'], $message['message']);
        $returnMessage = $contract->paserMessage();
        $lineBot->replyMessage($returnMessage);
        return response('good', 200)->withHeaders([
            "Access-Control-Allow-Origin" => "*"
        ]);
    }
}
