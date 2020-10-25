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

                'phone_number' => 'required|string',
                // 'plate_number' => 'required|string',
                'password' => 'required|string|min:8',
            ]);

            $phone = User::where('phone_number', $validated['phone_number'])->first();

            if (!$phone){
                $user = new User;
                $user->phone_number = $validated['phone_number'];
                $user->password = md5($validated['password']);
                $user->save();
            }else{
                return $this->error([], 'Phone Number Exists', 404);
            }
            // $pl_no = new PlateNo;
            // $pl_no->plate_number = $validated['plate_number'];
            // $pl_no->user_id = $user->id;
            // $pl_no->is_active = true;
            // $pl_no->save();
        }catch(Exception $e){
            return $this->error($e->getMessage(), 'Error Registering User', 401);
        }
        return $this->success($user, 'User Registeration Success', 201);
    }

    public function userProfile(Request $request, $id){
        try{
           $validated = $request->validate([
                'name' => 'string',
                'email' => 'string',
                'address' => 'string',
                'city' => 'string',
                'state' => 'string',
            ]);

            $user = User::where('user_id', $id)->first();

            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->address = $validated['address'];
            $user->city = $validated['city'];
            $user->state = $validated['state'];
            $user->save();
        }catch(Exception $e){
            return $this->error($e->getMessage(), 'Error Registering User', 401);
        }

        return $this->success([$user, $pl_no], 'User Profile Updated', 201);
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
            $plate = PlateNo::where('plate_number', $validated['plate_number'])->where('user_id', $id)->first();

            if (!$plate){
                $pl_no = new PlateNo;
                $pl_no->plate_number = $validated['plate_number'];
                $pl_no->user_id = $id;
                $pl_no->save();
            }else{
                return $this->error([], 'Plate Number Exists already', 401);
            }
        }catch(Exception $e){
            return $this->error($e->getMessage(), 'Error Adding Plate Number', 401);
        }

        return $this->success($pl_no, 'Plate Number Added', 201);
    }

    public function getPlateNumbers($phone_number){
        try{
            $user_id = User::where('phone_number', $phone_number)->first();
            $plate_numbers = PlateNo::where('user_id', $user_id->id)->get();
        }catch(Exception $e){
            return $this->error($e->getMessage(), 'Error Retrieving Plate Numbers', 401);
        }
            return $this->success($plate_numbers, 'Plate Numbers Retrieved', 200);
    }


    public function ExistingEnterPark($plate_number){
        $plate_number = PlateNo::where('plate_number', $plate_number)->first();
        $plate_number->is_active = true;
        $plate_number->save();
        return $this->success([], 'Plate Numbers Added to Active parkers', 200);
    }


    public function exitPark($plate_number){
        $plate_number = PlateNo::where('plate_number', $plate_number)->first();
        $plate_number->is_active = false;
        $plate_number->save();
        return $this->success([], 'Plate Numbers Removed from Active parkers', 200);
    }

    public function removePlateNumber($plate_number){
        $plate_number = PlateNo::where('plate_number', $plate_number)->first();
        $plate_number->delete();
        return $this->success([], 'Plate Numbers Deleted', 200);
    }

    public function verifyPhone($phone_number){
        //
    }

}
