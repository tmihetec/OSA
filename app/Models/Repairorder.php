<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;


class Repairorder extends Eloquent {

	use SoftDeletes;

	protected $fillable = [];
	protected $table = 'repairorders';
	protected $dates = ['deleted_at', 'devicerecievedate','stsroopendate','devicebuydate','posclaimdate','devicemanufactureddate'];

	protected $appends = array('adresa_otpreme');

	/**
	 * vraća kolko je puta bio na servisu (broji IMEI-je)
	 * http://heera.it/laravel-model-relationship#.Vx7wXdKLTix
	 *
	 * kaj ako je taj uređan dan u zamjenu? deviceoutgoingimei?
	 */
	public function exservisi(){

		$ret=

//DB::enableQueryLog();
		\DB::table('repairorders')
			->select('*',\DB::raw(' IF(deviceoutgoingimei<>deviceincomingimei,"da","ne") as zamjena' ))

			->where(function($q){
				$q->where('deviceincomingimei','=',$this->attributes['deviceincomingimei'])
				  ->orWhere(function($qu){
				  		$qu->where('deviceoutgoingimei','=',$this->attributes['deviceincomingimei'])
				  			->where('deviceincomingimei','<>',$this->attributes['deviceincomingimei']);
				});
			})			
			
			->whereNotNull('deviceincomingimei')
			->whereNull('deleted_at')
//			->get()
			;
//dd(DB::getQueryLog()[0]);
		return $ret;
		/*return \DB::table('repairorders')->where('deviceincomingimei','=',$this->attributes['deviceincomingimei'])
		->whereNotNull('deviceincomingimei')->where('devicemodel_id','=',$this->attributes['devicemodel_id'])->whereNull('deleted_at');
		*/
	    //return $this->hasMany('Repairorder', 'deviceincomingimei', 'deviceincomingimei');
	}


	/**
	 * Vraća zadnji status relokacije (ili za isporuku ili za servis), koji ima relocation id > 0  
	 * kako bi se znalo kamo se treba vratiti uređaj (ako se odluči vratiti)
	 * 
	 * @return collection zadnji status relokacije
	 */
	public function relocationBack(){
		return $this->repairstatuses()->whereIn('repairstatus_id',array(11,13))->where('relocationspp_id','>',0)->orderBy('repairorder_repairstatus.updated_at','desc')->first();
		//
	}

	//http://laravel.com/docs/5.1/eloquent-mutators

	public function getAdresaOtpremeAttribute(){

		if(!isset($this->attributes['devicereturntype_id'])) {
			return "nepoznata vrsta otpreme";
		} 

		switch ($this->attributes['devicereturntype_id']) {
			case '4':
				// NA NEKU DRUGU ADRESU
				return $this->attributes['devicereturnother'];
				break;

			case '3':
				// OSOBNO
				return "osobno preuzimanje";
				break;

			case '2':
				// DIREKTNO KORISNIKU
				$returnAdresa = $this->attributes['customerstreet'];
				if (!is_null($this->grad)) {
					if (trim($returnAdresa)!=="") $returnAdresa.="<br>";
					$returnAdresa.=$this->grad->custom_name;
				}
				return $returnAdresa;
				break;


			default:
				// 1 = NA POS
				if (!is_null($this->pos) && !is_null($this->pos->attributes['posadresa'])) {
					$returnAdresa = $this->pos->attributes['posadresa'];
					if (!is_null($this->pos->grad)) {
						if (trim($returnAdresa)!=="") $returnAdresa.="<br>";
						$returnAdresa.=$this->pos->grad->custom_name;
					}
				} else {
					$returnAdresa = "Nepoznata";
				}

				return $returnAdresa;
				break;
		}


	}


	public function serviser(){
		return $this->belongsTo('App\Models\User','stsserviceperson_id')->withTrashed();
		}

	public function logisticarotpremio(){
		return $this->belongsTo('App\Models\User','devicereturnperson_id')->withTrashed();
		}

