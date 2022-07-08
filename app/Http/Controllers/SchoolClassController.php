<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolClassRequest;
use App\Http\Requests\UpdateSchoolClassRequest;
use App\Http\Requests\IndexSchoolClassRequest;
use App\Http\Requests\ImportSchoolClassRequest;
use App\Http\Requests\CreateSchoolClassRequest;
use App\Models\SchoolClass;
use App\Models\SchoolTerm;
use App\Models\Instructor;
use App\Models\ClassSchedule;

class SchoolClassController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(IndexSchoolClassRequest $request)
    {
        $validated = $request->validated();

        if(isset($validated['periodoId'])){
            $schoolterm = SchoolTerm::find($validated['periodoId']);
        }else{
            $schoolterm = SchoolTerm::getCurrentSchoolTerm();
        }

        $turmas = $schoolterm ? SchoolClass::whereBelongsTo($schoolterm)->get() : [];

        return view('schoolclasses.index', compact(['turmas', 'schoolterm']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(CreateSchoolClassRequest $request)
    {
        $validated = $request->validated();

        $turma = new SchoolClass;
        $schoolTerm = SchoolTerm::find($validated["periodoId"]);
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
        $schoolclass->delete();

        return redirect('/schoolclasses');
    }

    public function import(ImportSchoolClassRequest $request)
    {
        $validated = $request->validated();
        $schoolTerm = SchoolTerm::find($validated["periodoId"]);

        $turmas = SchoolClass::getFromReplicadoBySchoolTerm($schoolTerm);

        foreach($turmas as $turma){
            $schoolclass = SchoolClass::where(array_intersect_key($turma, array_flip(array('codtur', 'coddis'))))->first();

            if(!$schoolclass){
                $schoolclass = new SchoolClass;
                $schoolclass->fill($turma);
                $schoolclass->save();
        
                foreach($turma['instructors'] as $instructor){
                    $schoolclass->instructors()->attach(Instructor::firstOrCreate(Instructor::getFromReplicadoByCodpes($instructor["codpes"])));
                }
    
                foreach($turma['class_schedules'] as $classSchedule){
                    $schoolclass->classschedules()->attach(ClassSchedule::firstOrCreate($classSchedule));
                }
                $schoolclass->save();
            }
        }
        
        return redirect('/schoolclasses');
    }
}
