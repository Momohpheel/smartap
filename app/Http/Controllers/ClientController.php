<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Model\Client;
use App\Model\User;
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
                'latitude' => $client->latitude,
                'longitude' => $client->longitude,
                'state' => $client->state,
                'lga' => $client->lga,
               'description' => $client->description,
                'subscription_plan' => $client->sunscription_plan,
            ];



        }catch(Exception $e){
            return $this->error($e->getMessage(), 'Error Registering Client', 401);
        }
        return $this->success($data, 'Client Registeration Success', 201);
    }

    public function addProfile(Request $request, $token){

        try{
                        $validated = $request->validate([
                            'company_name' => 'required|string',
                            'address' => 'required|string',
                            'latitude' => 'required|numeric',
                            'longitude' => 'required|numeric',
                            'state' => 'required|string',
                            'lga' => 'required|string',
                            'description' => 'required|string',
                            'subscription_plan' => 'required|string',
                            'token'=> 'required'
                        ]);

                        $client = Client::where('id', auth()->user()->id)->first();
                        $client->company_name = $validated['company_name'];
                        $client->address = $validated['address'];
                        $client->long = $validated['longitude'];
                        $client->lat = $validated['latitude'];
                        $client->state = $validated['state'];
                        $client->lga = $validated['lga'];
                        $client->description = $validated['description'];
                        $client->subscription_plan = $validated['subscription_plan'];
                        $client->save();


                        $data = [
                            'company_name' => $client->company_name,
                            'address' => $client->address,
                            'latitude' => $client->latitude,
                            'longitude' => $client->longitude,
                            'state' => $client->state,
                            'lga' => $client->lga,
                            'description' => $client->description,
                            'subscription_plan' => $client->sunscription_plan,
                        ];


    }catch(Exception $e){
        return $this->error($e->getMessage(), 'Error Registering Client', 401);
    }
    return $this->success($data, 'Client Registeration Success', 201);


}



    public function clientLogin(Request $request){
        try{
                $validated = $request->validate([
                    "username" => "required|string",
                    "password" => "required"
                ]);


                $client_phone = Client::where('phone_number', $validated['username'])->first();
                $client_email = Client::where('email', $validated['username'])->first();
                if ($client_phone){

                    if ($client_phone->password == md5($validated['password'])){
                        $accessToken = $client_phone->createToken('ClientToken')->accessToken;
                        $data = [
                            'company_name' => $client_phone->company_name,
                            'email' => $client_phone->email,
                            'phone_number' => $client_phone->phone_number,
                            'address' => $client_phone->address,
                            'latitude' => $client_phone->latitude,
                            'longitude' => $client_phone->longitude,
                            'state' => $client_phone->state,
                            'lga' => $client_phone->lga,
                            'description' => $client_phone->description,
                            'subscription_plan' => $client_phone->sunscription_plan,
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
                            'description' => $client_email->description,
                            'subscription_plan' => $client_email->sunscription_plan,
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
                return $this->error(true, "No User is registered under this company",400);
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
                    ];
                }

                return $this->success($data, "Users Retrieved",200);
            }

        }catch(Exception $e){
            $this->error($e->getMessage(), "Error in gettting Users", 400);
        }
    }


    public function getOneClient(Request $request){    }
    public function getAllActiceMembers(){}
    public function getUserDetails(){    }

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


