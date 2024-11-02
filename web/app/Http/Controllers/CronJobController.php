<?php

namespace App\Http\Controllers;

use App\Jobs\RunCommand;
use App\Libraries\Commander;
use App\Libraries\Facades\Disk;
use App\Models\Cronjob;
use Carbon\Carbon;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CronJobController extends Controller
{
    public function index()
    {
        $crons = Cronjob::orderBy('name')->get();

        return view('CronJob.index', compact('crons'));
    }

    public function store(Request $r)
    {
        $validate = Validator::make($r->all(), [
            'name' => 'string|required',
            'payload' => 'string|required',
            'run_every' => 'string|required',
        ]);

        if ($validate->fails()) {
            return back()->with('error', 'Request not valid.');
        }

        if (Cronjob::create(
            $r->only((new Cronjob())->getFillable())
        )) {
            return back()->with('success', 'Cron created successfully');
        }

        return back()->with('error', 'Create cron failed');
    }

    public function update($id, Request $r)
    {
        $cron = Cronjob::find($id);
        if (! $cron) {
            return back()->with('error', "Cron doesn't exists");
        }

        $validate = Validator::make($r->all(), [
            'name' => 'string|required',
            'payload' => 'string|required',
            'run_every' => 'string|required',
        ]);

        if ($validate->fails()) {
            return back()->with('error', 'Request not valid.');
        }

        if ($cron->update(
            $r->only((new Cronjob())->getFillable())
        )) {
            return back()->with('success', 'Cron updated successfully');
        }

        return back()->with('error', 'Update cron failed');
    }

    public function destroy($id)
    {
        $cron = Cronjob::find($id);
        if (! $cron) {
            return back()->with('error', "Cron doesn't exists");
        }

        if ($cron->delete()) {
            return back()->with('success', 'Cron deleted successfully');
        }

        return back()->with('error', 'Delete cron failed');
    }

    public function run($id, Request $r)
    {
        $cron = Cronjob::find($id);
        if (! $cron) {
            return back()->with('error', "Site doesn't exists");
        }

        $scriptName = '/tmp/execute-'.$id.'.sh';
        $outputName = '/tmp/execute-'.$id.'.log';

        if ($r->start == 'true') {
            ! file_exists($outputName) ?: unlink($outputName);
            Disk::createFile($scriptName, <<<BASH
                #!/bin/bash
                echo "-- Task Start --"
                {$cron->payload}
                echo "-- Task Done --"
                rm $scriptName
            BASH);
            Commander::exec("chmod +x $scriptName");
            dispatch((new RunCommand("{$scriptName} > {$outputName}", true)));

            $cron->update([
                'executed_at' => Carbon::now(getenv('TZ')),
            ]);
        }

        if (file_exists($outputName)) {
            $read = '';
            try {
                $read = file_get_contents($outputName);
            } catch (Error $e) {
                Log::emergency($e->getMessage());
                $read = $e->getMessage().PHP_EOL.'-- Task Done --';
            }
        } else {
            return 'Waiting task run on queue';
        }

        return $read;
    }
}
