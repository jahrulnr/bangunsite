<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FlushSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'session:flush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Flush all user sessions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $base = base_path();
        $sessions = glob($base.'/storage/framework/sessions/*');
        foreach ($sessions as $session) {
            if (str_ends_with($session, '.gitignore')) {
                continue;
            }
            unlink($session);
        }
        $this->components->info('Sessions cleared successfully.');
    }
}
