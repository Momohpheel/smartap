<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Model\Client;
use App\Model\User;
use App\Model\PlateNo;
use Illuminate\Support\Facades\Storage;
use App\Model\subscriptionPlan as SubPlan;
use App\Traits\Response;

class ClientController extends Controller
{

    use Response;
    public $token;

    public function registerClient(Request $request){
        try{
            $validated = $request->validate([
                'company_name' => 'required|string',
                'password' => 'required',
                'phone_number' =>'required|string',
                'official_email' => "required|email"

            ]);


            $client = new Client;
            $client->company_name = $validated['company_name'];
            $client->password = md5($validated['password']);
            $client->email = $validated['official_email'];
            $client->phone_number = $validated['phone_number'];
            $client->token = $this->token();
            $accessToken = $client->createToken('ClientToken')->accessToken;

            $client->save();
            $data = [
                'company_name' => $client->company_name,
                'token' => $client->token,
                'email' =>$client->email,
                'phone_number' => $client->phone_number,
                'access_token' => $accessToken,
                'address' => $client->address,
                'latitude' => $client->lat,
                'longitude' => $client->long,
                'state' => $client->state,
                'lga' => $client->lga,
               'description' => $client->description,
                //'subscription_plan' => $client->sunscription_plan,
            ];



        }catch(Exception $e){
            return $this->error($e->getMessage(), 'Error Registering Client', 401);
        }
        return $this->success($data, 'Client Registeration Success', 201);
    }

    public function addProfile(Request $request){

        try{
                        $validated = $request->validate([
                            'company_name' => 'required|string',
                            'address' => 'required|string',
                            'latitude' => 'required|numeric',
                            'longitude' => 'required|numeric',
                            'state' => 'required|string',
                            'lga' => 'required|string',
                            'description' => 'required|string',
                            //'subscription_plan' => 'required|string',
                        ]);

                        $client = Client::where('id', auth()->user()->id)->first();
                        $client->company_name = $validated['company_name'];
                        $client->address = $validated['address'];
                        $client->long = $validated['longitude'];
                        $client->lat = $validated['latitude'];
                        $client->state = $validated['state'];
                        $client->lga = $validated['lga'];
                        $client->description = $validated['description'];
                        //$client->subscription_plan = $validated['subscription_plan'];
                        $client->save();


                        $data = [
                            'company_name' => $client->company_name,
                            'address' => $client->address,
                            'latitude' => $client->lat,
                            'longitude' => $client->long,
                            'state' => $client->state,
                            'lga' => $client->lga,
                            'description' => $client->description,
                            //'subscription_plan' => $client->sunscription_plan,
                        ];


    }catch(Exception $e){
        return $this->error($e->getMessage(), 'Error Registering Client', 401);
    }
    return $this->success($data, 'Client Registeration Success', 201);


}



    public function clientLogin(Request $request){
        try{
                $validated = $request->validate([
                    "company" => "required|string",
                    "password" => "required"
                ]);


                $client_phone = Client::where('phone_number', $validated['company'])->first();
                $client_email = Client::where('email', $validated['company'])->first();
                if ($client_phone){

                    if ($client_phone->password == md5($validated['password'])){
                        $accessToken = $client_phone->createToken('ClientToken')->accessToken;
                        $data = [
                            'company_name' => $client_phone->company_name,
                            'email' => $client_phone->email,
                            'phone_number' => $client_phone->phone_number,
                            'address' => $client_phone->address,
                            'latitude' => $client_phone->lat,
                            'longitude' => $client_phone->long,
                            'state' => $client_phone->state,
                            'lga' => $client_phone->lga,
                            'token' => $client_phone->token,
                            'description' => $client_phone->description,
                            'logo' => 'https://smartap.herokuapp.com'.Storage::url($client_phone->logo) ?? null,
                            //'subscription_plan' => $client_phone->sunscription_plan,
                            'access_token' => $accessToken,
                        ];
                        return $this->success($data, "Login Successfull", 200);


                    }else{
                        return $this->error(true, "Wrong Password", 422);
                    }

                }else if ($client_email) {
                    if ($client_email->password == md5($validated['password'])){
                        $accessToken = $client_email->createToken('ClientToken')->accessToken;
                        $data = [
                            'company_name' => $client_email->company_name,
                            'address' => $client_email->address,
                            'email' => $client_email->email,
                            'phone_number' => $client_email->phone_number,
                            'latitude' => $client_email->latitude,
                            'longitude' => $client_email->longitude,
                            'state' => $client_email->state,
                            'lga' => $client_email->lga,
                            'token' => $client_email->token,
                            'description' => $client_email->description,
                            'logo' => 'https://smartap.herokuapp.com'.Storage::url($client_email->logo) ?? null,
                            //'subscription_plan' => $client_email->sunscription_plan,
                            'access_token' => $accessToken,
                        ];
                        return $this->success($data, "Login Successfull", 200);

                    }else{
                        return $this->error(true, "Wrong Password", 422);
                    }


                }else{
                    return $this->error(true, "This Company doesnt exist", 422);
                }

            }catch(Exception $e){
                return $this->error($e->getMessage(),"Login Unsuccessful",400);
            }

    }

