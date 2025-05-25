<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaktuLibur extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function grupLibur()
    {
        return $this->hasMany(GroupLibur::class);
    }
}
