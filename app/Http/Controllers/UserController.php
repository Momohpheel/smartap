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
                'company_token' => 'required|string',
                'password' => 'required|string|min:8',
            ]);

            $phone = User::where('phone_number', $validated['phone_number'])->first();

            if (!$phone){
                $user = new User;
                $user->phone_number = $validated['phone_number'];
                $user->company_token = $validated['company_token'];
                $user->name = $validated['name'];
                $user->password = md5($validated['password']);
                $accessToken = $user->createToken('authToken')->accessToken;
                $user->save();
                $data = [
                    'name' => $user->name,
                'phone_number' => $user->phone_number,
                'company_token' => $user->company_token,
                "access token" => $accessToken
                ];
            }else{
                return $this->error(true, 'Phone Number Exists', 400);
            }

        }catch(Exception $e){
            return $this->error($e->getMessage(), 'Error Registering User', 401);
        }
        return $this->success($data, 'User Registeration Success', 201);
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

                $user = User::where('token', $header)->first();
                if ($user){
                    if ($header == $user->token){
                        $user->email = $validated['email'];
                        $user->address = $validated['address'];
                        $user->city = $validated['city'];
                        $user->state = $validated['state'];
                        $user->save();
                        return $this->success($user, 'User Profile Updated', 201);
                    }else{
                        return $this->error(true, 'User not found', 404);
                    }
                }else{
                    return $this->error(true, 'invalid token ', 400);
                }
        }catch(Exception $e){
            return $this->error($e->getMessage(), 'Error Registering User', 401);
        }

    }

    public function addPlateNumber(Request $request){
        // $header = $request->header('Authorization');
        $user = User::where('id', auth()->user()->id)->first();
        if (!$user){
            return $this->error(true, 'invalid token', 400);
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

                return $this->success($pl_no, 'Plate Number Added', 201);
            }else{
                return $this->error(true, 'Plate Number Exists already', 401);
            }
        }catch(Exception $e){
            return $this->error($e->getMessage(), 'Error Adding Plate Number', 401);
        }


    }

    public function getPlateNumbers(Request $request){
        try{
            // $header = $request->header('Authorization');
            // if ($header){
                $user_id = User::where('id', auth()->user()->id)->first();
                if ($user_id){
                    $plate_numbers = PlateNo::where('user_id', $user_id->id)->get();
                    return $this->success($plate_numbers, 'Plate Numbers Retrieved', 200);
                }else{
                    return $this->error(true, 'invalid token', 400);
                }

            // }else{
            //     return $this->error(true, 'No Token Found', 400);
            // }

        }catch(Exception $e){
            return $this->error($e->getMessage(), 'Error Retrieving Plate Numbers', 401);
        }

    }


    public function ExistingEnterPark($plate_number){
        $plate_number = PlateNo::where('plate_number', $plate_number)->first();
        $plate_number->is_active = true;
        $plate_number->save();
        return $this->success($plate_number, 'Plate Numbers Added to Active parkers', 200);
    }


    public function exitPark($plate_number){
        $plate_number = PlateNo::where('plate_number', $plate_number)->first();
        $plate_number->is_active = false;
        $plate_number->save();
        return $this->success($plate_number, 'Plate Numbers Removed from Active parkers', 200);
    }

    public function removePlateNumber($plate_number){
        $plate_number = PlateNo::where('plate_number', $plate_number)->first();
        $plate_number->delete();
        return $this->success($plate_number, 'Plate Numbers Deleted', 200);
    }

    public function vehicleRegisteration(Request $request){


        // $header = $request->header('Authorization');
        $user = User::where('id', auth()->user()->id)->first();

        if (!$user){
            return $this->error(true, 'No User Found', 400);
        }

        try{
            $validated = $request->validate([
                 'plate_number' => 'required|string',
                 'type' => 'string',
                 'brand' => 'string',
                 'color' => 'string',
             ]);
             $pla = PlateNo::where('plate_number', $validated['plate_number'])->where('user_id', $user->id)->first();

        if (!$pla){
             $Plate = new PlateNo();
             $Plate->plate_number = $validated['plate_number'];
             $Plate->type = $validated['type'];
             $Plate->brand = $validated['brand'];
             $Plate->color = $validated['color'];
             $Plate->user_id = $user->id;
             $Plate->save();
             return $this->success($Plate, 'Vehicle Added', 201);
        }else{
                return $this->error(true, 'Vehicle Exists', 400);
            }
         }catch(Exception $e){
             return $this->error($e->getMessage(), 'Error Registering Vehicle', 400);
         }


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
