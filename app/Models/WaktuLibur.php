<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaktuLibur extends Model
{
    use HasFactory;

    public function grupLibur()
    {
        return $this->belongsToMany(GrupLibur::class);
    }
}
