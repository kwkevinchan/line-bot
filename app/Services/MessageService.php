<?php
namespace App\Services;

use App\Entities\User;
use App\Entities\Channel;

class MessageService
{
    private $user;
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUserEmail()
    {
        return $this->user->email ?? "尚未輸入Email";
    }

    public function setUserEmail($email)
    {
        $this->user->email = $email;
        $this->user->save();
        return $this->user->email;
    }

    public function getAllChannels()
    {
        return Channel::get();
    }
}