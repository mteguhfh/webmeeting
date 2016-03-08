<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Section;

class Issue extends Model
{
    protected $fillable = ['section_id', 'topic', 'topic_description', 'issued', 'target', 'status', 'priority', 'action'];

    public function section()
    {
    	return $this->belongsTo('\App\Section');
    }

    public function sections()
    {
    	return $this->belongsToMany('\App\Section');
    }

    public function getSectionListAttribute()
    {
    	return $this->sections->lists('id')->all();
    }
}
