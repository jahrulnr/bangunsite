<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ssh extends Model
{
    protected $table = 'ssh';

    protected $fillable = [
        'host',
        'port',
        'user',
        'pass',
    ];

    protected $hidden = [
        'pass',
    ];

    protected $casts = [
        'pass' => 'encrypted',
    ];

    public function getPass()
    {
        return $this->pass;
    }

    public function scopeFindByKey($builder, string $key)
    {
        $parse = explode('::', $key);
        $model = $this->find($parse[0]);
        if (! $model) {
            return false;
        }

        if (password_verify($model->getPass(), $parse[1])) {
            return $model;
        }

        return false;
    }
}
