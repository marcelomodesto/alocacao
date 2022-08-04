<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Requisition;
use App\Models\Reservation;
use App\Models\SchoolTerm;
use App\Models\SchoolClass;

class ProcessReservation implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public $timeout = 999;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $schoolterm = SchoolTerm::getLatest();

        $schoolclasses = SchoolClass::whereBelongsTo($schoolterm)->whereHas("room")->get();

        foreach($schoolclasses as $schoolclass){
            $requisition = Requisition::createFromSchoolClass($schoolclass);
            $reservations = Reservation::createFrom($requisition, $schoolclass);
        }
    }
}
