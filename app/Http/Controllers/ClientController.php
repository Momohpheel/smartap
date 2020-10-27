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
                'subscription_plan' => 'required|string',
                // 'logo' => 'required|image|max:2000|mimes:jpeg,jpg,png'
            ]);

            // if ($request->hasFile('logo')){
            //     $name = $request->file('logo')->getClientOriginalName();
            //     $ext = $request->file('logo')->getClientOriginalExtension();
            //     $name_without_ext = pathinfo($name, PATHINFO_FILENAME);


            // }
            $client = new Client;
            $client->company_name = $validated['company_name'];
            $client->address = $validated['address'];
            $client->long = $validated['longitude'];
            $client->lat = $validated['latitude'];
            $client->state = $validated['state'];
            $client->lga = $validated['lga'];
            $client->description = $validated['description'];
            $client->subscription_plan = $validated['subscription_plan'];
            $client->token = $this->token();
            //$client->url = env('APP_URL').'api/v1/client/'.$client->token;
            // $client->logo = "";
            $client->save();



        }catch(Exception $e){
            return $this->error($e->getMessage(), 'Error Registering Client', 401);
        }
        return $this->success($client, 'Client Registeration Success', 201);
    }


    public function getOneClient(Request $request){
        //$header = $request->header('Authorization');
        $validated = $request->validate([
            'token' => 'required|string',
        ]);
        $client = Client::where('token', $validated['token'])->first();
        if ($client){
            return $this->success($client, 'Client Fetched', 200);
        }
        return $this->error([], 'No Client Found', 404);
    }


    public function getAllActiceMembers(){
        $active_members = PlateNo::where('is_active', true)->get();
        if ($active_members){
            return $this->success($active_members, 'All Actice Members Fetched', 200);
        }else{
            return $this->error([], 'No Actice Members Fetched', 400);
        }
    }

    public function getUserDetails($plate){
        $pl = PlateNo::where('plate_number', $plate)->first();
        if ($pl){
            $user = User::where('id', $pl->id)->first();
                if ($user){
                    $details = [
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone_number' => $user->phone_number,
                    ];

                    return $this->success($details, 'Member Details Fetched', 200);
                }else{
                    return $this->error([], 'No User Found', 400);
                }
        }else{
            return $this->error([], 'Plate Number Not Found', 400);
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


