<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\User;
class PlateNo extends Model
{
    public function user(){
        return $this->belongsTo(User::class);
    }
}
