<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\SchoolTerm;
use App\Models\Priority;
use App\Models\SchoolClass;
use App\Models\CourseInformation;
use App\Http\Requests\CompatibleRoomRequest;
use App\Http\Requests\AllocateRoomRequest;
use App\Http\Requests\DistributesRoomRequest;
use Ismaelw\LaraTeX\LaraTeX;
use App\Jobs\ProcessReport;
use romanzipp\QueueMonitor\Models\Monitor;
use Illuminate\Support\Facades\Storage;


class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $salas = Room::all();

        return view('rooms.index', compact(['salas']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function show(Room $room)
    {
        return view('rooms.show', compact(['room']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function edit(Room $room)
    {
        //
    }

    /**CourseInformation
     */
    public function update(Request $request, Room $room)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function destroy(Room $room)
    {
        //
    }

    public function dissociate(SchoolClass $schoolclass)
    {
        $schoolclass->room()->dissociate();
        $schoolclass->save();

        return back();
    }

    public function compatible(CompatibleRoomRequest $request)
    {
        $validated = $request->validated();

        $room = Room::find($validated["room_id"]);

        $st = SchoolTerm::getLatest();

        $res = [];

        $turmas = SchoolClass::whereBelongsTo($st)->whereDoesntHave("room")->whereDoesntHave("fusion")
                    ->union(SchoolClass::whereExists(function($query){
                        $query->from("fusions")->whereColumn("fusions.master_id","school_classes.id");
                    })->whereBelongsTo($st)->whereDoesntHave("room"))->get();

        foreach($turmas as $turma){
            if($room->isCompatible($turma, $ignore_block=true, $ignore_estmtr=true)){
                array_push($res, $turma);
            }
        }

        return response()->json(json_encode($res));
    }

    public function allocate(AllocateRoomRequest $request, Room $room)
    {
        $validated = $request->validated();

        $room->schoolclasses()->save(SchoolClass::find($validated["school_class_id"]));

        return back();
    }

    public function makeReport()
    {
        ProcessReport::dispatch();

        return back();
    }

    public function downloadReport()
    {
        $job = Monitor::where("name","App\Jobs\ProcessReport")->latest("started_at")->first();

        $file = json_decode($job->data)->fileName;

        $job->delete();

        return Storage::download($file);
    }

    public function distributes(DistributesRoomRequest $request)
    {
        $validated = $request->validated();
        
        $salas_diposniveis = $validated["rooms_id"];

        $schoolterm = SchoolTerm::getLatest();

        foreach(SchoolClass::whereBelongsTo($schoolterm)->get() as $schoolclass){
            $schoolclass->room()->dissociate();
            $schoolclass->save();
        }

        foreach(CourseInformation::$codtur_by_course as $sufixo_codtur=>$course){
            $turmas = SchoolClass::whereBelongsTo($schoolterm)->whereHas("courseinformations", function($query)use($course){
                                        $query->whereIn("numsemidl",[1,2])->where("tipobg","O")->where("nomcur", $course["nomcur"]);
                                    })->where("codtur","like","%".$sufixo_codtur)->get();
                                    
            $ps = Priority::whereHas("schoolclass",function($query)use($turmas){
                                $query->whereIn("id",$turmas->pluck("id")->toArray());
                            })->with("room")->get();

            $salas = [];
            foreach($ps->groupBy("room.id") as $sala_id=>$p){
                $salas[$p->sum("priority")] = Room::find($sala_id);
            }
            krsort($salas);
            
            $salas = $salas ? $salas : Room::all()->sortby("assentos");

            $alocado = false;
            foreach($salas as $room){
                if(!$alocado){
                    if(in_array($room->id, $salas_diposniveis)){
                        $conflito = false;
                        foreach($turmas as $turma){
                            if(!$room->isCompatible($turma)){
                                $conflito = true;
                            }
                        }
                        if(!$conflito){
                            foreach($turmas as $turma){
                                $room->schoolclasses()->save($turma);
                            }
                            $alocado = true;
                        }
                    }
                }
            }
        }

        $prioridades = Priority::whereHas("schoolclass", function($query) use($schoolterm) {$query->whereBelongsTo($schoolterm);})
                                ->get()->sortByDesc("priority");

        foreach($prioridades as $prioridade){
            $t1 = $prioridade->schoolclass;
            $room = $prioridade->room;
            if(in_array($room->id, $salas_diposniveis)){
                if(!$t1->room()->exists() and $t1->coddis!="MAE0116"){
                    if($t1->fusion()->exists()){
                        if($t1->fusion->master->id == $t1->id){
                            if($room->isCompatible($t1)){
                                $room->schoolclasses()->save($t1);
                            }                        
                        }
                    }elseif($room->isCompatible($t1)){
                        $room->schoolclasses()->save($t1);
                    }
                }
            }
        }

        $turmas = SchoolClass::whereBelongsTo($schoolterm)
                                ->where("tiptur","Pós Graduação")
                                ->whereDoesntHave("room")->get();
        foreach($turmas as $t1){
            foreach(Room::whereIn("id", $salas_diposniveis)->get()->shuffle() as $sala){
                if(!$t1->room()->exists() and !$t1->fusion()->exists()){
                    if($sala->isCompatible($t1)){
                        $sala->schoolclasses()->save($t1);
                    }
                }
            }
        }

        $turmas = SchoolClass::whereBelongsTo($schoolterm)
                                ->where("tiptur","Graduação")
                                ->whereNotNull("estmtr")
                                ->whereDoesntHave("room")
                                ->get()->sortBy("estmtr");
        foreach($turmas as $t1){
            foreach(Room::whereIn("id", $salas_diposniveis)->get()->sortby("assentos") as $sala){
                if(!$t1->room()->exists() and $t1->coddis!="MAE0116"){
                    if($t1->fusion()->exists()){
                        if($t1->fusion->master->id == $t1->id){
                            if($sala->isCompatible($t1)){
                                $sala->schoolclasses()->save($t1);
                            }                        
                        }
                    }elseif($sala->isCompatible($t1)){
                        $sala->schoolclasses()->save($t1);
                    }
                }
            }
        }

        return redirect("/rooms");
    }
}
