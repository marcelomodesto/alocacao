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

    public static function getLatest()
    {
        $year = SchoolTerm::max("year");
        $period = SchoolTerm::where("year",$year)->max("period");
        return SchoolTerm::where(["year"=>$year,"period"=>$period])->first();
    } 
}
