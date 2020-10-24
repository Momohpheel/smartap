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

    // public function __construct(){
    //     $this->middleware('');
    // }


    public function clientLogin(){

    }

    public function regiterClient(Request $request){
        try{

            $validated = $request->validate([
                'company_name' => 'required|string',
                'address' => 'required|string',
                'geolocation' => 'required|string',
                'state' => 'required|string',
                'lga' => 'required|string',
                'description' => 'required|string',
                'subscription_plan' => 'required|string',
            ]);

            $client = new Client;
            $client->company_name = $validated['company_name'];
            $client->address = $validated['address'];
            $client->geolocation = $validated['geolocation'];
            $client->state = $validated['state'];
            $client->lga = $validated['lga'];
            $client->description = $validated['description'];
            $client->token = Str::random(50);
            $token = $client->token;
            $client->save();



        }catch(Exception $e){
            return $this->error($e->getMessage(), 'Error Registering Client', 401);
        }
        return $this->success($client, 'Client Registeration Success', 201);
    }

    public function getClient($url){
        $client = Client::where('token', $url)->first();
        return $this->success($client, 'Client Fetched', 200);
    }
}
