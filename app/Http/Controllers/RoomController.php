<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\SchoolTerm;
use App\Models\Priority;
use App\Models\SchoolClass;

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
                    if($t1->fusion->master->id == $t1->id and $room->nome[0]=="B"){
                        if($room->isCompatible($t1)){
                            $room->schoolclasses()->save($t1);
                        }                        
                    }
                }elseif(($t1->tiptur=="Graduação" and $room->nome[0]=="B") or 
                        ($t1->tiptur=="Pós Graduação" and $room->nome[0]=="A")){
                    if($room->isCompatible($t1)){
                        $room->schoolclasses()->save($t1);
                    }
                }
            }
        }

        $turmas = SchoolClass::where("tiptur","Pós Graduação")->whereDoesntHave("room")->get();
        foreach($turmas as $t1){
            foreach(Room::all()->shuffle() as $sala){
                if(!$t1->room()->exists() and !$t1->fusion()->exists()){
                    if($t1->tiptur=="Pós Graduação" and $sala->nome[0]=="A"){
                        if($sala->isCompatible($t1)){
                            $sala->schoolclasses()->save($t1);
                        }
                    }
                }
            }
        }
        return redirect("/rooms");
    }
}
