<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Client;
use App\Model\User;
use App\Model\PlateNo;
use App\Traits\Response;

class UserController extends Controller
{

    use Response;

    public function userLogin(){

    }

    public function registerUser(Request $request){
        try{

            $validated = $request->validate([
                'email' => 'required|email|unique',
                'phone_number' => 'required|string',
                'name' => 'required|string',
                'geolocation' => 'required|string',
                'plate_number' => 'required|string'
            ]);

            $user = new User;
            $user->email = $validated['email'];
            $user->phone_number = $validated['phone_number'];
            $user->name = $validated['name'];
            $user->geolocation = $validated['geolocation'];
            $user->save();
            $pl_no = new PlateNo;
            $user->plate_number = $validated['plate_number'];
            $pl_no->users()->save();
        }catch(Exception $e){
            return $this->error($e->getMessage(), 'Error Registering User', 401);
        }

        return $this->success([$user, $pl_no], 'User Registeration Success', 201);
    }

    public function addPlateNumber(Request $request, $id){

        try{
            $validated = $request->validate([
                'plate_number' => 'required|string'
            ]);

            $pl_no = new PlateNo;
            $pl_no->plate_number = $validated['plate_number'];
            $pl_no->users()->save();

        }catch(Exception $e){
            return $this->error($e->getMessage(), 'Error Adding Plate Number', 401);
        }

        return $this->success($user, 'Plate Number Added', 201);
    }

}
