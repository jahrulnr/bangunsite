<?php

namespace App\Http\Controllers;

use App\Models\Ssh;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SshController extends Controller
{
    public function index()
    {
        $accounts = Ssh::all();

        return view('Ssh.index', compact('accounts'));
    }

    public function add(Request $r)
    {
        $validate = Validator::make($r->all(), [
            'host' => 'string|required',
            'user' => 'string|required',
            'port' => 'integer|nullable',
            'pass' => 'string|required',
        ], [
            'host.required' => 'Host cannot be empty',
            'user.required' => 'User cannot be empty',
            'port.integer' => 'Port must be a valid number',
            'pass.required' => 'Password cannot be empty',
        ]);

        if ($validate->fails()) {
            return back()->with('error', $validate->errors()->first());
        }

        $input = $r->only(
            (new Ssh())->getFillable()
        );

        if (empty($input['port'])) {
            $input['port'] = 22;
        }

        if (Ssh::create($input)) {
            return back()->with('success', 'Ssh account added successfully');
        }

        return back()->with('error', 'Failed to add ssh account');
    }

    public function update(Request $r)
    {
        $model = Ssh::find($r->id);

        if (! $model) {
            return back()->with('error', 'Ssh account does not exists');
        }

        $validate = Validator::make($r->all(), [
            'host' => 'string|required',
            'user' => 'string|required',
            'port' => 'integer|nullable',
            'pass' => 'string|nullable',
        ], [
            'host.required' => 'Host cannot be empty',
            'user.required' => 'User cannot be empty',
            'port.integer' => 'Port must be a valid number',
        ]);

        if ($validate->fails()) {
            return back()->with('error', $validate->errors()->first());
        }

        $input = $r->only(
            (new Ssh())->getFillable()
        );

        if (empty($input['port'])) {
            $input['port'] = 22;
        }

        if (empty($input['pass'])) {
            unset($input['pass']);
        }

        if ($model->update($input)) {
            return back()->with('success', 'Ssh account updated successfully');
        }

        return back()->with('error', 'Failed to update ssh account');
    }

    public function delete($id)
    {
        $model = Ssh::find($id);
        if (! $model) {
            return back()->with('error', 'Ssh account does not exits');
        }

        if ($model->delete()) {
            return back()->with('success', 'Ssh account deleted successfully');
        }

        return back()->with('error', 'Failed to delete Ssh account');
    }

    public function connect(Request $r, $id)
    {
        $data = Ssh::find($id);
        if (! $data) {
            return back()->with('error', 'Ssh account does not exits');
        }

        $encrypt = $data->id.'::'.bcrypt($data->getPass());
        $endpoint = 'wss://'.$r->host().':13999/ssh/connection?key='.$encrypt;

        return view('Ssh.terminal', compact('data', 'endpoint'));
    }
}
