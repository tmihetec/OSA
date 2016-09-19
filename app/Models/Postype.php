<?php
namespace App\Models;

class Postype extends \Eloquent {

	protected $fillable = [];
	protected $table = 'postypes';
	
	
	public function poses(){
		return $this->hasMany('App\Models\Pos','postype_id'); // return $this->hasMany('Comment', 'foreign_key', 'local_key');
	}

	
}