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
            $client->subscription_plan = $validated['subscription_plan'];
            $client->token = Str::random(50);
            $client->url = env('APP_URL').'api/v1/client/'.$client->token;
            $client->save();



        }catch(Exception $e){
            return $this->error($e->getMessage(), 'Error Registering Client', 401);
        }
        return $this->success($client, 'Client Registeration Success', 201);
    }


    public function getOneClient($url){
        //$client = Client::where('token', $url)->first();
        return $this->success(1, 'Client Fetched', 200);
    }


    public function getAllClients(){
        $clients = Client::all();
        return $this->success($clients, 'All Clients Fetched', 200);
    }
}
