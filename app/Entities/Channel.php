<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $fillable = [
        'name',
    ];
    public $timestamps = false;

    public function channels()
    {
        return $this->belongsToMany(Channel::class, 'user_channels');
    }

}
