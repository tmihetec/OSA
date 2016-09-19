<?php
namespace App\Models;

class Locplace extends \Eloquent {

	protected $fillable = [];
	public $timestamps = false;
	//protected $table = 'locplaces';
	

		public function country(){
			return $this->belongsTo('App\Models\Loccountry','loccountry_id');
		}

		public function area(){
			return $this->belongsTo('App\Models\Locarea','locarea_id');
		}

		public function getCustomNameAttribute(){
	/*
		može se pozvati sa: $pos->custom_name; ili $pos->getCustomNameAttribute()
	*/
		if ($this->postalcode > 0)
			return strtoupper($this->country->code)." ".$this->postalcode." - ".$this->name;
		else 
			return 'Nije specificiran';
	}	

	
}