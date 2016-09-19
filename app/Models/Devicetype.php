<?php
namespace App\Models;

class Devicetype extends \Eloquent {

	protected $fillable = [];
	protected $table = 'devicetypes';
	
		public $timestamps= false;

//	public function repairorders(){
//		return $this->hasMany('Repairorders','brand_id');
//	}

public function models(){
		return $this->hasMany('App\Models\Model','devicetype_id');
	}
	
}