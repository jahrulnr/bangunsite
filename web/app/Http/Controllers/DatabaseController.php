<?php

namespace App\Http\Controllers;

use App\Libraries\Facades\SQLite;
use Illuminate\Http\Request;

class DatabaseController extends Controller
{
    public function index()
    {
        $tables = SQLite::getTables();
        $dbPath = SQLite::getPath();

        return view('Database.index', compact('tables', 'dbPath'));
    }

    public function show($name, Request $r)
    {
        $limit = (int) ($r->limit ?: 100);
        $cols = SQLite::getCols($name);
        $rows = SQLite::getRows($name, $limit);

        return view('Database.show', compact('name', 'cols', 'rows'));
    }
}
