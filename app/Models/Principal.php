<?php
namespace App\Models;

class Principal extends \Eloquent {

	protected $fillable = [];
	protected $table = 'principals';
	
	public $timestamps = false;

//	public function repairorders(){
//		return $this->hasMany('Repairorders','brand_id');
//	}

	public function poses(){
		return $this->hasMany('App\Models\Pos','principal_id');
	}
	
}