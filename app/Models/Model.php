<?php
namespace App\Models;

class Model extends \Eloquent {

	protected $fillable = [];
	protected $table = 'models';
	public $timestamps = false;

	public function repairorders(){
		return $this->hasMany('App\Models\Repairorder','devicemodel_id');
	}

	public function brand(){
		return $this->belongsTo('App\Models\Brand','brand_id'); //return $this->belongsTo('User', 'local_key', 'parent_key');
	}
	
	public function devicetype(){
		return $this->belongsTo('App\Models\Devicetype','devicetype_id'); //return $this->belongsTo('User', 'local_key', 'parent_key');
	}
	
	//http://stackoverflow.com/questions/24980403/select2-custom-matcher-for-non-adjacent-keywords
	// ovo implementirati, tak da se može tražiti i npr. C25 NOA
	// to staviti i za spareparts, customer i technician symptoms.
	// shvatiti!!

	public function getBrandModelAttribute(){
	/*
		može se pozvati sa: $pos->custom_name; ili $pos->getCustomNameAttribute()
	*/

		return $this->devicetype->naziv." - ".$this->brand->name." $this->name"; // možda dodati u naziv i brand kojem pripada? i lokacije skladišta?
	}	

}