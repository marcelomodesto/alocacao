<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SchoolClass;
use Uspdev\Replicado\DB;

class Instructor extends Model
{
    use HasFactory;

    protected $fillable = [
        'codpes',
        'nompes',
        'codema',
    ];

    public function schoolclasses()
    {
        return $this->belongsToMany(SchoolClass::class);
    }

    public function getPronounTreatment()
    {
        $query = " SELECT P.sexpes";
        $query .= " FROM PESSOA AS P";
        $query .= " WHERE P.codpes = :codpes";
        $param = [
            'codpes' => $this->codpes,
        ];

        $res = DB::fetchAll($query, $param)[0];

        if($res['sexpes'] == 'F'){
            return 'Profa. Dra. ';
        }else{
            return 'Prof. Dr. ';
        }
    }

    public function getNomAbrev()
    {
        $pattern = '/ de | do | dos | da | das | e /i';
        $nome = preg_replace($pattern,' ',$this->nompes);
        $nome = explode(' ', $nome);
        
        $nomes_meio = ' ';
        
        if(count($nome) > 2){
            for($x=1;$x<count($nome)-1;$x++){
                $nomes_meio .= $nome[$x][0].". ";
            }
        }
        
        $nomeabreviado = array_shift($nome).$nomes_meio.array_pop($nome);
        
        return $nomeabreviado;
    }

    public static function getFromReplicadoBySchoolClass($schoolclass)
    {
        $query = " SELECT M.codpes";
        $query .= " FROM OCUPTURMA AS O, MINISTRANTE AS M";
        $query .= " WHERE O.coddis = :coddis";
        $query .= " AND O.codtur = :codtur";
        $query .= " AND M.coddis = :coddis";
        $query .= " AND M.codtur = :codtur";
        $query .= " AND M.codperhor = O.codperhor";
        $param = [
            'coddis' => $schoolclass['coddis'],
            'codtur' => $schoolclass['codtur'],
        ];

        $res = array_unique(DB::fetchAll($query, $param),SORT_REGULAR);

        $instructors = [];
        foreach($res as $codpes){
            array_push($instructors, SELF::getFromReplicadoByCodpes($codpes['codpes']));
        }

        return $instructors;
    }

    public static function getFromReplicadoByPosSchoolClass($schoolclass)
    {
        $query = " SELECT M.codpes";
        $query .= " FROM R32TURMINDOC AS M";
        $query .= " WHERE M.sgldis = :sgldis";
        $query .= " AND M.numseqdis = :numseqdis";
        $query .= " AND M.numofe = :numofe";
        $param = [
            'sgldis' => $schoolclass['coddis'],
            'numseqdis' => $schoolclass['numseqdis'],
            'numofe' => $schoolclass['numofe'],
        ];

        $res = array_unique(DB::fetchAll($query, $param),SORT_REGULAR);

        $instructors = [];
        foreach($res as $codpes){
            array_push($instructors, SELF::getFromReplicadoByCodpes($codpes['codpes']));
        }

        return $instructors;
    }
    
    public static function getFromReplicadoByCodpes($codpes)
    {
        $query = " SELECT P.codpes, P.nompes, VP.codset, EP.codema";
        $query .= " FROM PESSOA AS P, VINCULOPESSOAUSP AS VP, EMAILPESSOA as EP";
        $query .= " WHERE P.codpes = :codpes";
        $query .= " AND VP.codpes = :codpes";
        $query .= " AND EP.codpes = :codpes";
        $param = [
            'codpes' => $codpes,
        ];

        $res = array_unique(DB::fetchAll($query, $param),SORT_REGULAR);

        if($res){
            unset($res[0]["codset"]);
    
            return $res[0];
        }else{
            return [];
        }
    }
}
