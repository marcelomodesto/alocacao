<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use romanzipp\QueueMonitor\Models\Monitor;
use Illuminate\Support\Facades\Auth;

class MonitorController extends Controller
{
    public function getImportProcess()
    {
        if(!Auth::check() or !Auth::user()->hasRole(["Administrador", "Operador"])){
            abort(403);
        }

        $max_id = Monitor::where(['name'=>'App\Jobs\ProcessImportSchoolClasses'])->max('id');
        $max_progress = Monitor::where(['id'=>$max_id])->max('progress');
        return response()->json(Monitor::where(['id'=>$max_id, 
                                                'progress'=>$max_progress])
                                        ->first());
    }

    public function getReportProcess()
    {
        if(!Auth::check() or !Auth::user()->hasRole(["Administrador", "Operador"])){
            abort(403);
        }

        $max_id = Monitor::where(['name'=>'App\Jobs\ProcessReport'])->max('id');
        $max_progress = Monitor::where(['id'=>$max_id])->max('progress');
        return response()->json(Monitor::where(['id'=>$max_id, 
                                                'progress'=>$max_progress])
                                        ->first());
    }

    public function getReservationProcess()
    {
        if(!Auth::check() or !Auth::user()->hasRole(["Administrador", "Operador"])){
            abort(403);
        }

        $max_id = Monitor::where(['name'=>'App\Jobs\ProcessReservation'])->max('id');
        $max_progress = Monitor::where(['id'=>$max_id])->max('progress');
        return response()->json(Monitor::where(['id'=>$max_id, 
                                                'progress'=>$max_progress])
                                        ->first());
    }
}
