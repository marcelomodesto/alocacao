@extends('main')

@section('title', 'Horário das Disciplinas')

@section('content')
  @parent 
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @php
                $schoolterm = App\Models\SchoolTerm::getLatest();
            @endphp
            <h1 class='text-center mb-5'><b>{!! $course->nomcur !!}</b></h1>
            <h2 class='text-center mb-5'>Horário das Disciplinas - {!! $schoolterm->period . ' de ' . $schoolterm->year !!}</h2>

            <div class="d-flex justify-content-center">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped table-hover" style="font-size:15px;">
                        <tr>
                            <th>Código do Curso</th>
                            <th>Período</th>
                            @if($course->grupo)
                                <th>Grupo</th>
                            @endif
                        </tr>

                        <tr style="font-size:12px;">
                                <td>{{ $course->codcur }}</td>
                                <td>{{ ucfirst($course->perhab) }}</td>
                            @if($course->grupo)
                                <th>{{ $course->grupo }}</th>
                            @endif
                        </tr>
                    </table>
                </div>
            </div>

            @php

                $semestres = $schoolterm->period == "1° Semestre" ? [1,3,5,7,9] : [2,4,6,8,10];

                $turmas = App\Models\SchoolClass::whereBelongsTo($schoolterm)
                        ->whereHas("courseinformations", function($query)use($semestres, $course){
                            $query->whereIn("numsemidl",$semestres)
                                ->where("nomcur",$course->nomcur)
                                ->where("perhab", $course->perhab);
                            })->get();
            @endphp

            @if($turmas->isNotEmpty())
                @foreach($semestres as $semestre)
                    @php
                        $semestres = $schoolterm->period == "1° Semestre" ? [1,3,5,7,9] : [2,4,6,8,10];

                        $turmas = App\Models\SchoolClass::whereBelongsTo($schoolterm)
                                ->whereHas("courseinformations", function($query)use($semestre, $course){
                                    $query->where("numsemidl",$semestre)
                                        ->where("nomcur",$course->nomcur)
                                        ->where("perhab", $course->perhab);
                                    })->get();

                        $habs = [];

                        foreach($turmas as $turma){
                            $habs = array_merge($habs, array_column(
                                $turma->courseinformations()
                                    ->select(["codhab","nomhab"])
                                    ->whereIn("numsemidl",$semestres)
                                    ->where("nomcur",$course->nomcur)
                                    ->where("perhab", $course->perhab)
                                    ->get()->sortBy("codhab")->toArray(),"codhab", "nomhab"));
                        }
                    @endphp
                    @foreach($habs as $nomhab=>$codhab)
                        @php
                        $turmas = App\Models\SchoolClass::whereBelongsTo($schoolterm)
                            ->whereHas("courseinformations", function($query)use($semestre, $course, $codhab){
                                $query->where("numsemidl",$semestre)
                                    ->where("codhab", $codhab)
                                    ->where("nomcur",$course->nomcur)
                                    ->where("perhab", $course->perhab)
                                    ->whereIn("tipobg", ["O", "C"]);
                                })->get();
                        
                        if($course->grupo){
                            if($course->grupo=="A"){
                                $turmas = $turmas->filter(function($turma)use($turmas, $schoolterm){
                                    $codturs = $turmas->where("coddis",$turma->coddis)->pluck("codtur")->toArray();
                                    $prefixo_codtur = $schoolterm->year.($schoolterm->period == "1° Semestre" ? "1" : "2");
                                    if(in_array($prefixo_codtur."47", $codturs) and in_array($prefixo_codtur."48", $codturs)){
                                        if(substr($turma->codtur,-2,2)!="48"){
                                            return true;
                                        }else{
                                            return false;
                                        }
                                    }else{
                                        return true;
                                    }
                                });
                            }elseif($course->grupo=="B"){
                                $turmas = $turmas->filter(function($turma)use($turmas, $schoolterm){
                                    $codturs = $turmas->where("coddis",$turma->coddis)->pluck("codtur")->toArray();
                                    $prefixo_codtur = $schoolterm->year.($schoolterm->period == "1° Semestre" ? "1" : "2");
                                    if(in_array($prefixo_codtur."47", $codturs) and in_array($prefixo_codtur."48", $codturs)){
                                        if(substr($turma->codtur,-2,2)!="47"){
                                            return true;
                                        }else{
                                            return false;
                                        }
                                    }else{
                                        return true;
                                    }
                                });
                            }
                        }

                        $dias = ['seg', 'ter', 'qua', 'qui', 'sex'];  

                        $schedules = array_unique(App\Models\ClassSchedule::whereHas("schoolclasses", function($query)use($turmas){$query->whereIn("id",$turmas->pluck("id")->toArray());})->select(["horent","horsai"])->whereNotIn("diasmnocp", ["sab","dom"])->get()->toArray(),SORT_REGULAR);

                        array_multisort(array_column($schedules, "horent"), SORT_ASC, $schedules);

                        $horarios = [];
                        foreach($schedules as $schedule){
                            array_push($horarios, $schedule["horent"]." às ".$schedule["horsai"])
                            ;
                        }
                        @endphp
                        @if($turmas->isNotEmpty())
                            <h2 class="text-left"><b>{!! $semestre."° Semestre".(count($habs) > 1 ? ( in_array($codhab, [1,4]) ? " 00".$codhab." - "."Núcleo Básico" : " ".$codhab." - ".explode("Habilitação em ", $nomhab)[1]) : "") !!}</b></h2>
                            <br>
                            <table class="table table-bordered" style="font-size:15px;">
                                <tr style="background-color:#F5F5F5">
                                    <th>Horários</th>
                                    <th>Segunda</th>
                                    <th>Terça</th>
                                    <th>Quarta</th>
                                    <th>Quinta</th>
                                    <th>Sexta</th>
                                </tr>
                                @foreach($horarios as $h)
                                    <tr>
                                        <td width="170px">{{ explode(" ",$h)[0] }}<br>{{ explode(" ",$h)[1] }}<br>{{ explode(" ",$h)[2] }}</td>
                                        @foreach($dias as $dia)
                                            @php $done = []; @endphp
                                            <td style="vertical-align: middle;" width="180px">                                                
                                                @foreach($turmas as $turma)
                                                    @if($turma->classschedules()->where("diasmnocp",$dia)->where("horent",explode(" ",$h)[0])->where("horsai",explode(" ",$h)[2])->get()->isNotEmpty())
                                                        @if(!$turma->externa)
                                                            <a class="text-dark" target="_blank"
                                                                href="{{'https://uspdigital.usp.br/jupiterweb/obterTurma?nomdis=&sgldis='.$turma->coddis}}"
                                                            >
                                                                {!! $turma->coddis." T.".substr($turma->codtur,-2,2) !!}
                                                            </a>
                                                            <br>
                                                        @elseif(!in_array($turma->id, $done))
                                                            <a class="text-dark" target="_blank"
                                                                href="{{'https://uspdigital.usp.br/jupiterweb/obterTurma?nomdis=&sgldis='.$turma->coddis}}"
                                                            >
                                                                {!! $turma->coddis." " !!}
                                                                @php $coddis = $turma->coddis; @endphp
                                                                @foreach($turmas->filter(function($t)use($coddis){return $t->coddis == $coddis;}) as $turma2)
                                                                    @if($turma2->classschedules()->where("diasmnocp",$dia)->where("horent",explode(" ",$h)[0])->where("horsai",explode(" ",$h)[2])->get()->isNotEmpty())
                                                                        {!! "T.".substr($turma2->codtur,-2,2)." " !!}
                                                                        @php array_push($done, $turma2->id); @endphp
                                                                    @endif
                                                                @endforeach
                                                            </a>
                                                            <br>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </table>
                            <br>                            
                            @php
                                $turmas = $turmas->merge(
                                    App\Models\SchoolClass::whereBelongsTo($schoolterm)
                                        ->whereHas("courseinformations", function($query)use($semestre, $course, $codhab){
                                            $query->where("numsemidl",$semestre)
                                                ->where("codhab", $codhab)
                                                ->where("nomcur",$course->nomcur)
                                                ->where("perhab", $course->perhab)
                                                ->where("tipobg", "L");
                                            })->get()
                                )
                            @endphp
                            <table class="table table-bordered table-striped table-hover">

                                <tr>
                                    <th>Código da Disciplina</th>
                                    <th>Nome da Disciplina</th>
                                    <th>Tipo</th>
                                    <th>Professor(es)</th>
                                    <th>Sala</th>
                                    <th>Turma</th>
                                </tr>

                                    @php $done = []; @endphp
                                    @foreach($turmas as $turma)
                                        @if(!in_array($turma->id, $done))
                                            <tr>
                                                <td>{!! $turma->coddis !!}</td>
                                                <td>
                                                    <a class="text-dark" target="_blank"
                                                        href="{{'https://uspdigital.usp.br/jupiterweb/obterTurma?nomdis=&sgldis='.$turma->coddis}}"
                                                    >
                                                        {!! $turma->nomdis !!}
                                                    </a>
                                                </td>
                                                @php  
                                                    $tipobg = $turma->courseinformations()->select(["codcur","tipobg"])
                                                        ->where("numsemidl",$semestre)
                                                        ->where("nomcur",$course->nomcur)
                                                        ->where("perhab", $course->perhab)
                                                        ->where("codhab", $codhab)
                                                        ->get()->toArray();

                                                    foreach($tipobg as $key=>$value){
                                                        unset($tipobg[$key]["pivot"]);
                                                    }

                                                    $tipobg = array_unique($tipobg, SORT_REGULAR);

                                                    $tipos = ["L"=>"Livre","O"=>"Obrigatória","C"=>"Eletiva"];
                                                @endphp
                                                <td>
                                                    @foreach($tipobg as $t)
                                                        @if($t["codcur"] != $course->codcur)
                                                            @php
                                                                $mostrar_cur_ant = true;
                                                                foreach($tipobg as $t2){
                                                                    if($t["codcur"] != $t2["codcur"] and $t["tipobg"] == $t2["tipobg"]){
                                                                        $mostrar_cur_ant = false;
                                                                    }
                                                                }
                                                            @endphp
                                                            @if($mostrar_cur_ant)
                                                                {!! "Curr. Ant. ".$tipos[$t["tipobg"]] !!}<br>
                                                            @endif
                                                        @else
                                                            @php
                                                                $mostrar_cur_nov = false;
                                                                foreach($tipobg as $t2){
                                                                    if($t["codcur"] != $t2["codcur"] and $t["tipobg"] != $t2["tipobg"]){
                                                                        $mostrar_cur_nov = true;
                                                                    }
                                                                }
                                                            @endphp
                                                            @if($mostrar_cur_nov)
                                                                {!! "Curr. Nov. ".$tipos[$t["tipobg"]] !!}<br>
                                                            @else
                                                                {!! $tipos[$t["tipobg"]] !!}<br>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </td>
                                                <td style="white-space: nowrap;">
                                                    @foreach($turma->instructors as $instructor)
                                                        {{ $instructor->getPronounTreatment() . $instructor->getNomAbrev()}} <br/>
                                                    @endforeach
                                                </td>
                                                <td>
                                                    @if(!$turma->externa)
                                                        @if($turma->fusion()->exists()) 
                                                            {!! $turma->fusion->master->room()->exists() ? $turma->fusion->master->room->nome : "Sem Sala" !!}
                                                        @else
                                                            {!! $turma->room()->exists() ? $turma->room->nome : "Sem Sala" !!}
                                                        @endif
                                                    @else
                                                        Externa
                                                    @endif
                                                </td>
                                                <td>
                                                    @php 
                                                        $coddis = $turma->coddis; 
                                                        $codturs = [];
                                                    @endphp
                                                    @foreach($turmas as $turma2)
                                                        @if(($turma->coddis == $turma2->coddis) and ($turma->instructors->diff($turma2->instructors)->isEmpty()) and ($turma2->instructors->diff($turma->instructors)->isEmpty()))
                                                            @php 
                                                                array_push($done, $turma2->id); 
                                                                array_push($codturs, substr($turma2->codtur,-2,2)); 
                                                            @endphp
                                                        @endif
                                                    @endforeach
                                                    @php sort($codturs); @endphp
                                                    @foreach($codturs as $codtur)
                                                        {!! "T.".$codtur !!}<br>
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tr>
                            </table>
                            <br>                     
                            <br>                     
                        @endif
                    @endforeach
                @endforeach
            @else
                <p class="text-center">Não há turmas cadastradas</p>
                
            @endif
        </div>
    </div>
</div>
@endsection