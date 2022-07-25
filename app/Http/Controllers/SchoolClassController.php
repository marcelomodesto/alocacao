<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolClassRequest;
use App\Http\Requests\UpdateSchoolClassRequest;
use App\Http\Requests\IndexSchoolClassRequest;
use App\Http\Requests\CreateSchoolClassRequest;
use App\Models\SchoolClass;
use App\Models\SchoolTerm;
use App\Models\Instructor;
use App\Models\ClassSchedule;
use App\Models\Priority;
use App\Models\Fusion;
use App\Models\Room;
use App\Models\CourseInformation;

class SchoolClassController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(IndexSchoolClassRequest $request)
    {
        $schoolterm = SchoolTerm::getLatest();

        $turmas = $schoolterm ? SchoolClass::whereBelongsTo($schoolterm)->where("externa", "Não")->get() : [];

        return view('schoolclasses.index', compact(['turmas', 'schoolterm']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $turma = new SchoolClass;
        $schoolTerm = SchoolTerm::getLatest();
        $turma->schoolterm()->associate($schoolTerm);

        return view('schoolclasses.create', compact('turma'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSchoolClassRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSchoolClassRequest $request)
    {

        $validated = $request->validated();
        $schoolTerm = SchoolTerm::find($validated["periodoId"]);
        $schoolclass = SchoolClass::where(array_intersect_key($validated, array_flip(array('codtur', 'coddis'))))->first();

        if(!$schoolclass){
            $schoolclass = new SchoolClass;

            $schoolclass->fill($validated);

            $schoolclass->schoolterm()->associate($schoolTerm);
            $schoolclass->save();

            if(array_key_exists('instrutores', $validated)){
                foreach($validated['instrutores'] as $instructor){
                    $schoolclass->instructors()->attach(Instructor::firstOrCreate(Instructor::getFromReplicadoByCodpes($instructor['codpes'])));
                }
            }   

            if(array_key_exists('horarios', $validated)){
                foreach($validated['horarios'] as $classSchedule){
                    $schoolclass->classschedules()->attach(ClassSchedule::firstOrCreate($classSchedule));
                }
            }
            $schoolclass->save();
        }else{
            Session::flash("alert-warning", "Já existe uma turma cadastrada com esse código de turma e código de disciplina");
            return back();
        }

        return redirect('/schoolclasses');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SchoolClass  $schoolClass
     * @return \Illuminate\Http\Response
     */
    public function show(SchoolClass $schoolClass)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SchoolClass  $schoolClass
     * @return \Illuminate\Http\Response
     */
    public function edit(SchoolClass $schoolclass)
    {

        $turma = $schoolclass;

        return view('schoolclasses.edit', compact('turma'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSchoolClassRequest  $request
     * @param  \App\Models\SchoolClass  $schoolClass
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSchoolClassRequest $request, SchoolClass $schoolclass)
    {
        $validated = $request->validated();

        $schoolclass->instructors()->detach();
        if(array_key_exists('instrutores', $validated)){
            foreach($validated['instrutores'] as $instructor){
                $schoolclass->instructors()->attach(Instructor::firstOrCreate(Instructor::getFromReplicadoByCodpes($instructor['codpes'])));
            }
        }

        $schoolclass->classschedules()->detach();
        if(array_key_exists('horarios', $validated)){
            foreach($validated['horarios'] as $classSchedule){
                $schoolclass->classschedules()->attach(ClassSchedule::firstOrCreate($classSchedule));
            }
        }

        $schoolclass->update($validated);

        return redirect('/schoolclasses');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SchoolClass  $schoolClass
     * @return \Illuminate\Http\Response
     */
    public function destroy(SchoolClass $schoolclass)
    {
        $schoolclass->instructors()->detach();
        $schoolclass->classschedules()->detach();
        $schoolclass->courseinformations()->detach();
        $schoolclass->delete();

        return back();
    }

    public function externals()
    {
        $schoolterm = SchoolTerm::getLatest();

        $turmas = $schoolterm ? SchoolClass::whereBelongsTo($schoolterm)->where("externa", "Sim")->get() : [];

        return view('schoolclasses.externals', compact(['turmas', 'schoolterm']));
    }

    public function import()
    {
        $schoolterm = SchoolTerm::getLatest();

        $turmas = SchoolClass::getFromReplicadoBySchoolTerm($schoolterm);

        foreach($turmas as $turma){
            if ((($turma['tiptur'] == "Pós Graduação") or 
                ($turma['tiptur'] == "Graduação" and substr($turma["codtur"], -2, 2) >= "40") or
                ($turma['tiptur'] == "Graduação" and $turma["coddis"] == "MAE0116")) and
                ($turma["nomdis"] != "Trabalho de Formatura")){
                if($turma['class_schedules']){
                    $schoolclass = SchoolClass::where(array_intersect_key($turma, array_flip(array('codtur', 'coddis'))))->first();
        
                    if(!$schoolclass){
                        $schoolclass = new SchoolClass;
                        $schoolclass->fill($turma);
                        $schoolclass->save();
                
                        foreach($turma['instructors'] as $instructor){
                            if($instructor){
                                $schoolclass->instructors()->attach(Instructor::firstOrCreate(Instructor::getFromReplicadoByCodpes($instructor["codpes"])));
                            }
                        }
            
                        foreach($turma['class_schedules'] as $classSchedule){
                            $schoolclass->classschedules()->attach(ClassSchedule::firstOrCreate($classSchedule));
                        }

                        $priorities = Priority::$priorities_by_course;

                        if(in_array($schoolclass->coddis,array_keys($priorities))){
                            foreach($priorities[$schoolclass->coddis] as $codtur=>$salas){
                                if($codtur == substr($schoolclass->codtur, -2, 2) and $schoolclass->tiptur == "Graduação"){
                                    foreach($salas as $room_name=>$priority){
                                        if($priority > 20){
                                            $room = Room::where("nome", $room_name)->first();
                                            if($room){
                                                Priority::updateOrCreate(
                                                    ["room_id"=>$room->id,"school_class_id"=>$schoolclass->id],
                                                    ["priority"=>$priority]
                                                );
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        foreach(CourseInformation::getFromReplicadoBySchoolClass($schoolclass) as $info){
                            CourseInformation::firstOrCreate($info)->schoolclasses()->save($schoolclass);
                        }

                        $schoolclass->calcEstimadedEnrollment();
                        
                        $schoolclass->save();
                    }
                }
            }
        }

        $docentes = Instructor::whereHas("schoolclasses", function ($query) use($schoolterm){
                                    $query->whereBelongsTo($schoolterm);
                                })->withCount("schoolclasses")->having("schoolclasses_count",">",1)->get();

        $conflicts = [];
        
        foreach($docentes as $docente){
            $done = [];
            foreach($docente->schoolclasses as $t1){
                $conflicts[$t1->id] = [];
                array_push($done, $t1->id);
                foreach($docente->schoolclasses()->whereNotIn("id", $done)->get() as $t2){
                    if($t1->isInConflict($t2) and $t1->instructors->diff($t2->instructors)->isEmpty() and $t2->instructors->diff($t1->instructors)->isEmpty()){
                        array_push($conflicts[$t1->id], $t2->id);
                    }
                }
                if(!$conflicts[$t1->id]){
                    unset($conflicts[$t1->id]);
                }
            }
        }

        foreach($conflicts as $key=>$value){
            foreach($value as $id){
                if(in_array($id, array_keys($conflicts))){
                    unset($conflicts[$id]);
                }
            }
        }

        foreach($conflicts as $key=>$value){
            $t1 = SchoolClass::find($key); 
            $fusion = new Fusion; 
            $fusion->master()->associate($t1); 
            $fusion->save(); 
            $t1->fusion()->associate($fusion); 
            $t1->save();  
            foreach($value as $id){
                $t2 = SchoolClass::find($id); 
                $t2->fusion()->associate($fusion); 
                $t2->save();
            }
        }

        return redirect('/schoolclasses');
    }
}
