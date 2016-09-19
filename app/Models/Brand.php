<?php
namespace App\Models;

class Brand extends \Eloquent {

	protected $fillable = [];
	protected $table = 'brands';
	
	public $timestamps= false;
//	public function repairorders(){
//		return $this->hasMany('Repairorders','brand_id');
//	}

public function models(){
		return $this->hasMany('App\Models\Model','brand_id');
	}
	
}