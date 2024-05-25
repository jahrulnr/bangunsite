<?php

namespace App\Console\Commands;

use App\Jobs\RunCommand;
use App\Libraries\Commander;
use App\Models\Cronjob;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class CronJobs extends Command
{
    protected $signature = 'run:cronjobs';

    protected $description = 'Run CronJobs';

    private function cronList(): array
    {
        $crons = Cronjob::all(['id', 'payload', 'run_every', 'executed_at']);
        $run = [];
        foreach ($crons as $cron) {
            $run[$cron->run_every][] = $cron;
        }

        return $run;
    }

    public function handle()
    {
        $process = new Process(['artisan', 'queue:work', '--sleep=3', '--tries=3'], base_path());
        $process->start($this->handleOutput());
        $cronScript = base_path('app/Console/Commands/CronJobs.php');
        $lastUpdate = filemtime($cronScript);

        $min = date('i', time());
        $hour = date('H', time());
        $day = date('d', time());
        $month = date('m', time());

        $this->components->info('CronJobs Runned');
        while ($process->isRunning()) {
            if ($lastUpdate) {
                clearstatcache(false, $cronScript);
            }

            $cronjobs = $this->cronList();
            $checkMin = date('i', time());
            $checkHour = date('H', time());
            $checkDay = date('d', time());
            $checkMonth = date('m', time());

            if (isset($cronjobs['min']) && $checkMin != $min) {
                $min = $checkMin;
                foreach ($cronjobs['min'] as $run) {
                    $this->dispatchCommand($run);
                }
            }
            if (isset($cronjobs['hour']) && $checkHour != $hour) {
                $hour = $checkHour;
                foreach ($cronjobs['hour'] as $run) {
                    $this->dispatchCommand($run);
                }
            }
            if (isset($cronjobs['day']) && $checkDay != $day) {
                $day = $checkDay;
                foreach ($cronjobs['day'] as $run) {
                    $this->dispatchCommand($run);
                }
            }
            if (isset($cronjobs['month']) && $checkMonth != $month) {
                $month = $checkMonth;
                foreach ($cronjobs['month'] as $run) {
                    $this->dispatchCommand($run);
                }
            }

            $checkUpdate = filemtime($cronScript);
            if ($lastUpdate < $checkUpdate) {
                $lastUpdate = $checkUpdate;
                $this->components->info('CronJobs has been updated. Restarting CronJobs...');
                $process->stop(5);
                sleep(5);
                // $process->start($this->handleOutput());
                Commander::exec('supervisorctl restart crond');
            }

            sleep(5);
        }

        return $process->getExitCode();
    }

    public function handleOutput()
    {
        return fn ($type, $buffer) => str($buffer)->explode("\n")->each(function ($line) {
            $this->comment($line);
        });
    }

    private function dispatchCommand(string|Cronjob $run)
    {
        if ($run instanceof Cronjob) {
            $run->update([
                'executed_at' => Carbon::now(getenv('TZ')),
            ]);
            dispatch(new RunCommand($run->payload));
        } else {
            dispatch(new RunCommand($run));
        }
    }
}
