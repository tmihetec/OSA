<?php
namespace App\Models;

class Devicerecievetype extends \Eloquent {

	protected $fillable = [];
	protected $table = 'devicerecievetypes';
	
	public $timestamps = false;

//	public function repairorders(){
//		return $this->hasMany('Repairorders','brand_id');
//	}

	
}