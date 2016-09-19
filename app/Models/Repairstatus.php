<?php

namespace App\Models;

class Repairstatus extends \Eloquent {

	protected $fillable = [];
	protected $table = 'repairstatuses';

	//http://laravel.com/docs/5.0/eloquent#touching-parent-timestamps
	//protected $touches = ['post'];
	
	public function repairorders(){

		return $this->belongsToMany('App\Models\Repairorder')
		->select(array('*','users.user'))
		->orderBy('repairorder_repairstatus.updated_at','desc')
		->join('users','repairorder_repairstatus.user_id','=','users.id')
		->withTimestamps();

/*
		return $this->belongsToMany('Repairstatus')
		->orderBy('repairorder_repairstatus.updated_at','desc')
		->withPivot('user_id')

		->join('users','repairorder_repairstatus.user_id','=','users.id')
		->addSelect('users.user')
		->addSelect('repairstatuses.*')

		->withTimestamps();

*/
		}


	public function odrediste(){
		return $this->belongsTo('App\Models\Pos','relocationspp_id');
	}

	public function lokacija(){
		return $this->belongsTo('\App\Models\Pos','locationspp_id');
	}

}