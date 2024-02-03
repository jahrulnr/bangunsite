<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if (auth()->check()) {
            return redirect(route('home'));
        }

        return view('login');
    }

    public function auth(Request $request): RedirectResponse
    {
        $input = $request->only('email', 'password');
        $session = Session::get('id');
        $input['email'] = $request->email ?: User::find($session[0])?->email;
        $validator = Validator::make($input, [
            'email' => 'email|required',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return back()->with('error', 'Email/Password invalid!');
        }

        if (Auth::attempt($input, $request->remember)) {
            if ($session !== null) {
                return redirect(
                    getReferer()
                );
            }

            return redirect(
                route('home')
            );
        }

        return back()->with('error', 'Email/Password invalid!');
    }

    public function lockscreen()
    {
        $id = auth()->user()?->id ?: Session::get('id');
        if (! auth()->check() && ! $id) {
            return redirect(route('login'));
        } elseif (auth()->check()) {
            Auth::logout();
        }

        $user = User::find($id);
        if (is_array($id)) {
            $user = $user->first();
        }
        saveReferer();
        Session::push('id', $id);

        return view('lockscreen', compact('user'));
    }
}
