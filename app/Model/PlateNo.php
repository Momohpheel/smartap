<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PlateNo extends Model
{
    public function users(){
        return $this->hasOne(User::class);
    }
}
