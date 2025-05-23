<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    public function jam_kerja() {
        return $this->belongsTo(JamKerja::class);
    }

    public function detail_shift() {
        return $this->hasOne(DetailShift::class);
    }

    public function jadwal_participant() {
        return $this->belongsToMany(JadwalParticipant::class);
    }

    public function presensi() {
        return $this->hasMany(Presensi::class);
    }
}
