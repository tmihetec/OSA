<?php
namespace App\Models;

class Loccountry extends \Eloquent {

	protected $fillable = [];
	protected $table = 'loccountries';
	
	public function serviceproviders(){
		return $this->hasMany('App\Models\Serviceprovider','country_id');
	}

	
}