    public function getUsersRegisteredUnderCompany(){
        try{
            $company = Client::where('id', auth()->user()->id)->first();
            $users = User::where('company_token' ,$company->token)->get();
            //if (count($users) == 0){return 'yes';}else{return 'no';}
            if(count($users) == 0){
                return $this->success([], "No User is registered under this company",200);
            }
            else{
                foreach($users as $user){
                    $data[] = [
                        'name' => $user->name,
                        'phone_number' => $user->phone_number,
                        'email' => $user->email,
                        'address' => $user->address,
                        'city' => $user->city,
                        'state' => $user->state,
                        'at_location' => $user->at_location,
                    ];
                }

                return $this->success($data, "Users Retrieved",200);
            }

        }catch(Exception $e){
            $this->error($e->getMessage(), "Error in gettting Users", 400);
        }
    }

    public function changePassword(Request $request){

        try{
            $validated = $request->validate([
                "token" => "required",
                'password' => "required|string",
                'new_password' => "required|string",
                "confirm_password" => "required|string"
            ]);

            if ($validated['new_password'] == $validated['confirm_password']){

                $client = Client::where('id', auth()->user()->id)->where('password', md5($validated['password']))->where('token', $validated['token'])->first();
                if ($client){
                    $client->password = md5($validated['new_password']);
                    $client->save();
                    $data = [
                        'company_name' => $client->company_name,
                        'address' => $client->address,
                    ];
                    return $this->success($data, "Password changed", 200);
                }else{
                    return $this->error([], "Client doesn't exist", 400);
                }


            }else{
                return $this->error([], "Passwords do not match", 400);
            }

        }catch(Exception $e){
            return $this->error($e->getMessage(), "Password couldnt be changed", 400);
        }

    }

    public function getUserDetails($id){
        if (!$id){
            return $this->error([], "No resource given", 400);
        }

        $user = User::where('id', $id)->first();
        if ($user){
            $vehicles = PlateNo::where('user_id', $id)->get();
            foreach($vehicles as $vehicle){
                $cars[] = [
                    'plate_number' => $vehicle->plate_number,
                    'brand' => $vehicle->brand,
                    'type' => $vehicle->type,
                    'color' => $vehicle->color,
                ];
            }
            $data = [
                'name' => $user->name,
                'phone' => $user->phone_number,
                'email' => $user->email,
                'address' => $user->address,
                'city' => $user->city,
                'state' => $user->state,
                'vehicles' => $cars
            ];


        return $this->success($data, "User Details", 200);

        }else{
            return $this->error([], "No user found", 400);
        }

     }

     public function getUserAtLocationDetails(){

        try{
            $company = Client::where('id', auth()->user()->id)->first();
            $users = User::where('company_token' ,$company->token)->where('at_location', true)->get();
            //if (count($users) == 0){return 'yes';}else{return 'no';}
            if(count($users) == 0){
                return $this->success([], "No User is registered under this company",200);
            }
            else{

                foreach($users as $user){
                    $vehicles = PlateNo::where('user_id', $user->id)->get();
                    $data[] = [
                        'name' => $user->name,
                        'phone_number' => $user->phone_number,
                        'email' => $user->email,
                        'address' => $user->address,
                        'city' => $user->city,
                        'state' => $user->state,
                        'vehicles' => count($vehicles) ?? null,
                        'at_location' => $user->at_location,
                    ];
                }

                return $this->success($data, "Users Retrieved",200);
            }

        }catch(Exception $e){
            $this->error($e->getMessage(), "Error in gettting Users", 400);
        }
     }

     public function sub_plan(Request $request){
        $validated = $request->validate([
            'plan' => "required|string",
            "amount" => "required|string",
            "expiry_date" => "required|string"
        ]);

        $sub = new SubPlan;
        $sub->plan = $validated['plan'];
        $sub->amount = $validated['amount'];
        $sub->expiry_date = $validated['expiry_date'];
        $sub->client_id = auth()->user()->id;
        $sub->save();

        return $this->success($sub, "Subscription Added", 200);
     }

     public function getClientInfo(Request $request){
        $company = auth()->user()->id;
         $client = Client::where('id', $company)->first();
         if ($client){
            $data = [
                'company_name' => $client->company_name,
                'address' => $client->address,
                'latitude' => $client->lat,
                'longitude' => $client->long,
                'state' => $client->state,
                'lga' => $client->lga,
                'description' => $client->description,
                'token' => $client->token,
                'logo' => env('APP_URL') . Storage::url($client->logo) ?? null
             ];
             return $this->success($data, "Client Details", 200);
         }else{
            return $this->error(true, "Invalid token", 400);
         }

     }

     function uploadAvatar(Request $request){
        $data = request()->validate( [
            'logo' => 'required|image|mimes:jpeg,png,jpg|max:1999|nullable',
        ]);

        if (request()->hasFile('logo')){
            $image_name = request()->file('logo')->getClientOriginalName();
            $image_name_withoutextensions =  implode("_", explode(" ", pathinfo($image_name, PATHINFO_FILENAME)));
            $image_extension = request()->file('logo')->getClientOriginalExtension();
            $image_to_store = $image_name_withoutextensions.'_'.time().'.'. $image_extension;
            $path = request()->file('logo')->storeAs('public/', $image_to_store);

            $user_id = auth()->user()->id;
            $client = Client::find($user_id);
            $client->logo = 'public/' . $image_to_store;
            $client->save();
            return $this->success($client->logo,"Image Upload successfull!", 200);
        }

        return $this->error(true,"Please provide and image file");
    }
    // public function getLogo(){
    //     $client = Client::find(auth()->user()->id);
    //     if ($client){
    //         if ($client->logo){
    //             return $this->success(["logo" => env('APP_URL') . Storage::url($client->logo)], "Company Logo!", 200);
    //         }else{
    //             return $this->error(true,"No logo found!");
    //         }
    //     }else{
    //         return $this->error(true,"User doesn't exist!");
    //     }

    // }
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


