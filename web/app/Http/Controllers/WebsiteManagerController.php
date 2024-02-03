<?php

namespace App\Http\Controllers;

use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WebsiteManagerController extends Controller
{
    public function index()
    {
        $website = Website::all();

        return view('Website.index', compact('website'));
    }

    public function check(Request $r): string
    {
        $validate = Validator::make([
            'domain' => 'string|required',
            'path' => 'string|required',
        ], [
            'domain.required' => 'Domain not valid',
            'path.required' => 'Path not valid',
        ]);

        if ($validate->fails()) {
            return $validate->errors()->first();
        }

        if (! filter_var($r->domain, FILTER_VALIDATE_DOMAIN)) {
            return 'Domain not valid';
        }

        $path = $r->get('path');
        if (! validatePath($path)) {
            return 'Path have illegal character!';
        }

        $pathExists = Website::where('path', $path)->exists();
        if (! $pathExists) {
            try {
                if (is_file($path)) {
                    return 'Path is file!';
                }
                is_dir($path) ?: mkdir($path, 755, true);

                return 'success';
            } catch (\Exception $e) {
                Log::error($e);

                return 'Create path failed! See logs for details.';
            }
        }

        return 'Path used by other website! Please use another path.';
    }

    public function store(Request $r)
    {
        $validate = $this->check($r);
        if ($validate !== 'success') {
            return back()->with('error', $validate);
        }

        if ($r->name == null) {
            $r->name = $r->domain;
        }
        $website = Website::create($r->only((new Website)->getFillable()));

        if (! $website) {
            return back()->with('error', 'Fail to save website data');
        }

        return back()->with('success', $r->domain.' created');
    }

    public function update(Request $r)
    {

    }
}
