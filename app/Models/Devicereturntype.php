<?php
namespace App\Models;

class Devicereturntype extends \Eloquent {

	protected $fillable = [];
	protected $table = 'devicereturntypes';
	
	public $timestamps = false;

//	public function repairorders(){
//		return $this->hasMany('Repairorders','brand_id');
//	}

	
}