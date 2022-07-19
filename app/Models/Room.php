<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SchooClass;
use App\Models\Priority;
use Illuminate\Support\Facades\DB;

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

    public function isCompatible(SchoolClass $t1, $ignore_block=false, $ignore_estmtr=false)
    {
        if(!$ignore_estmtr){
            if($t1->estmtr){
                if($this->assentos < $t1->estmtr*1.2){
                    return false;
                }
            }
        }
        if(!$ignore_block){
            if(($t1->tiptur=="Graduação" and $this->nome[0]=="A") or 
                ($t1->tiptur=="Pós Graduação" and $this->nome[0]=="B")){
                return false;
            }
        }
        foreach($this->schoolclasses as $t2){
            if($t1->isInConflict($t2)){
                return false;
            }
        }
        return true;
    }
}
