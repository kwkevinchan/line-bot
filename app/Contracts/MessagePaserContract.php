<?php
namespace App\Contracts;

use App\Services\MessageService;

class MessagePaserContract
{
    private $message;
    private $messageService;

    public function __construct($user, $message)
    {
        $this->message = $message;
        $this->messageService = new MessageService($user);
    }

    public function paserMessage()
    {
        $message = $this->message;

        switch ($message)
        {
            case '幫助':
                $returnMessage = 
                    "'我的Email':可查詢您目前的Email\n\n" .
                    "'更新Email:{email}':可建立您的Email資訊\n\n" .
                    "'頻道清單':可查詢目前支援的新聞頻道\n\n" .
                    "'我的頻道':可查詢目前訂閱的新聞頻道\n\n" .
                    "'訂閱{頻道名}':可以訂閱選定的新聞頻道\n\n" .
                    "'取消訂閱{頻道名}':可以取消訂閱的頻道\n\n" .
                    "'幫助':可查詢本提示\n\n"
                ;
                break;

            case '我的Email':
                $email = $this->messageService->getUserEmail();
                $returnMessage = "您的Email為:\n" . $email;
                break;

            case '頻道清單':
                $channels = $this->messageService->getAllChannels();
                $returnMessage = "現在支援的頻道為:\n";
                foreach($channels as $channel) {
                    $returnMessage .= $channel->name . "\n";
                }
                break;

            case '我的頻道':
                $channels = $this->messageService->getUserChannels();
                $returnMessage = "您所訂閱的頻道為:\n";
                foreach($channels as $channel) {
                    $returnMessage .= $channel->name . "\n";
                }
                break;

            case (preg_match('#^更新Email:#', $message) ? true : false):
                $email = explode('更新Email:', $message)[1];
                $this->messageService->setUserEmail($email);
                $returnMessage = "您的Email已設定為:\n" . $email;
                break;
            
            case (preg_match('#^訂閱#', $message) ? true : false):
                $channel = explode('訂閱', $message)[1];
                $success = $this->messageService->setUserChannel($channel);
                if ($success) {
                    $returnMessage = "您已訂閱:\n" . $channel;
                } else {
                    $returnMessage = "查無此頻道";
                }
                break;

            case (preg_match('#^取消訂閱#', $message) ? true : false):
                $channel = explode('取消訂閱', $message)[1];
                $success = $this->messageService->unsetUserChannel($channel);
                if ($success) {
                    $returnMessage = "您已取消訂閱:\n" . $channel;
                } else {
                    $returnMessage = "您並未訂閱或查無此頻道";
                }
                break;

            default:
                $returnMessage = "您的輸入為:\n" . $message . "\n如需幫助請輸入'幫助'查看操作指令";
                break;
        }

        return $returnMessage;
    }
}