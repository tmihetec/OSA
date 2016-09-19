<?php


namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Silber\Bouncer\Database\HasRolesAndAbilities;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable {

 	use SoftDeletes;
 	use HasRolesAndAbilities;


 	protected $dates= ['deleted_at'];
	protected $append = ['ime_prezime','aktivan', 'custom', 'servis_user'];

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	//protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');



	public function getAktivanAttribute(){
		return $this->trashed() ? 0 : 1;
	}

	public function getImePrezimeAttribute(){
		$imeprezime=trim($this->first_name." ".$this->last_name)!=="" ? $this->first_name." ".$this->last_name : $this->user;
		return $imeprezime;
	}

	public function getCustomAttribute(){
		return $this->first_name." ".$this->last_name." (".$this->user.")";
	}

	public function getServisUserAttribute(){
		$ime=trim($this->first_name." ".$this->last_name)!=="" ? $this->first_name." ".$this->last_name : $this->user;
		return $ime." - ".$this->lokacija->posname;
	}

	public function lokacija(){
		return $this->belongsTo('App\Models\Pos','location');
	}	
}
