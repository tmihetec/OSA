<?php
namespace App\Models;

class Serviceprovider extends \Eloquent {

	protected $fillable = [];
	//protected $table = 'serviceproviders';
	
	public function loccountry(){
		return $this->belongsTo('App\Models\Loccountry','country_id');
	}

	
}