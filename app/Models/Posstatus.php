<?php
namespace App\Models;

class Posstatus extends \Eloquent {

	protected $fillable = [];
	protected $table = 'posstatuses';
	

	public function poses(){
		return $this->hasMany('App\Models\Pos','posstatus_id');
	}

	
}