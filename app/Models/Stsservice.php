<?php
namespace App\Models;

class Stsservice extends \Eloquent {

	protected $fillable = [];
	//protected $table = 'stsservices';
		public $timestamps = false;


		public function getCustomNameAttribute(){
	/*
		može se pozvati sa: $pos->custom_name; ili $pos->getCustomNameAttribute()
	*/
		return "$this->name ($this->code)";
	}	


	
}