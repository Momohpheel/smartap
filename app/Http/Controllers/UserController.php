<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Client;
use App\Model\User;
use App\Traits\Response;

class UserController extends Controller
{

    use Response;

    public function registerUser(Request $request){
        try{

            $validated = $request->validate([
                'plate_number' => 'required|string',
                'phone_number' => 'required|string',
                'name' => 'required|string',
                'geolocation' => 'required|string',
            ]);

            $user = new User;
            $user->plate_number = $validated['plate_number'];
            $user->phone_number = $validated['phone_number'];
            $user->name = $validated['name'];
            $user->geolocation = $validated['geolocation'];


        }catch(Exception $e){
            return $this->error($e->getMessage(), 'Error Registering User', 401);
        }

        return $this->success($user, 'User Registeration Success', 201);
    }

}
