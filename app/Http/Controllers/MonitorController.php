<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use romanzipp\QueueMonitor\Models\Monitor;

class MonitorController extends Controller
{
    public function getImportProcess()
    {
        $max_id = Monitor::where(['name'=>'App\Jobs\ProcessImportSchoolClasses'])->max('id');
        $max_progress = Monitor::where(['id'=>$max_id])->max('progress');
        return response()->json(Monitor::where(['id'=>$max_id, 
                                                'progress'=>$max_progress])
                                        ->first());
    }

    public function getReportProcess()
    {
        $max_id = Monitor::where(['name'=>'App\Jobs\ProcessReport'])->max('id');
        $max_progress = Monitor::where(['id'=>$max_id])->max('progress');
        return response()->json(Monitor::where(['id'=>$max_id, 
                                                'progress'=>$max_progress])
                                        ->first());
    }
}
