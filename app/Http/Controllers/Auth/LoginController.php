<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Model\Client;
use App\Model\User;
use App\Model\Movement;
use App\Traits\Response;
use Carbon\Carbon;

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
            'company_token' => 'required|string',
            'at_location' => 'boolean'
        ]);

        $user = User::where('phone_number', $validated['phone_number'])->where('password', md5($validated['password']))->where('company_token', $validated['company_token'])->first();
        if ($user){

            // $header = $request->header('Authorization');
            // if ($header == $user->token){
                // $move = Movement::where('user_id', $user->id)->first();

                // if ($move){
                //     $user->at_location = $validated['at_location'];
                //     $move->login_time = Carbon::now();
                //     $move->save();
                // }else{
                    $move = new Movement();
                    $user->at_location = $validated['at_location'];
                    $move->at_location = $validated['at_location'];
                    $move->login_time = Carbon::now();
                    $move->user_id = $user->id;
                    $move->save();
                // }

                $accessToken = $user->createToken('authToken')->accessToken;
                $data = [
                    "name"=> $user->name,
                    "phone_number"=> $user->phone_number,
                    // "email"=> $user->email,
                    // "city"=> $user->city,
                    // "state"=> $user->state,
                    // "address"=> $user->address,
                    // "token"=> $user->token,
                    "company_token"=> $user->company_token,
                    "at_location"=> $user->at_location,
                    "access_token" => $accessToken
                ];

                return $this->success($data , 'Success', 200);

        }else{
           return $this->error(true, 'Phone Number or Password Incorrect', 400);
        }

    }

    public function userLogout(Request $request){
        $request->validate([
            'company_token' => 'required'
        ]);
        $user = User::where('id', auth()->user()->id)->where('company_token', $request->company_token)->first();
            $move = Movement::where('user_id', $user->id)->where('at_location', true)->first();
            if ($move){
                $move->at_location = false;
                $move->logout_time = Carbon::now();
                $move->save();
            }

            Auth::logout();

            return $this->success(true, "User Successfully logged out", 200);
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
            return $this->error([], 'Client Login Failure', 400);
        }
    }
}
