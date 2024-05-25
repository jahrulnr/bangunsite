<?php

namespace App\Libraries;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SQLite
{
    protected $path;

    public function __construct()
    {
        $this->path = env('DB_DATABASE');
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getTables(): array
    {
        $list = DB::select('SELECT name FROM sqlite_master WHERE type = "table"');

        return array_column($list, 'name');
    }

    public function getCols(string $name): array
    {
        return Schema::getColumnListing($name);
    }

    public function getRows(string $table, $limit = 100): \Illuminate\Support\Collection
    {
        $get = DB::table($table)->limit($limit)->get();

        return $get;
    }
}
