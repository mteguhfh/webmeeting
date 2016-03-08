<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \App\Issue;

class Section extends Model
{
    protected $guarded = ['id'];

    public function issue(){
    	return $this->hasMany('\App\Issue');
    }

    public function issues(){
    	return $this->belongsToMany('App\Issue');
    }
}
