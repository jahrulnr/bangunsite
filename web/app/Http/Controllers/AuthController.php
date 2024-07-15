<?php

namespace App\Http\Controllers;

use App\Mail\Notification;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
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
        $executed = RateLimiter::attempt(
            'login-invalid:'.$request->ip(),
            5,
            function () use ($request) {
                header('Hit: '.RateLimiter::attempts('login-invalid:'.$request->ip()));
            },
            60,
        );

        if (! $executed) {
            $wait = RateLimiter::availableIn('login-invalid:'.$request->ip());

            return back()->with('error', 'Too many request! You may try again in '.$wait.' seconds.');
        }

        $input = $request->only('email', 'password');
        $session = Session::get('id', [0 => '-1']);
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

            if (env('MAIL_NOTIFICATION', 'true') == 'true') {
                try {
                    Mail::to(Auth::user()->email)->send(new Notification([
                        'title' => 'New Login Notification',
                        'subject' => 'Someone logged in to your account',
                        'body' => 'This is a notification that someone has logged in to your account at '.now()->format('F j, Y H:i').'. If this was not you, please change your password immediately.',
                    ]));
                } catch (Exception $e) {
                    Log::emergency($e->getMessage());
                }
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
