<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    public function participants()
    {
        return $this->belongsToMany(Participant::class, 'group_participants', 'id_group', 'id_participant');
    }

    public function grupLibur() {
        return $this->belongsToMany(GrupLibur::class, 'group_liburs');
    }
}