	public function posotprema(){
		return $this->belongsTo('App\Models\Devicereturntype','posdevicereturntype_id');
		}

	public function otprema(){
		return $this->belongsTo('App\Models\Devicereturntype','devicereturntype_id');
		}

	public function grad(){
		return $this->belongsTo('App\Models\Locplace','customerplace_id');
		}

	public function model(){
		return $this->belongsTo('App\Models\Model','devicemodel_id');
		}
	public function pos(){
		return $this->belongsTo('App\Models\Pos','pos_id');
		}

	//http://www.developed.be/2013/08/30/laravel-4-pivot-table-example-attach-and-detach/
	/*
	Extra foreign key column in pivot table
	But what if the extra column is a foreign key to some other table?  Let’s say we’re in a casino where customers regularly switch chairs and we want to register at which chair the drink was ordered.

	Say: customer_drinks(customer_id, drink_id, customer_got_drink, chair_id)

	And: chair(chair_id, chair_name)

	The method withPivot(‘chair_id’) won’t automatically join with table the chair table, so we don’t have access to the chair_name column that way. We have to explicitly join with table chair.

	class Customer extends \Eloquent {    
	    public function drinks()
	    {
	        return $this->belongsToMany('Drink', 'customer_drinks', 'customer_id', 'drink_id')
	                    ->withPivot('customer_got_drink', 'chair_id');
	                    ->join('chair', 'chair_id', 'chair.id');
	                    ->select('drink_id', 'customer_id', 'pivot_customer_got_drink', 'chair.name AS pivot_chair_name'); //this select is optional
	    }
	}
	*/	

	public function spareparts(){
		return $this->belongsToMany('App\Models\Sparepart')->with('stock')->withPivot('qty','price','spwarehouse_id')->withTimestamps(); 
		}

	public function stsservices(){
		return $this->belongsToMany('App\Models\Stsservice')->withPivot('qty','price','savedname','savedjm'); 
		}

	public function customersymptoms(){
		return $this->belongsToMany('App\Models\Customersymptom'); 
		}

	public function faultyelements(){
		return $this->belongsToMany('App\Models\Faultyelement'); 
		}

	public function techniciansymptoms(){
		return $this->belongsToMany('App\Models\Techniciansymptom')->orderBy('main','desc')->withPivot('main'); 
		}


	public function repairstatuses(){
		//return $this->belongsToMany('Repairstatus')->orderBy('repairorder_repairstatus.updated_at','desc')->withPivot('user_id')->withTimestamps();

		return $this->belongsToMany('App\Models\Repairstatus')
		->select(array('*','repairstatuses.*','users.user as user', 'lu.user as loggeduser', 'poses.posname'))
		->orderBy('repairorder_repairstatus.created_at','desc')
		->leftJoin('users','repairorder_repairstatus.user_id','=','users.id')
		->leftJoin('users as lu','repairorder_repairstatus.loggeduser_id','=','lu.id')
		->leftJoin('poses','repairorder_repairstatus.relocationspp_id','=','poses.id')
		->withTimestamps()
		->withPivot('id');

		}


	public function lateststatus(){

/*
	return $this->belongsToMany('Repairstatus')
		->select(array('*','users.user'))
		->orderBy('repairorder_repairstatus.updated_at','desc')
		->join('users','repairorder_repairstatus.user_id','=','users.id')
		->withTimestamps()
		->limit(1,0);

 */
	    return $this->repairstatuses()->latest('repairorder_repairstatus.created_at');//->limit(1,0);
		}



	public function stsservicelevel(){
		return $this->belongsTo('App\Models\Servicelevel','stsservicelevel_id');
		}


//POS->PARTNER->Distributer?
	public function distributer(){
		return $this->belongsTo('App\Models\Distributer','pos_id');
		}


	public function servicelocation(){
		return $this->belongsTo('App\Models\Pos','stsservicelocation_id');
		}





}