<?php
namespace App\Models;

class Techniciansymptom extends \Eloquent {

	protected $fillable = [];
	//protected $table = 'techniciansymptoms';
	

		public function getCustomNameAttribute(){
	/*
		može se pozvati sa: $pos->custom_name; ili $pos->getCustomNameAttribute()
	*/
		return "$this->name ($this->code)";
	}	


	
}