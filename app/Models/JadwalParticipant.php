<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalParticipant extends Model
{
    use HasFactory;

    public function shift(){
        return $this->belongsTo(Shift::class, 'id_shift');
    }

    public function participant(){
        return $this->belongsTo(Participant::class);
    }

}
