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
                'geolocation' => 'required|string',
                'state' => 'required|string',
                'lga' => 'required|string',
                'description' => 'required|string',
                'subscription_plan' => 'required|string',
                'logo' => 'required|image|max:2000|mimes:jpeg,jpg,png'
            ]);

            if ($request->hasFile('logo')){
                $name = $request->file('logo')->getClientOriginalName();
                $ext = $request->file('logo')->getClientOriginalExtension();
                $name_without_ext = pathinfo($name, PATHINFO_FILENAME);


            }
            $client = new Client;
            $client->company_name = $validated['company_name'];
            $client->address = $validated['address'];
            $client->long = $validated['long'];
            $client->lat = $validated['lat'];
            $client->state = $validated['state'];
            $client->lga = $validated['lga'];
            $client->description = $validated['description'];
            $client->subscription_plan = $validated['subscription_plan'];
            $client->token = Str::random(50);
            $client->url = env('APP_URL').'api/v1/client/'.$client->token;
            $client->logo = "";
            $client->save();



        }catch(Exception $e){
            return $this->error($e->getMessage(), 'Error Registering Client', 401);
        }
        return $this->success($client, 'Client Registeration Success', 201);
    }


    public function getOneClient($url){
        $client = Client::where('token', $url)->first();
        return $this->success($client, 'Client Fetched', 200);
    }


    public function getAllActiceMembers(){
        $active_members = PlateNo::where('is_active', true)->get();
        return $this->success($active_members, 'All Actice Members Fetched', 200);
    }

    public function getUserDetails($plate){
        $pl = PlateNo::where('plate_number', $plate)->first();
        $user = User::where('id', $pl->id)->first();
        $details = [
            'name' => $user->name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
        ];

        return $this->success($details, 'Member Details Fetched', 200);
    }
}
