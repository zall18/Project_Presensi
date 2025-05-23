<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalParticipant extends Model
{
    use HasFactory;

    public function shift(){
        return $this->hasMany(Shift::class);
    }

    public function participant(){
        return $this->hasMany(Participant::class);
    }

}
