<?php

namespace App\Http\Controllers;

use App\Libraries\Facades\Cpu;
use App\Libraries\Facades\Disk;
use App\Libraries\Facades\Memory;
use App\Libraries\Facades\Network;
use App\Models\Website;

class HomeController extends Controller
{
    public function index()
    {
        $countSite = Website::count();
        $cpus = Cpu::count();
        $memory = Memory::info();
        $disk = Disk::bytesReadable(disk_total_space('/'));
        $network = Network::traffic();

        return view('Home.index', compact('countSite', 'cpus', 'memory', 'disk', 'network'));
    }
}
