<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupParticipant extends Model
{
    use HasFactory;

    public function participants()
    {
        return $this->belongsToMany(Participant::class);
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
    }



}
