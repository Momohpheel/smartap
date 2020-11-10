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
                'address' => 'required|string',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'state' => 'required|string',
                'lga' => 'required|string',
                'description' => 'required|string',
                'password' => 'required',
                'subscription_plan' => 'required|string',
            ]);


            $client = new Client;
            $client->company_name = $validated['company_name'];
            $client->address = $validated['address'];
            $client->long = $validated['longitude'];
            $client->lat = $validated['latitude'];
            $client->state = $validated['state'];
            $client->lga = $validated['lga'];
            $client->description = $validated['description'];
            $client->password = md5($validated['password']);
            $client->subscription_plan = $validated['subscription_plan'];
            $client->token = $this->token();
            $accessToken = $client->createToken('ClientToken')->accessToken;

            $client->save();
            $data = [
                'company_name' => $client->company_name,
                'address' => $client->address,
                'latitude' => $client->latitude,
                'longitude' => $client->longitude,
                'state' => $client->state,
                'lga' => $client->lga,
                'token' => $client->token,
                'description' => $client->description,
                'subscription_plan' => $client->sunscription_plan,
                'access_token' => $accessToken,
            ];



        }catch(Exception $e){
            return $this->error($e->getMessage(), 'Error Registering Client', 401);
        }
        return $this->success($data, 'Client Registeration Success', 201);
    }

    public function clientLogin(Request $request){
        try{
                $validated = $request->validate([
                    "company_name" => "required|string",
                    "company_token" => "required|string",
                    "password" => "required"

                ]);


                $client = Client::where('company_name', $validated['company_name'])->where('password', md5($validated['password']))->first();
                    if ($client){
                        if ($client->token == $validated['company_token']){
                            $accessToken = $client->createToken('ClientToken')->accessToken;

                            $data = [
                                'company_name' => $client->company_name,
                                'address' => $client->address,
                                'latitude' => $client->latitude,
                                'longitude' => $client->longitude,
                                'state' => $client->state,
                                'lga' => $client->lga,
                                'description' => $client->description,
                                'subscription_plan' => $client->sunscription_plan,
                                'access_token' => $accessToken,
                            ];
                            return $this->success($data, "Login Successfull", 200);
                        }
                        else{
                            return $this->error(true, "Wrong token", 422);
                        }
                    }
                    else{
                        return $this->error(true, "Company Name or Password Incorrect", 422);
                    }
                }catch(Exception $e){
                    return $this->error($e->getMessage(),"Login Unsuccessful",400);

                }
    }

    public function getUsersRegisteredUnderCompany(){
        try{
            $company = Client::where('id', auth()->user()->id)->first();
            $users = User::where('company_token', $company->token)->get();
            return $users;
            if (!empty($users)){
                    foreach ($users as $user){
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
            }else{
                return $this->error(true, "No User is registered under this company",400);
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


