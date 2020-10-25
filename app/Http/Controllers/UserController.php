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



    public function registerUser(Request $request){
        try{

            $validated = $request->validate([
                'password' => 'required|string',
                'phone_number' => 'required|string',
                'name' => 'required|string',
                'geolocation' => 'required|string',
                'plate_number' => 'required|string',
                'password' => 'required|string|min:8',
            ]);

            $user = new User;

            $user->phone_number = $validated['phone_number'];
            $user->name = $validated['name'];
            $user->geolocation = $validated['geolocation'];
            $user->password = md5($data['password']);
            $user->save();

            $pl_no = new PlateNo;
            $pl_no->plate_number = $validated['plate_number'];
            $pl_no->user_id = $user->id;
            $pl_no->is_active = true;
            $pl_no->save();
        }catch(Exception $e){
            return $this->error($e->getMessage(), 'Error Registering User', 401);
        }

        return $this->success([$user, $pl_no], 'User Registeration Success', 201);
    }

    public function addPlateNumber(Request $request, $id){
        if ($id == null){
            return $this->error('No User', 'No User Selected', 401);
        }

        $user = User::find($id);

        if (!$user){
            return $this->error('User Not Found', 'No User Selected', 401);
        }

        try{
            $validated = $request->validate([
                'plate_number' => 'required|string'
            ]);

            $pl_no = new PlateNo;
            $pl_no->plate_number = $validated['plate_number'];
            $pl_no->user_id = $id;
            $pl_no->save();

        }catch(Exception $e){
            return $this->error($e->getMessage(), 'Error Adding Plate Number', 401);
        }

        return $this->success($user, 'Plate Number Added', 201);
    }

    public function getPlateNumbers($phone_number){
        try{
            $user_id = User::where('phone_number', $phone_number)->first();
            $plate_numbers = PlateNo::where('user_id', $user_id)->get();
        }catch(Exception $e){
            return $this->error($e->getMessage(), 'Error Retrieving Plate Numbers', 401);
        }
            return $this->success($user, 'Plate Numbers Retrieved', 200);
    }


    public function ExistingEnterPark($plate_number){
        $plate_number = PlateNo::where('plate_number', $plate_number)->first();
        $plate_number->is_active = true;
        return $this->success($user, 'Plate Numbers Added to Active parkers', 200);
    }


    public function exitPark($plate_number){
        $plate_number = PlateNo::where('plate_number', $plate_number)->first();
        $plate_number->is_active = false;
        return $this->success($user, 'Plate Numbers Removed from Active parkers', 200);
    }

    public function removePlateNumber($plate_number){
        $plate_number = PlateNo::where('plate_number', $plate_number)->first();
        $plate_number->delete();
        return $this->success($user, 'Plate Numbers Deleted', 200);
    }

    public function verifyPhone($phone_number){
        //
    }

}
