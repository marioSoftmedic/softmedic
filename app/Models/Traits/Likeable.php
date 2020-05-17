<?php

namespace App\Models\Traits;

use App\Models\Like;

trait Likeable
{
    public static function bootLikeable()
    {
        //observer the model
        static::deleting(function ($model) {
            $model->removeLikes();
        });
    }

    //delete likes when model is being deleted
    public function removeLikes()
    {
        if ($this->likes()->count()) {
            $this->likes()->delete();
        }
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function like()
    {
        //if the person auth
        if (!auth()->check()) {
            return;
        }

        //check if the current user has already liked the model
        if ($this->isLikedByUser(auth()->id())) {
            return;
        }

        $this->likes()->create(['user_id'=> auth()->id()]);
    }

    public function unLike()
    {
        //check if the person has like
        if (!auth()->check()) {
            return;
        }

        if (!$this->isLikedByUser(auth()->id())) {
            return;
        }

        $this->likes()->where('user_id', auth()->id())->delete();
    }

    public function isLikedByUser($user_id)
    {
        return (bool)$this->likes()->where('user_id', $user_id)->count();
    }
}
