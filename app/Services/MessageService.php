<?php
namespace App\Services;

use App\Entities\User;
use App\Entities\Channel;
use Illuminate\Support\Facades\Redis;

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
        Redis::del($this->user->line_id);
        return $this->user->email;
    }

    public function getAllChannels()
    {
        return Channel::get();
    }

    public function getUserChannels()
    {
        return $this->user->channels;
    }

    public function setUserChannel($channel)
    {
        $channels = array_map('trim', explode(',', $channel));
        Redis::del($this->user->line_id);
        $channelIds = Channel::whereIn('name', $channels)->get()->pluck('id');
        if ($channelIds->isNotEmpty()) {
            $channelIds = $channelIds->merge($this->user->channels->pluck('id'));
            $this->user->channels()->sync($channelIds);
            return true;
        } else {
            return false;
        }
    }

    public function unsetUserChannel($channel)
    {
        $channels = array_map('trim', explode(',', $channel));
        Redis::del($this->user->line_id);
        $channelIds = Channel::whereIn('name', $channels)->get()->pluck('id');
        if ($channelIds->isNotEmpty()) {
            $this->user->channels()->detach($channelIds);
            return true;
        } else {
            return false;
        }
    }

    public function showChannelUser($channelId)
    {
        $channel = Channel::where('id', (int) $channelId)->first();
        if ($channel === null){
            return ['success' => false];
        } else {
            return [
                'success' => true,
                'users' => $channel->users,
            ];
        }

    }

}