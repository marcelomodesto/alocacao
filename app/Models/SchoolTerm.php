<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SchoolClass;

class SchoolTerm extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'period',
    ];

    public function schoolclasses()
    {
        return $this->hasMany(SchoolClass::class);
    }

    public static function getCurrentSchoolTerm()
    {
        return SchoolTerm::where(['year'=>date("Y"),'period'=>(date("m")<=7 ? "1° Semestre" : "2° Semestre")])->first();
    } 
}
