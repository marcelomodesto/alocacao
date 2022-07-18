<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\SchoolTerm;
use App\Models\Priority;
use App\Models\SchoolClass;
use App\Http\Requests\CompatibleRoomRequest;
use App\Http\Requests\AllocateRoomRequest;

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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
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
            if($room->isCompatible($turma)){
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

    public function distributes()
    {
        $schoolterm = SchoolTerm::getLatest();

        foreach(SchoolClass::whereBelongsTo($schoolterm)->get() as $schoolclass){
            $schoolclass->room()->dissociate();
            $schoolclass->save();
        }

        $prioridades = Priority::whereHas("schoolclass", function($query) use($schoolterm) {$query->whereBelongsTo($schoolterm);})
                                ->get()->sortByDesc("priority");

        foreach($prioridades as $prioridade){
            $t1 = $prioridade->schoolclass;
            $room = $prioridade->room;
            if(!$t1->room()->exists()){
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

        $turmas = SchoolClass::whereBelongsTo($schoolterm)
                                ->where("tiptur","Pós Graduação")
                                ->whereDoesntHave("room")->get();
        foreach($turmas as $t1){
            foreach(Room::all()->shuffle() as $sala){
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
            foreach(Room::all()->sortby("assentos") as $sala){
                if(!$t1->room()->exists()){
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
