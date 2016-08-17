<?php

namespace App;

use App\Meeting;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $fillable = ['time', 'title', 'description'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function userIsNotRegistered($user_id)
    {
        if(!$this->users()->where('users.id', $user_id)->first())
        	return true;
    }

    public function setTimeAttribute($value)
    {
        $this->attributes['time'] = Carbon::createFromFormat('YmdHie', $value);
    }
}
