<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ShowPosCourseScheduleRequest;
use App\Models\Course;
use App\Models\SchoolTerm;
use App\Models\SchoolClass;

class CourseScheduleController extends Controller
{
    public function index()
    {
        return view("courseschedules.index");
    }

    public function show(Course $course)
    {
        return view("courseschedules.show", compact("course"));
    }

    public function showLicNot()
    {
        return view("courseschedules.showLicNot");
    }

    public function showAll()
    {
        return view("courseschedules.showAll");
    }

    public function showPos(ShowPosCourseScheduleRequest $request)
    {
        $validated = $request->validated();

        $schoolterm = SchoolTerm::getLatest();
        
        if($validated["prefixo"] != "MPM"){
            $schoolclasses = SchoolClass::whereBelongsTo($schoolterm)
                ->where("tiptur","Pós Graduação")
                ->where("coddis","LIKE",$validated["prefixo"]."%")->orderBy("coddis")->get();
            $programas = [
                "MAC"=>"Programa de Pós-graduação em Ciência da Computação",
                "MAE"=>"Programa de Pós-graduação em Estatística",
                "MAT"=>"Programa de Pós-graduação em Matemática",
                "MAP"=>"Programa de Pós-graduação em Matemática Aplicada"];
            $titulo = $programas[$validated["prefixo"]];
        }else{
            $schoolclasses = SchoolClass::whereBelongsTo($schoolterm)
                ->where("tiptur","Pós Graduação")
                ->where(function($query){
                    $query->where("coddis","LIKE","MPM%")
                        ->orWhere("coddis","LIKE","GEN%");
                })->orderBy("coddis")->get();
            $titulo = "Mestrado Profissional em Ensino de Matemática";
        }
        
        return view("courseschedules.showpos", compact(["schoolclasses","titulo"]));
    }
}
