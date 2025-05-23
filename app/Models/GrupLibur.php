<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrupLibur extends Model
{
    use HasFactory;

    public function waktu_libur(){
        return $this->hasMany(WaktuLibur::class);
    }

    public function group(){
        return $this->hasMany(Group::class);
    }
}
