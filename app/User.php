<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
        public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }
    
    
    
    //多対多の関係がUserに対するものなので、Userモデルファイルに記述。
        public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    
    
    //フォロー、アンフォローできるようになる機能を追加。
    
    public function follow($userId)
{
    // confirm if already following
    $exist = $this->is_following($userId);
    // confirming that it is not you
    $its_me = $this->id == $userId;

    if ($exist || $its_me) {
        // do nothing if already following
        return false;
    } else {
        // follow if not following
        $this->followings()->attach($userId);
        return true;
    }
}

    public function unfollow($userId)
{
    // confirming if already following
    $exist = $this->is_following($userId);
    // confirming that it is not you
    $its_me = $this->id == $userId;


    if ($exist && !$its_me) {
        // stop following if following
        $this->followings()->detach($userId);
        return true;
    } else {
        // do nothing if not following
        return false;
    }
}


    public function is_following($userId) {
    return $this->followings()->where('follow_id', $userId)->exists();
}


     public function feed_microposts()
    {
        $follow_user_ids = $this->followings()-> pluck('users.id')->toArray();
        $follow_user_ids[] = $this->id;
        return Micropost::whereIn('user_id', $follow_user_ids);
    }
    
    
    
    
    //Favorite機能を追加
    
    public function favorites()
    {
        return $this->belongsToMany(Micropost::class, 'favorite', 'user_id','micropost_id')->withTimestamps();
    }
    
    
    public function favorite($micropostId)
{
    // confirm if already favorite
    $exist = $this->is_favorite($micropostId);   //変えるかも

    if ($exist) {
        return false;
    } else {
        // favorite if not following
        $this->favorites()->attach($micropostId);
        return true;
    }
}

    public function unfavorite($micropostId)
{
    // confirming if already favorite
    $exist = $this->is_favorite($micropostId);


    if ($exist) {
        $this->favorites()->detach($micropostId);
        return true;
    } else {
        // do nothing if not favorite
        return false;
    }
}


    public function is_favorite($micropostId) {
    return $this->favorites()->where('micropost_id', $micropostId)->exists();
}
    
    













}


