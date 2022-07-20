<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Uspdev\Replicado\DB;
use App\Models\SchoolClass;

class CourseInformation extends Model
{
    use HasFactory;

    protected $table = "course_informations";

    protected $fillable = [
        'nomcur',
        'codcur',
        'numsemidl',
        'perhab',
        'tipobg',
    ];

    public function schoolclasses()
    {
        return $this->belongsToMany(SchoolClass::class);
    }

    static $codtur_by_course = [
        "43"=>["nomcur"=>"Matemática Bacharelado", "perhab"=>"diurno"],
        "45"=>["nomcur"=>"Bacharelado em Ciência da Computação", "perhab"=>"diurno"],
        "46"=>["nomcur"=>"Estatística Bacharelado", "perhab"=>"diurno"],
        "44"=>["nomcur"=>"Matemática Aplicada - Bacharelado", "perhab"=>"diurno"],
        "54"=>["nomcur"=>"Bacharelado em Matemática Aplicada e Computacional", "perhab"=>"diurno"],
        "42"=>["nomcur"=>"Matemática Licenciatura", "perhab"=>"diurno"],
        "47"=>["nomcur"=>"Matemática Licenciatura", "perhab"=>"noturno", "grupo"=>"A"],
        "48"=>["nomcur"=>"Matemática Licenciatura", "perhab"=>"noturno", "grupo"=>"B"],
    ];


    public static function getFromReplicadoByCoddis($coddis)
    {

        $query = " SELECT CS.nomcur, CS.codcur, GC.numsemidl, HGR.perhab, GC.tipobg";
        $query .= " FROM UNIDADE AS U, SETOR AS S, PREFIXODISCIP AS PD, CURSOGR as CS, HABILITACAOGR AS HGR, CURRICULOGR AS CGR, GRADECURRICULAR AS GC";
        $query .= " WHERE (U.sglund LIKE :sglund)";
        $query .= " AND S.codund = U.codund";
        $query .= " AND PD.codset = S.codset";
        $query .= " AND CS.codclg = PD.codclg";
        $query .= " AND HGR.codcur = CS.codcur";
        $query .= " AND HGR.dtadtvhab IS NULL";
        $query .= " AND CGR.codcur = CS.codcur";
        $query .= " AND CGR.codhab = HGR.codhab";
        $query .= " AND GC.codcrl = CGR.codcrl";
        $query .= " AND GC.coddis = :coddis";
        $query .= " AND GC.verdis = (SELECT MAX(GC2.verdis) 
                                    FROM GRADECURRICULAR AS GC2 
                                    WHERE GC2.coddis = :coddis)";
        $param = [
            'sglund' => env("UNIDADE"),
            'coddis' => $coddis,
        ];

        return array_unique(DB::fetchAll($query, $param),SORT_REGULAR);
    }
}
