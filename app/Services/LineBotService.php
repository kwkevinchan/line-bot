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
            throw new \Exception('message body error: ' . json_encode($messageJson));
        }

        $user = $this->checkUser();
        return [
            'user' => $user,
            'message' => $this->messageGetText,
        ];
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

    public function checkUser()
    {
        try {
            $user = User::where('line_id', $this->messageGetUserId)->first();
            if ($user == null){
                $userProfile = $this->lineBot->getProfile($this->messageGetUserId)->getJSONDecodedBody();
                $user = User::create([
                    'name' => $userProfile['displayName'],
                    'line_id' => $this->messageGetUserId
                ]);

                $this->pushMessage($this->messageGetUserId, "歡迎您, 新的使用者:\n" . $userProfile['displayName'] . "\n
                    '我的Email':可查詢您目前的Email\n
                    '更新Email:{Email}':可建立您的Email資訊\n
                    '頻道清單':可查詢目前支援的新聞頻道\n
                    '我的頻道':可查詢目前訂閱的新聞頻道\n
                    '訂閱{頻道名}':可以訂閱選定的新聞頻道\n
                    '取消訂閱{頻道名}':可以取消訂閱的頻道\n
                    '幫助':可查詢本提示");
            }

            return $user;
        } catch ( \Exception $e ) {
            throw new \Exception($e->getMessage());
        }
    }
}