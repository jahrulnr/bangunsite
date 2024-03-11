<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cronjob extends Model
{
    protected $fillable = [
        'name',
        'payload',
        'run_every',
        'executed_at',
    ];
}
