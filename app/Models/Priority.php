<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Room;
use App\Models\SchooClass;
use Illuminate\Support\Facades\DB;

class Priority extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'school_class_id',
        'priority',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class, "room_id");
    }

    public function schoolclass()
    {
        return $this->belongsTo(SchoolClass::class, "school_class_id");
    }

    public static function calculaPrioridades(SchoolTerm $schoolterm)
    {
        print("Carregando disciplinas...\n");
        $disciplinas = DB::table("school_classes")->where("school_term_id",$schoolterm->id)
                            ->select(["coddis","tiptur"])->distinct()->get();
        $disciplinas = json_decode(json_encode($disciplinas),true);
        print("Disciplinas carregadas.\n");

        $prioridades = [];
        foreach($disciplinas as $disciplina){
            $coddis = $disciplina["coddis"];
            $tiptur = $disciplina["tiptur"];
            print("Carregando reservas da disciplina ".$coddis."...\n");

            $reservas = DB::table("RESERVA")->select("nome as sala")
                ->join("REQUISICAO","RESERVA.requisicao_id","=","REQUISICAO.id")
                ->join("SALA","RESERVA.sala_numero","=","SALA.numero")
                ->where("atividade","LIKE","%".$coddis."%")
                ->where("atividade","!=","Prova")
                ->where("atividade","!=","Monitoria")
                ->orWhere("titulo","like","%".$coddis."%")->get()->groupBy('sala');

            print("Reservas da disciplina ".$coddis." carregadas.\n");

            if(count($reservas)>0){
                print("Calculando prioridades da disciplina ".$coddis."...\n");
                foreach($reservas as $key=>$value){
                    if(($tiptur=="Graduação" and $key[0]=="B") or ($tiptur=="Pós Graduação" and $key[0]=="A")){
                        $prioridades[$coddis][$key]=count($value);
                    }
                }
                
                $total = array_sum($prioridades[$coddis]);
                if($total){
                    foreach($prioridades[$coddis] as $key=>$value){
                        $prioridades[$coddis][$key] = 100*$value/$total;
                    }
                }
                print("Prioridades da disciplina ".$coddis." calculadas.\n");
                print_r($prioridades[$coddis]);
            }else{
                print("Não foram encontradas reservas para disciplina ".$coddis.".\n");
            }
        }

        foreach($prioridades as $coddis=>$salas){
            $turmas = SchoolClass::where("coddis",$coddis)->get();
            foreach($turmas as $turma){
                foreach($salas as $sala_nome=>$prioridade){
                    $sala = Room::where("nome", $sala_nome)->first();
                    if($sala){
                        Priority::updateOrCreate(
                            ["room_id"=>$sala->id,"school_class_id"=>$turma->id],
                            ["priority"=>$prioridade]
                        );
                    }
                }
            }
        }
    }
}
