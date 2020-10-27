<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Model\Client;
use App\Model\User;
use App\Traits\Response;
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

    use AuthenticatesUsers, Response;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


    public function userLogin(Request $request){
        $validated = $request->validate([
            'phone_number' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('phone_number', $validated['phone_number'])->where('password', md5($validated['password']))->first();
        if ($user != null){
            $header = $request->header('Authorization');
            if ($header == $user->token){
                return $this->success($user, 'Success', 200);
            }else{
                return $this->error([], 'User Login Failure', 404);
            }
        }else{
            return $this->error([], 'User Login Failure', 404);
        }
    }

    public function clientLogin(Request $request){
        $validated = $request->validate([
            'phone_number' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = Client::where('phone_number', $validated['phone_number'])->where('password', md5($validated['password']))->first();
        if ($user != null){
            return $this->success($user, 'Success', 200);
        }else{
            return $this->error([], 'Client Login Failure', 404);
        }
    }
}
