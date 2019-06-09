<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LineBotService;

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
        $lineBot->messageJsonParse($lineWebhookJson);
        $message = $lineBot->getMessageContext();
        $lineBot->replyMessage("Hello, 你輸入的訊息是: \n" . $message);
        return response('good', 200)->withHeaders([
            "Access-Control-Allow-Origin" => "*"
        ]);
    }
}
