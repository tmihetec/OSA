<?php
namespace App\Models;

class Pos extends \Eloquent {

	protected $fillable = [];
	protected $table = 'poses';
	public $timestamps = false;
	
	public function posstatus(){
		return $this->belongsTo('App\Models\Posstatus','posstatus_id'); //model, kolona u ovom modelu (POS), kolona u POSSTATUS modelu
	}
	public function postype(){
		return $this->belongsTo('App\Models\Postype','postype_id'); //return $this->belongsTo('User', 'local_key', 'parent_key');
	}
	public function repairorders(){
		return $this->hasMany('App\Models\Repairorder','pos_id');
	}

	public function principal(){
		return $this->belongsTo('App\Models\Principal','principal_id');
	}

	public function grad(){
		return $this->belongsTo('App\Models\Locplace','posplace_id');
	}

	public function distributer(){
		return $this->belongsTo('App\Models\Distributer', 'distributer_id');
	}

	public function getCustomNameAttribute(){
	/*
		može se pozvati sa: $pos->custom_name; ili $pos->getCustomNameAttribute()
	*/

		if ($this->posid == '0') {

				return $this->principal->naziv.": $this->posname";
		}
		else if (trim($this->posid =='')) {
			return $this->principal->naziv.": $this->posname";
		}
		else {
			return $this->principal->naziv.": $this->posid - $this->posname";
		}
	}	
}