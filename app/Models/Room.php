<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SchooClass;
use App\Models\Priority;
use App\Models\ClassSchedule;
use Illuminate\Support\Facades\DB;

class Room extends Model
{
    use HasFactory;
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;
    public $prioridade = 0;

    protected $fillable = [
        'nome',
        'assentos'
    ];

    public function schoolclasses()
    {
        return $this->hasMany(SchoolClass::class);
    }

    public function schedules()
    {
        return $this->hasManyDeepFromRelations($this->schoolclasses(), (new SchoolClass())->classschedules());
    }

    public function priorities()
    {
        return $this->hasMany(Priority::class);
    }

    public function isCompatible(SchoolClass $t1, $ignore_block=false, $ignore_estmtr=false)
    {
        if($t1->externa){
            return false;
        }
        if(!$ignore_estmtr){
            if($t1->fusion()->exists()){
                if($t1->fusion->schoolclasses->pluck("estmtr")->filter()->isNotEmpty() and
                    ($this->assentos <= $t1->fusion->schoolclasses->sum("estmtr")*1.2)){
                        return false;
                }
            }elseif($t1->estmtr){
                if($this->assentos <= $t1->estmtr){
                    return false;
                }
            }
        }
        // Excepcionalmente por conta da reforma no bloco B as turmas da graduação serão alocadas no bloco A
        if(!$ignore_block){
            if($t1->tiptur=="Pós Graduação" and $this->nome[0]=="B"){
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
