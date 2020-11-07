<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Client;
use App\Model\User;
use App\Model\PlateNo;
use App\Traits\Response;
use App\Model\Vehicle;
use App\Model\Movement;
use Carbon\Carbon;

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
                $company = Client::where('token', $validated['company_token'])->first();
            if ($company){
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
                "access_token" => $accessToken
                ];
            }else{
                return $this->error(true, 'Phone Number Exists', 400);
            }
        }else{
            return $this->error(true, 'Company doesnt exist', 400);
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
                $user = User::where('id', auth()->user()->id)->first();
                if ($user){

                        $user->email = $validated['email'];
                        $user->address = $validated['address'];
                        $user->city = $validated['city'];
                        $user->state = $validated['state'];
                        $user->save();


                        $data = [
                            'name' => $user->name,
                            'phone_number' => $user->phone_number,
                            'company_token' => $user->company_token,
                            'email' => $user->email,
                            'address' => $user->address,
                            'city' => $user->city,
                            'state' => $user->state,
                        ];

                        return $this->success($data, 'User Profile Updated', 201);
                    }else{
                        return $this->error(true, 'User not found', 404);
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

             $data = [
                'plate_number' => $Plate->plate_number,
                'type' => $Plate->type,
                'brand' => $Plate->brand,
                'color' => $Plate->color,
                'user_id' => $Plate->user_id
             ];
             return $this->success($data, 'Vehicle Added', 201);
        }else{
                return $this->error(true, 'Vehicle Exists', 400);
            }
         }catch(Exception $e){
             return $this->error($e->getMessage(), 'Error Registering Vehicle', 400);
         }


    }

    public function updateVehicle(Request $request, $id){


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
             $pla = PlateNo::where('id', $id)->where('user_id', $user->id)->first();

        if ($pla){

             $pla->plate_number = $validated['plate_number'];
             $pla->type = $validated['type'];
             $pla->brand = $validated['brand'];
             $pla->color = $validated['color'];
             $pla->user_id = $user->id;
             $pla->save();

             $data = [
                'plate_number' => $pla->plate_number,
                'type' => $pla->type,
                'brand' => $pla->brand,
                'color' => $pla->color,
                'user_id' => $pla->user_id
             ];
             return $this->success($data, 'Vehicle Updated', 201);
        }else{
                return $this->error(true, 'Vehicle Doesnt Exists', 400);
            }
         }catch(Exception $e){
             return $this->error($e->getMessage(), 'Error Updating Vehicle', 400);
         }


    }

    public function searchVehicle(Request $request){
      try{


                $validated = $request->validate([
                    'company_token' => 'required|string',
                    'plate_number' => 'required'
                ]);

                $plate = PlateNo::where('plate_number', $validated['plate_number'])->get();

                if ($plate != null){
                    foreach ($plate as $pl){
                        $user = User::where('id', $pl->user_id)->where('at_location', true)->where('company_token', $validated['company_token'])->first();
                    }
                    if ($user){
                        //$user = User::where('id', $plate->user_id)->where('company_token', $validated['company_token'])->first();
                        //if ($user){
                            $platenu = PlateNo::where('plate_number', $validated['plate_number'])->where('user_id', $user->id)->first();
                                $data = [
                                    'name' => $user->name,
                                    'phone_number' => $user->phone_number,
                                    'state' => $user->state,
                                    'plate_number' => $platenu->plate_number,
                                    'type' => $platenu->type,
                                    'brand' => $platenu->brand,
                                    'color' => $platenu->color,
                                ];

                                return $this->success($data, 'Fetched User Vehicle', 200);
                            // }else{
                            //     return $this->error(true, 'User doesnt exist', 400);
                            // }
                        }else{
                            return $this->error(true, 'User is not at the location', 400);
                        }

                }else{
                    return $this->error(true, 'Plate Number doesnt exist', 400);
                }


            }catch(Exception $e){
                return $this->error($e->getMessage(), 'Error Fetching User', 400);

            }



    }

    public function userMovement(){
        try{
            $movement = Movement::where('user_id', auth()->user()->id)->get();
            $user = User::where('id', auth()->user()->id)->first();
            foreach($movement as $move){
                $data[] = [
                    'name' => $user->name,
                    'at_location' => $user->at_location,
                    'login_time' => $move->login_time,
                    'logout_time' => $move->logout_time,
                ];
        }


            return $this->success($data, "User Movements Fetched", 200);
        }catch(Exception $e){
            return $this->success($e->getMessage, "Error Fetching User Movement", 400);
        }
    }



    public function userLogout(Request $request){
        $request->validate([
            'company_token' => 'required'
        ]);
        $user = User::where('id', auth()->user()->id)->where('company_token', $request->company_token)->first();
 if ($user){
        $move = Movement::where('user_id', $user->id)->where('at_location', true)->first();
            if ($move){
                $move->at_location = false;
                $move->logout_time = Carbon::now();
                $move->save();
                $user->at_location = false;
                $user->save();
            }else{
                return $this->error(true, "User Not Logged In", 400);
            }

            // Auth::logout();

            return $this->success(true, "User Successfully logged out", 200);
        }else{
            return $this->error(true, "wrong company token", 400);
        }
    }

    public function sendMessage($number){
        $url = "https://api.africastalking.com/version1/messaging";
        $ch = curl_init($url);

        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode([
                'username'=> 'sandbox',
                'to'=> $number,
                'message' => "Hi, will you mind coming out to move your car?
                                 it's actually blocking mine! Thanks!",
                'from' => 'SMART-TAP'
            ]),
            CURLOPT_HTTPHEADER => array(
                'cache-control: no-cache',
                'Content-Type: application/x-www-form-urlencoded',
                'Accept: application/json',
                'apiKey: MyAppApiKey'
            ),
        ));


        $response = curl_exec($ch);
        $error = curl_error($ch);

        if ($error){
            die('There was an error: '. $error);
        }



        $trans = json_decode($response);

        header('Location :'. $trans->data->authorization_url);
        //curl_close($ch);



    }
}
