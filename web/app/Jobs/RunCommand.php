<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Process\Process;

class RunCommand implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $timeout = 300;

    private $cmd;

    private $useExecute;

    public function __construct(string $cmd, bool $useExecute = false)
    {
        $this->cmd = $cmd;
        $this->useExecute = $useExecute;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $cmd = $this->cmd;
        if ($this->useExecute) {
            exec($cmd);

            return;
        }

        $process = new Process(explode(' ', $cmd), base_path());
        $process->start();
    }
}
