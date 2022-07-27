<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;
use App\Models\SchoolTerm;
use App\Models\SchoolClass;
use App\Models\Instructor;
use App\Models\ClassSchedule;
use App\Models\Priority;
use App\Models\Room;
use App\Models\CourseInformation;
use App\Models\Fusion;

class ProcessImportSchoolClasses implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function progressCooldown(): int
    {
        return 1; 
    }

    public $timeout = 999;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->queueProgress(1);

        $schoolterm = SchoolTerm::getLatest();

        $turmas = SchoolClass::getFromReplicadoBySchoolTerm($schoolterm);

        $this->queueProgress(10);
        $t = count($turmas);
        $n = 0;

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
            $n += 1;
            $this->queueProgress(10 + floor($n*70/$t));
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

    $this->queueProgress(100);
    }
}
