<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SchooClass;
use App\Models\Priority;

class Room extends Model
{
    use HasFactory;
    public $prioridade = 0;

    protected $fillable = [
        'nome',
        'assentos'
    ];

    public function schoolclasses()
    {
        return $this->hasMany(SchoolClass::class);
    }

    public function priorities()
    {
        return $this->hasMany(Priority::class);
    }
}
