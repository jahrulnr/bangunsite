<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ssh;
use Illuminate\Http\Request;

class SshController extends Controller
{
    public function validate(Request $r)
    {
        $data = Ssh::findByKey($r->key);
        if ($data) {
            return response()->json([
                'host' => $data->host,
                'port' => (string) $data->port,
                'user' => $data->user,
                'pass' => $data->getPass(),
            ]);
        }

        return response()->json([
            'status' => 'invalid key',
        ], 401);
    }
}
