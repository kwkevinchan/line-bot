<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;

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
        $lineId = $this->messageGetUserId;
        $user = Redis::get($lineId);
        if($user === null){
            $user = User::where('line_id', $lineId)->first();
            if ($user == null){
                $user = $this->createUser($lineId);
            }
            Redis::set($lineId, serialize($product));
        } else {
            $user = unserialize($user);
        }
        return $user;
    }

    public function createUser($lineId)
    {
        $userProfile = $this->lineBot->getProfile($lineId)->getJSONDecodedBody();
        $user = User::create([
            'name' => $userProfile['displayName'],
            'line_id' => $this->messageGetUserId
        ]);

        $this->pushMessage($this->messageGetUserId, "歡迎您, 新的使用者:\n\n" . $userProfile['displayName'] . "\n
            '我的Email':可查詢您目前的Email\n\n
            '更新Email:{Email}':可建立您的Email資訊\n\n
            '頻道清單':可查詢目前支援的新聞頻道\n\n
            '我的頻道':可查詢目前訂閱的新聞頻道\n\n
            '訂閱{頻道名}':可以訂閱選定的新聞頻道\n\n
            '取消訂閱{頻道名}':可以取消訂閱的頻道\n\n
            '查詢頻道訂閱人{頻道ID}':可以查詢頻道的訂閱人\n\n
            '幫助':可查詢本提示");

        return $user;
    }
}