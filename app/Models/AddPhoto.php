<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddPhoto extends Model
{
    use HasFactory;
    protected $table = "addphoto";

    public function getEvent(){
        return $this->belongsTo('App\Models\AddEvent' , 'event', 'id');
    }
}
