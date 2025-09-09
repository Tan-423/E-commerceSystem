<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Get the post-register / post-login redirect path.
     *
     * @return string
     */
    public function redirectTo()
    {
        if (Auth::check() && Auth::user()->usertype === 'ADM') {
            return '/admin';
        }
        
        return '/';
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // Get user by email
        $user = User::where('email', $request->email)->first();
        
        // Check if the user exists and is locked before attempting authentication
        if ($user && $user->locked) {
            return $this->sendFailedLoginResponse($request, 'Your account has been locked due to too many failed login attempts. Please contact the administrator.');
        }

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            // Reset failed attempts on successful login
            if ($user) {
                $user->resetFailedAttempts();
            }
            
            if ($request->hasSession()) {
                $request->session()->put('auth.password_confirmed_at', time());
            }

            return $this->sendLoginResponse($request);
        }

        // Handle failed login attempt
        if ($user) {
            $user->incrementFailedAttempts();
            
            // Check if user was just locked
            if ($user->locked) {
                return $this->sendFailedLoginResponse($request, 'Your account has been locked due to too many failed login attempts. Please contact the administrator.');
            }
            
            // Show remaining attempts
            $remainingAttempts = 3 - $user->failed_attempts;
            if ($remainingAttempts > 0) {
                return $this->sendFailedLoginResponse($request, "Invalid credentials. You have {$remainingAttempts} attempt(s) remaining before your account is locked.");
            }
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $message
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request, $message = null)
    {
        $message = $message ?: trans('auth.failed');
        
        throw \Illuminate\Validation\ValidationException::withMessages([
            $this->username() => [$message],
        ]);
    }
}
