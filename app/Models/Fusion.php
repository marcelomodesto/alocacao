<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SchoolClass;

class Fusion extends Model
{
    use HasFactory;

    protected $fillable = [
        'master_id',
    ];

    public function master()
    {
        return $this->belongsTo(SchoolClass::class, "master_id");
    }

    public function schoolclasses()
    {
        return $this->hasMany(SchoolClass::class);
    }
}