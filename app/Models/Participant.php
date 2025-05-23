<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_participants', 'id_participant', 'id_group');
    }

    public function jadwalParticipant()
    {
        return $this->belongsToMany(JadwalParticipant::class);
    }
}
