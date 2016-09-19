<?php
namespace App\Models;


class Customersymptom extends \Eloquent {

	protected $fillable = [];
	//protected $table = 'customersymptoms';
	

		public function getCustomNameAttribute(){
	/*
		može se pozvati sa: $pos->custom_name; ili $pos->getCustomNameAttribute()
	*/
		return "$this->name ($this->code)";
	}	


	
}