<?php

namespace App\Services;

use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use App\Entities\User;

class LineBotService
{
    private $lineBot;
    private $messageGetType;
    private $messageGetReplyToken;
    private $messageGetUserId;
    private $messageGetText;

    public function __construct()
    {
        $httpClient = new CurlHTTPClient(env('LINEBOT_CHANNEL_TOKEN'));
        $this->lineBot = new LINEBot($httpClient, ['channelSecret' => env('LINEBOT_CHANNEL_SECRET')]);
    }

    public function pushMessage($lineId, $message)
    {
        $messageBuild = new TextMessageBuilder($message);
        $this->lineBot->pushMessage($lineId, $messageBuild);
    }

    public function replyMessage($message)
    {
        $messageBuild = new TextMessageBuilder($message);
        $this->lineBot->replyMessage($this->messageGetReplyToken, $messageBuild);
    }

    public function messageJsonParse($messageJson)
    {
        $this->messageGetReplyToken = $messageJson['events'][0]['replyToken'];
        $this->messageGetUserId = $messageJson['events'][0]['source']['userId'] ?? null;
        $this->messageGetType = $messageJson['events'][0]['message']['type'] ?? null;
        $this->messageGetText = $messageJson['events'][0]['message']['text'] ?? null;

        if ($this->messageGetUserId === null 
            || $this->messageGetType === null 
            || $this->messageGetText === null
        ) {
            $this->replyMessage('對不起, 目前不支援此類型訊息');
        }
    }

    public function getMessageContext()
    {
        return $this->messageGetText;
    }

    public function messageGetUserId()
    {
        return $this->messageGetUserId;
    }

    public function getUserProfile($userLineId)
    {
        return $this->lineBot->getProfile($userLineId);
    }

    public function checkNewUser()
    {
        $user = User::where('line_id', $this->messageGetUserId)->first();
        if ($user == null){
            $userProfile = $this->lineBot->getProfile($this->messageGetUserId);
            User::create('line_id', $this->messageGetUserId);
        }
    }
}