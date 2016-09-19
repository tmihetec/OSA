<?php
namespace App\Models;

class Sparepart extends \Eloquent {

	protected $fillable = [];
	//protected $table = 'spareparts';
	public $timestamps = false;

	
	public function stock(){
		return $this->belongsToMany('App\Models\Spwarehouse')->withPivot('qty');
	}



		public function getCustomNameAttribute(){
	/*
		može se pozvati sa: $pos->custom_name; ili $pos->getCustomNameAttribute()
	*/
		//return $this->stock->code."-$this->name"; // možda dodati u naziv i brand kojem pripada? i lokacije skladišta?
		return "$this->name"; // možda dodati u naziv i brand kojem pripada? i lokacije skladišta?
	}	


	
}