<?php
namespace App\Models;

class Faultyelement extends \Eloquent {

	protected $fillable = [];
	//protected $table = 'faultyelements';
	

		public function getCustomNameAttribute(){
	/*
		može se pozvati sa: $pos->custom_name; ili $pos->getCustomNameAttribute()
	*/
		return "$this->name ($this->code)";
	}	


	
}