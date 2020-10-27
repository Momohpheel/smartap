<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Client;
use App\Model\User;
use App\Model\PlateNo;
use App\Traits\Response;
use App\Model\Vehicle;

class UserController extends Controller
{

    use Response;



    public function registerUser(Request $request){
        try{

            $validated = $request->validate([
                'name' => 'required|string',
                'phone_number' => 'required|string',
                'password' => 'required|string|min:8',
            ]);

            $phone = User::where('phone_number', $validated['phone_number'])->first();

            if (!$phone){
                $user = new User;
                $user->phone_number = $validated['phone_number'];
                $user->name = $validated['name'];
                $user->password = md5($validated['password']);
                $user->token = $this->token();
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

    public function userProfile(Request $request){
        try{
            $validated = $request->validate([
                    'email' => 'string',
                    'address' => 'string',
                    'city' => 'string',
                    'state' => 'string',
                ]);

                $header = $request->header('Authorization');

                $user = User::where('token', $token)->first();
                if ($user){
                    if ($header == $user->token){
                        $user->email = $validated['email'];
                        $user->address = $validated['address'];
                        $user->city = $validated['city'];
                        $user->state = $validated['state'];
                        $user->save();
                        return $this->success($user, 'User Profile Updated', 201);
                    }else{
                        return $this->error([], 'User not found', 404);
                    }
                }else{
                    return $this->error([], 'User not found ', 401);
                }
        }catch(Exception $e){
            return $this->error($e->getMessage(), 'Error Registering User', 401);
        }

    }

    public function addPlateNumber(Request $request){
        $header = $request->header('Authorization');
        $user = User::where('token', $header)->first();
        if (!$user){
            return $this->error('User Not Found', 'No User Selected', 401);
        }

        try{
            $validated = $request->validate([
                'plate_number' => 'required|string',
                'type' => 'string',
                 'brand' => 'string',
                 'color' => 'string',
            ]);
            $plate = PlateNo::where('plate_number', $validated['plate_number'])->where('user_id', $user->id)->first();

            if (!$plate){
                $pl_no = new PlateNo;
                $pl_no->plate_number = $validated['plate_number'];
                $pl_no->type = $validated['type'];
                $pl_no->brand = $validated['brand'];
                $pl_no->color = $validated['color'];
                $pl_no->user_id = $user->id;
                $pl_no->save();
            }else{
                return $this->error([], 'Plate Number Exists already', 401);
            }
        }catch(Exception $e){
            return $this->error($e->getMessage(), 'Error Adding Plate Number', 401);
        }

        return $this->success($pl_no, 'Plate Number Added', 201);
    }

    public function getPlateNumbers(){
        try{
            $header = $request->header('Authorization');
            $user_id = User::where('token', $header)->first();
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

    public function vehicleRegisteration(Request $request){


        $header = $request->header('Authorization');
        $user = User::where('token', $header)->first();

        if (!$user){
            return $this->error('User Not Found', 'No User Selected', 401);
        }

        try{
            $validated = $request->validate([
                 'plate_number' => 'required|string',
                 'type' => 'string',
                 'brand' => 'string',
                 'color' => 'string',
             ]);
             $plate = PlateNo::where('plate_number', $validated['plate_number'])->where('user_id', $user->id)->first();

        if (!$plate){
             $Plate = new PlateNo();
             $Plate->plate_number = $validated['plate_number'];
             $Plate->type = $validated['type'];
             $Plate->brand = $validated['brand'];
             $Plate->color = $validated['color'];
             $Plate->user_id = $user->id;
             $Plate->save();
            }
         }catch(Exception $e){
             return $this->error($e->getMessage(), 'Error Registering Vehicle', 401);
         }

         return $this->success($Plate, 'Vehicle Added', 201);
    }

    public function token() {
        $text = '';
        $possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

        for ($i = 0; $i<12; $i++) {
            $random_number = rand(1, strlen($possible));
            $text = $text.substr($possible, $random_number, 1);
            // str_replace(array('[',']'), '',
            $clrStr = str_replace(array('-'), '', $text);
            if(strlen($clrStr)%4 == 0) $text = $text.'-';
        }
        return strtolower(rtrim($text, "- "));
    }


}
