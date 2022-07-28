<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;
use Ismaelw\LaraTeX\LaraTeX;
use App\Models\SchoolTerm;

class ProcessReport implements ShouldQueue
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

        $file = (new LaraTeX('rooms.reports.latex'))->with(['schoolterm' => SchoolTerm::getLatest(),])->inline('relatorio.pdf');

        $file = $file->getFile();

        $this->queueData(['fileName' => $file->getFilename()]);

        $this->queueProgress(100);
    }
}
