<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;
use App\Models\Requisition;
use App\Models\Reservation;
use App\Models\SchoolTerm;
use App\Models\SchoolClass;

class ProcessReservation implements ShouldQueue, ShouldBeUnique
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
        $this->queueProgress(0);

        $schoolterm = SchoolTerm::getLatest();

        $schoolclasses = SchoolClass::whereBelongsTo($schoolterm)->whereHas("room")->get();

        $t = count($schoolclasses)*2;
        $n = 0;

        foreach($schoolclasses as $schoolclass){
            if(!Reservation::checkAvailability($schoolclass)){
                $this->queueData(["status"=>"failed","schoolclass"=>$schoolclass->toArray(),
                "room"=>$schoolclass->room->nome,"schedules"=>$schoolclass->classschedules->toArray()]);
                return false;
            }

            $n += 1;
            $this->queueProgress(floor($n*100/$t));
        }

        foreach($schoolclasses as $schoolclass){
            $requisition = Requisition::createFromSchoolClass($schoolclass);
            $reservations = Reservation::createFrom($requisition, $schoolclass);

            $n += 1;
            $this->queueProgress(floor($n*100/$t));
        }
        $this->queueProgress(100);
    }
}
