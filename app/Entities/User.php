<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = [
        'name',
        'email',
        'line_id',
        'permission',
    ];

    public function channels()
    {
        return $this->belongsToMany(Channel::class, 'user_channels');
    }

}
