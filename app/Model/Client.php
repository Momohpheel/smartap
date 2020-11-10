<?php

namespace App\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
class Client extends Authenticatable
{
    use HasApiTokens;
    //
}
