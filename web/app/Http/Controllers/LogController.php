<?php

namespace App\Http\Controllers;

use App\Libraries\Facades\Log;
use App\Models\Website;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index()
    {
        $sites = Website::orderBy('name', 'asc')->get();

        return view('Log.index', compact('sites'));
    }

    public function getLog(Request $r)
    {
        $logType = $r->type == 'accesslog' ? 'access' : 'error';

        if ($r->domain != 'default') {
            $log = Log::readLog($logType.'-'.$r->domain.'.log', 1000);
        } else {
            $log = Log::readLog($logType.'.log', 1000);
        }

        return $log;
    }
}
