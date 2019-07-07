<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $fillable = [
        'name',
    ];
    public $timestamps = false;

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_channels');
    }

}
