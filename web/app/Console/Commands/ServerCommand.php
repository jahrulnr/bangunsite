<?php

namespace App\Console\Commands;

use Illuminate\Foundation\Console\ServeCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Process\PhpExecutableFinder;

#[AsCommand(name: 'server')]
class ServerCommand extends ServeCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'server';

    /**
     * Execute the console command.
     *
     * @return int
     *
     * @throws \Exception
     */
    public function handle()
    {
        // create default web for port 80/443
        $webPath = env('WEB_PATH');
        if (! is_dir($webPath)) {
            exec("mkdir -p '{$webPath}/default'");
            copy('/storage/webconfig/index.html', $webPath.'/default/index.html');
            copy('/storage/webconfig/healty.php', $webPath.'/default/healty.php');
            exec("chown -R apps:apps '{$webPath}'");
        }

        $environmentFile = $this->option('env')
                            ? base_path('.env').'.'.$this->option('env')
                            : base_path('.env');
        $iniFile = '/storage/webconfig/app.ini';

        $hasEnvironment = file_exists($environmentFile);
        $hasIni = file_exists($iniFile);

        $environmentLastModified = $hasEnvironment
                            ? filemtime($environmentFile)
                            : now()->addDays(30)->getTimestamp();
        $iniLastModified = $hasIni
                            ? filemtime($iniFile)
                            : now()->addDays(30)->getTimestamp();

        $process = $this->startProcess($hasEnvironment);

        while ($process->isRunning()) {
            if ($hasEnvironment || $hasIni) {
                clearstatcache(false, $environmentFile);
                clearstatcache(false, $iniFile);
            }

            if (! $this->option('no-reload') &&
                $hasEnvironment &&
                (filemtime($environmentFile) > $environmentLastModified ||
                filemtime($iniFile) > $iniLastModified)) {
                $environmentLastModified = filemtime($environmentFile);
                $iniLastModified = filemtime($iniFile);

                $this->newLine();

                $this->components->info('app.ini or .env changes detected. Restarting server...');

                $process->stop(5);

                $this->serverRunningHasBeenDisplayed = false;

                $process = $this->startProcess($hasEnvironment);
            }

            sleep(30);
        }

        $status = $process->getExitCode();

        if ($status && $this->canTryAnotherPort()) {
            $this->portOffset += 1;

            return $this->handle();
        }

        return $status;
    }

    /**
     * Get the full server command.
     *
     * @return array
     */
    protected function serverCommand()
    {
        $server = file_exists(base_path('server.php'))
            ? base_path('server.php')
            : base_path('vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php');

        return [
            (new PhpExecutableFinder())->find(false),
            '-c',
            '/storage/webconfig/app.ini',
            '-S',
            $this->host().':'.$this->port(),
            $server,
        ];
    }
}
