<?php

namespace App\Http\Controllers;

use Input, View, Validator, Session, Redirect, Auth;
use User, Repairorder, Model, Repairstatus, Locplace, Pos;
use Claimtype, Stsservicelevel, Devicereturntype, Devicerecievetype;
use Customersymptom,Techniciansymptom, Faultyelement, Sparepart, Stsservice, Spwarehouse;
use App\Models\Soap\ws_tele2soap;

use \Illuminate\Http\Request;
use \App\Http\Controllers\SoapClientController;

use Log, PDF; //Mail, Slugify
use DB;


use Bouncer; 

class RepairorderController extends Controller {


	// DA LI TESTIRA RJEŠITI PREKO ENV VARIJABLE
//	static private $testing; // da li da komunicira sa T2 servisima TRUE= NEMOJ KOMUNICIRATI!


	//	public function __construct() \App\Http\Controllers\SoapClientController $sc)
//	public function __construct()
//	{
		// DA LI TESTIRA RJEŠITI PREKO ENV VARIJABLE, ako je nema, onda se ne testira (web)
//		self::$testing=env('DISABLE_T2SOAP', false);
//	}


	static private $paginationCount=0; // 	

	static private $serviceprovider_id=1;

	// "druga" usluga - enable input name and jm
	static private $otherservice=6;

	//statuses
	// -- zaprmianje
	static private $status_open=1; 					// otvoren nalog
	static private $status_pos=2; 					// zaprimljen na POS
	static private $status_inhouse=3;				// zaprimljen u servis
	// -- servis
	static private $status_service=4;				// servis u tijeku
	// STARO: static private $status_offer=5;					// poslana ponuda
	static private $status_waitresponse=6;			// čeka se odgovor stranke
	static private $status_waitpart=7;				// čeka se dio
	// -- gotov serivs
	static private $status_over=8;					// servis završen
	static private $status_rejected=9;				// odustanak od servisa
	// -- treba doradu
	static private $status_needrelocation=10;		// treba prebaciti u drugi servis
	// -- otprema
	static private $status_moved=11;				// prebačeno u drugi servis
	// -- Otpreljeno prema kupcu
	static private $status_shipped=12;				// otpremljeno iz servisa


	// STARO:: static private $status_rejected_shipped=13;		// otpremljeno iz servisa nepopravljano

	// -- Otpreljeno prema drugom servisu na doradu
	static private $status_movedfix = 13;                // prebačeno iz servisa prema drugom servisu na doradu


	// -- izbrisan
	static private $status_deleted=14;				// admin izbrisao nalog

	static private $stsPrincipalId = 137;


	//claimtypes
	static private $claimtype_doa = 2;
	static private $claimtype_dap = 3;
	static private $claimtype_qc = 6;

	static private $no_posID=120; 	// NEPOZNAT POS
	static private $no_gradID=0;	// NEPOZNAT GRAD

	static private $devicereturntype_OTHER = 4;

	// disclaimers
	public static $disclaimers = [
		'Uređaj je testiran. Svi sustavi su ispravni i rade u okviru specifikacija proizvođača.',
	];

	public static function getStatusiNeedRelocation(){
		return self::$status_needrelocation;
	}

	public static function getStatusDeleted(){
		return self::$status_deleted;
	}

	public static function getStsPrincipalId(){
		return self::$stsPrincipalId;
	}

	public static function getStatusipredotpremu(){
		return array(self::$status_over, self::$status_rejected);
	}

	public static function getStatusiServis(){
		return array(self::$status_service,  self::$status_needrelocation, self::$status_waitpart, self::$status_waitresponse);
	}
	public static function getStatusiTrebaOdrediste()
	{
		return array(self::$status_needrelocation,  self::$status_moved, self::$status_movedfix);
	}
	
	public static function getStatusiOtpremljeno(){
		return array(self::$status_shipped);
	}

	

	public static function getOtherservice(){
		return self::$otherservice;
	}

	public static function getPaginationCount(){
		return self::$paginationCount;
	}


	static private 	$ro_rules_noimei = array(
					'deviceincomingimei' 		=> 'required',	
					'deviceoutgoingimei' 		=> 'required_if:stsdeviceswap,1|required_if:stsmbswap,1',	
					);

	static private 	$ro_rules_imei = array(
					'deviceincomingimei' 		=> 'required|digits:15',	
					'deviceoutgoingimei' 		=> 'required_if:stsdeviceswap,1|required_if:stsmbswap,1|digits:15',	
					);


	public static function ro_rules_all()
	{
	    return array(
						'devicemodel_id' 			=> 'required|numeric', //|min:1',	
						'devicebuydate'				=> 'date_format:"d.m.Y"',
						'posclaimdate'				=> 'date_format:"d.m.Y"',
						'devicemanufactureddate'	=> 'date_format:"m/Y"',
					    'posdevicereturntype_id'	=> 'required|numeric', //|min:1',
					    'posdevicereturnother'		=> 'required_if:posdevicereturntype_id,4',
					    'devicerecievetype_id'		=> 'required|numeric',
						'devicerecievedate'			=> 'required|date_format:"d.m.Y"',
					    "devicerecieveother"		=> 'required_if:devicerecievetype_id,2',
					    "relocationspp_id"			=> "required_if:status,".self::$status_needrelocation.", ".self::$status_movedfix.", ".self::$status_moved
						);
	}

	 

	static private $ro_messages = [
		    'devicemodel_id.required' => 'Odaberite <strong>BRAND/MODEL</strong> uređaja.',
		    'devicebuydate.date_format' => 'Datum računa - neispravan format datuma (npr. 28.02.2014).',
		    'posclaimdate.date_format' => 'Datum zaprimanja komitenta - neispravan format datuma (npr. 28.02.2014).',
		    'devicemanufactureddate.date_format' => 'Datum proizvodnje - neispravan format datuma (npr. 05/2014).',
		    'posdevicereturntype_id.required' => 'Obavezno unjeti vrstu otpreme uređaja',
		    'posdevicereturnother.required_if' => 'Otprema je odabrana <strong>DRUGO</strong>, upiši kako/kamo.',
		    'devicerecievetype_id.required' => 'Obavezno unjeti vrstu dopreme uređaja',
		    'devicerecievedate.required' => 'Obavezno unjeti datum dopreme uređaja',
		    'devicerecieveother.required_if' => 'Doprema je odabrana <strong>DRUGO</strong>, molim upiši kako!',
		    'relocationspp_id.required_if' => 'Obavezno unjeti kuda treba poslati uređaj'
		    ];
	static private $ro_messages_imei = [
		    'deviceincomingimei.required' => 'Unesite <strong>IMEI1</strong> uređaja.',
		    'deviceoutgoingimei.required_if' => 'Kod zamjene uređaja ili MB obvezan je <strong>izlazni IMEI</strong> uređaja.',
		    'deviceincomingimei.digits' => 'Polje <strong>IMEI1</strong> mora imati 15 znamenki',
		    'deviceoutgoingimei.digits' => 'Polje <strong>Izlazni IMEI</strong> mora imati 15 znamenki',
		];
	static private $ro_messages_noimei = [
		    'deviceincomingimei.required' => 'Unesite <strong>Serijski broj ili IMEI1</strong> uređaja.',
		    'deviceoutgoingimei.required_if' => 'Kod zamjene uređaja ili MB obvezan je <strong>izlazni serijski broj ili IMEI</strong> uređaja.',

		];


/*

 	protected $soapClientController;

	public function __construct(\App\Http\Controllers\SoapClientController $sc)
	{
		$this->soapClientController=$sc;
	}
*/

	
	/**
	 * tipa, 404, zapravo forbidden
	 *
	 */
	public function zabrani($msg=null){
		dd("Neautorizirani pristup");
			//return View::make('zabranjeno');
	}
	


	public function startscreen(Request $request)
	{
	
	//Log::info("start screen");
	//


	if($request->has('imei') && $request->has('caseid') ){
	//		if (Input::has('imei') && Input::has('caseid')) {

			$imei = $request->input('imei');
			$caseid = $request->input('caseid');

// VALIDACIJA!


			
			//izvadi "datumzaprimanjanapm" i "id" za najnoviji Slucaj za taj ID (po datumu zaprimanja)
			//$slucaj = Repairorder::where('deviceincomingimei','=',$imei)->orderBy('posclaimdate','desc')->first();
			//bolje po datumu otvaranja naloga



//DB::enableQueryLog();
			$slucaj = \App\Models\Repairorder::where('deviceincomingimei','=',$imei)
								->where(function ($q) use ($caseid){
									$q->where('posrepairorderno',$caseid)
									  ->orWhere('stsrepairorderno',$caseid);
								})
								->orderBy('stsroopendate','desc')->first();
//print_r(DB::getQueryLog()[0]['query']);
//dd($slucaj);



			//Sorting A Collection By A Value
			//http://laravel.com/docs/5.0/eloquent#working-with-pivot-tables
			$slFirst=null;
			if (!is_null($slucaj)) $slFirst=$slucaj->repairstatuses->first();
			if (!empty($slucaj) && !empty($slFirst) ) { 

				$sendData=array(
					'imei'=>$imei,
					'nalog'=>$caseid,
					'imeidata'=>$slucaj
					);
				// IMA IMEIJA
				return View::make('check')->with($sendData);
				
			} else { 

				// NEMA IMEIJA
				return View::make('check')->withErrors('Ne postoje podaci za navedeni uređaj. Pokušajte kasnije.');			
			}
			
		} else return View::make('check');
		
	}















	public function arhiva(Request $request)
	{
		
		// UX
		//https://datatables.net/examples/server_side/defer_loading.html
		//učitam prvih 25kom (ili kolko već) i prikažem - da se popuni stranica , ostalo ide prek ajaxa

		$total=0;
		$slucajevi=null;

		try 
		{
			$data=DatatablesController::dohvatiArhivaIntro($request, 25);
			$total=$data['total'];
			$slucajevi=$data['nalozi'];
		} catch (Exception $e){
			dd("greška kod dohvaćanja inicijalnih naloga za prikaz arhivu.");
		}

		$sendData=[
				'total'=>$total,
				'slucajevi'=>$slucajevi,
			];

		return View::make('slucaj.arhiva')->with($sendData);		
	}	



	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{


/* SETUP: samo jednom... */

		/*
		// ABILITIJI!! >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

		//vidiSveNaloge
		Bouncer::allow('admin')->to('vidiSveNaloge');
		Bouncer::allow('logistika')->to('vidiSveNaloge');
		Bouncer::allow('servis')->to('vidiSveNaloge');
		Bouncer::allow('superadmin')->to('vidiSveNaloge');
	
		//vidiSppNaloge
		Bouncer::allow('superadmin')->to('vidiSppNaloge');
		Bouncer::allow('admin')->to('vidiSppNaloge');
		Bouncer::allow('logistika')->to('vidiSppNaloge');
		Bouncer::allow('servis')->to('vidiSppNaloge');
		Bouncer::allow('spp')->to('vidiSppNaloge');

		//modalStatusSuperAdminOpts
		Bouncer::allow('superadmin')->to('modalStatusSuperAdminOpts');



		dd("jesam");
		*/




		// VIEWOVI!! >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
		// DA LI POSTOJI TAJ VIEW?
		// // ovo izvaditi van, negdje di će se sigurno otvoriti!
		if(
			count(\DB::select("SHOW TABLES LIKE 'naplativiiznosi'")) == 0 ||
			count(\DB::select("SHOW TABLES LIKE 'zs'")) == 0
			) {

				// ZADNJI STATUSI
				\DB::connection('mysql-emulate-prepares')->statement("CREATE OR REPLACE VIEW zs AS 
								SELECT t1.id, t1.repairorder_id, t1.repairstatus_id, t1.user_id, t1.created_at as datumstatusa
								FROM repairorder_repairstatus t1 
								LEFT JOIN repairorder_repairstatus t2
									ON (t1.repairorder_id = t2.repairorder_id 
										AND t1.created_at < t2.created_at
										)
								WHERE t2.created_at IS NULL
				");

				// IZNOS KUNA PO NALOGU
				\DB::connection('mysql-emulate-prepares')->statement("CREATE OR REPLACE VIEW iznosnaloga AS
								SELECT repairorder_id as nalog, sum(price*qty) as iznos FROM repairorder_stsservice
								GROUP BY repairorder_id
								UNION ALL
								SELECT repairorder_id as nalog, sum(price*qty) as iznos FROM repairorder_sparepart
								GROUP BY repairorder_id
				");

				// NALPATIVO PO NALOGU (van jamstva)
				\DB::connection('mysql-emulate-prepares')->statement("CREATE OR REPLACE VIEW naplativiiznosi AS
								SELECT t1.nalog, sum(t1.iznos) as ukupno
								FROM repairorders 
								LEFT JOIN iznosnaloga t1 ON t1.nalog=repairorders.id
								WHERE repairorders.devicewarranty<>1
								GROUP BY t1.nalog
				");

				// VIEW ZA IZVJEŠTAJ DOA,QC,SERVISNI
				\DB::connection('mysql-emulate-prepares')->statement("CREATE OR REPLACE VIEW reportsOther AS
						SELECT
							principals.naziv as principal,
							repairorders.stsrepairorderno as radni_nalog,
						    repairorders.devicerecievedate as datum_dolaska,
						    repairorders.posclaimdate as datum_prijave,
						    brands.name as brand,
						    models.name as model,
						    devicetypes.naziv as tip_uredjaja,
						    repairorders.posrepairorderno as case_id,
						    repairorders.deviceincomingimei as imei,
						    repairorders.devicefailuredescription as kvar,
						    repairorders.stsnotice as komentar_servisa,
						    stsservicelevels.name as level,
						    if (repairorders.stsdoadap=1,'DA','NE') as doa,
						    CASE WHEN 	repairorders.devicereturntype_id=5 or 
										repairorders.devicereturntype_id=6 or 
										repairorders.devicereturntype_id=7 
								THEN 'NE' 
								ELSE 'DA'
							END as otpremljeno, 
						    poses.posname as prodajno_mjesto,
						    CONCAT(poses.posadresa, ',', grad_posa.postalcode, ' ', grad_posa.name) as prodajno_mjesto_adresa,
						    poses.posid as pos_id,
						    if (repairorders.devicepostpaid=1,'POST','PRE') as postpaid_prepaid,
						    repairorders.devicereturndate as datum_otpremanja,
							DATEDIFF(repairorders.devicereturndate, repairorders.devicerecievedate) as ttr,
						    if (repairorders.stsclaimtype_id=6,'DA','NE') as qc_zahtjev,
						    if (repairorders.stsqc=1,'DA','NE') as qc_ok,
						    if (naplativiiznosi.ukupno > 0, 'DA', 'NE') as naplata,


						    if (repairorders.devicewarranty=1,'DA','NE') as priznato_jamstvo,
						    if (repairorders.stsfa-iluredetected=1,'DA','NE') as greska_uocena,
						    if (repairorders.stsmbswap=1,'DA','NE') as mb_swap,
						    if (repairorders.stsdeviceswap=1,'DA','NE') as device_swap,


						    repairorders.stsroopendate as datum_otvaranja_naloga,
						    repairorders.stsroclosingdate as datum_zatvaranja_naloga,
						    YEAR(repairorders.stsroclosingdate) as godina_zatvaranja_naloga,
						    CONCAT(serviseri.first_name, ' ', serviseri.last_name) as zatvorio_serviser,
						    servisnelokacije.posname as servis_zatvaranja

						FROM repairorders
				    LEFT JOIN stsservicelevels ON repairorders.stsservicelevel_id = stsservicelevels.id
				    LEFT JOIN models ON repairorders.devicemodel_id = models.id
				    LEFT JOIN brands ON models.brand_id=brands.id
				    LEFT JOIN devicetypes ON models.devicetype_id = devicetypes.id

				    LEFT JOIN poses ON poses.id = repairorders.pos_id
				    LEFT JOIN principals ON poses.principal_id = principals.id
				    LEFT JOIN locplaces as grad_posa ON poses.posplace_id = grad_posa.id
				    LEFT JOIN naplativiiznosi ON repairorders.id = naplativiiznosi.nalog
				    LEFT JOIN users as serviseri ON repairorders.stsserviceperson_id=serviseri.id
				    LEFT JOIN poses as servisnelokacije ON servisnelokacije.id=repairorders.stsservicelocation_id

						ORDER BY repairorders.devicereturndate ASC
				");

				// VIEW ZA IZVJEŠTAJ DOA,QC,SERVISNI - zadnjih 45D
				\DB::connection('mysql-emulate-prepares')->statement("CREATE OR REPLACE VIEW reportsOther45D AS
						SELECT
							principals.naziv as principal,
							repairorders.stsrepairorderno as radni_nalog,
						    repairorders.devicerecievedate as datum_dolaska,
						    repairorders.posclaimdate as datum_prijave,
						    brands.name as brand,
						    models.name as model,
						    devicetypes.naziv as tip_uredjaja,
						    repairorders.posrepairorderno as case_id,
						    repairorders.deviceincomingimei as imei,
						    repairorders.devicefailuredescription as kvar,
						    repairorders.stsnotice as komentar_servisa,
						    stsservicelevels.name as level,
						    if (repairorders.stsdoadap=1,'DA','NE') as doa,
						    CASE WHEN 	repairorders.devicereturntype_id=5 or 
										repairorders.devicereturntype_id=6 or 
										repairorders.devicereturntype_id=7 
								THEN 'NE' 
								ELSE 'DA'
							END as otpremljeno, 
						    poses.posname as prodajno_mjesto,
						    CONCAT(poses.posadresa, ',', grad_posa.postalcode, ' ', grad_posa.name) as prodajno_mjesto_adresa,
						    poses.posid as pos_id,
						    if (repairorders.devicepostpaid=1,'POST','PRE') as postpaid_prepaid,
						    repairorders.devicereturndate as datum_otpremanja,
							DATEDIFF(repairorders.devicereturndate, repairorders.devicerecievedate) as ttr,
						    if (repairorders.stsclaimtype_id=6,'DA','NE') as qc_zahtjev,
						    if (repairorders.stsqc=1,'DA','NE') as qc_ok,

						    if (naplativiiznosi.ukupno > 0, 'DA', 'NE') as naplata,
						    if (repairorders.devicewarranty=1,'DA','NE') as priznato_jamstvo,
						    if (repairorders.stsfailuredetected=1,'DA','NE') as greska_uocena,
						    if (repairorders.stsmbswap=1,'DA','NE') as mb_swap,
						    if (repairorders.stsdeviceswap=1,'DA','NE') as device_swap,

						    repairorders.stsroopendate as datum_otvaranja_naloga,
						    repairorders.stsroclosingdate as datum_zatvaranja_naloga,

						    YEAR(repairorders.stsroclosingdate) as godina_zatvaranja_naloga,

						    CONCAT(serviseri.first_name, ' ', serviseri.last_name) as zatvorio_serviser,
						    servisnelokacije.posname as servis_zatvaranja

						FROM repairorders
				    LEFT JOIN stsservicelevels ON repairorders.stsservicelevel_id = stsservicelevels.id
				    LEFT JOIN models ON repairorders.devicemodel_id = models.id
				    LEFT JOIN brands ON models.brand_id=brands.id
				    LEFT JOIN devicetypes ON models.devicetype_id = devicetypes.id

				    LEFT JOIN poses ON poses.id = repairorders.pos_id
				    LEFT JOIN principals ON poses.principal_id = principals.id
				    LEFT JOIN locplaces as grad_posa ON poses.posplace_id = grad_posa.id
				    LEFT JOIN naplativiiznosi ON repairorders.id = naplativiiznosi.nalog
				    LEFT JOIN users as serviseri ON repairorders.stsserviceperson_id=serviseri.id
				    LEFT JOIN poses as servisnelokacije ON servisnelokacije.id=repairorders.stsservicelocation_id

				    	WHERE repairorders.devicereturndate > NOW() - INTERVAL 45 DAY

						ORDER BY repairorders.devicereturndate ASC
				");


		}

// VIEWOVI!! >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>








		/*
		idemo sad prek ajaxa dohvaćati json - datatablesController...
		 */

		// Temporarily increase memory limit to 256MB
		ini_set('memory_limit','256M');

		//ajmo vidjeti jeli SPP (POS) USER..
		//ako je, onda treba izvaditi samo radne naloge koji su zaprimljeni na tom posu!
		
		if (Bouncer::allows('vidiSveNaloge')){
			$slucajevi=\App\Models\Repairorder::whereNull('devicereturndate')->orderBy('posclaimdate','desc')->orderBy('updated_at','desc')->get();
		} else if (Bouncer::allows('vidiSppNaloge')){
			$spp_id=\Auth::user()->location;
			$slucajevi=\App\Models\Repairorder::whereNull('devicereturndate')
									->where(function ($q) use ($spp_id)  {
										$q->where('pickuppos_id',$spp_id)
											->orWhere('pos_id',$spp_id);
										})
									->orderBy('updated_at','desc')
									->orderBy('posclaimdate','desc')
									->get();
		} else {
			$slucajevi=null;
		}

		$adminUser = Bouncer::is(\Auth::user())->a('admin'); //->an('admin');

		$sendData=[
				'adminUser'=>$adminUser,
				'slucajevi'=>$slucajevi,
				'arhiva'=>false,
				'cont'=>'otvoreno'
			];


		return View::make('slucaj.index')->with($sendData);		
		//return $this->index();
		//return View::make('slucaj.index')->with(array('adminUser'=>$adminUser,'slucajevi'=>$slucajevi));		
		
	}


	/**
	 *  Funkcija samo u tablicu naloga pristiglih putem web servisa postavlja u polje
	 *  sts_repairorder_number broj novogeneriranog naloga
	 *  Na taj način taj web-servis nalog postaje "zatvoren"
	 */
	public function closeSoapCase($t2sID,$stscaseId){

			// ako je, treba u t2soap tablici unjeti br novog radnog naloga
			$t2nalog=\App\Models\Soap\ws_tele2soap::find($t2sID);

			if (!empty($t2nalog)) {
				$t2nalog->sts_repairorder_number=$stscaseId;
				$t2nalog->save();
				return $t2nalog->caseId;
			}

			return false;
	}

	public function updateSoapStatus(SoapClientController $soapClientController, $caseId, $STS_statusId, $status_pivot_id, $comment="", $remark="", $imei="", $reason="", $mbswap=0, $doadap=0, $claimtype=1, $deviceswap=0, $servicerejected=0) {
				//if (self::$testing) return false;

				// iz tablice ws_tele2soap_statuse izvaditi "code" prema sts_repairstatus_id,
				// ako ga nema nikome niš...
				try{

					//callOrderUpdate(
					//	$caseId, 
					//	$repairStatus, 
					//	$remark="", 
					//	$comment="", 
					//	$imei="", 
					//	$reason="", 
					//	$serviceStatus="", 
					//	$serviceType=""){


					$repairStatus=\App\Models\Soap\ws_tele2soap_statuses::where("sts_repairstatus_id",$STS_statusId)->firstOrFail()->code;

					// serviceStatus valjda još ne šaljemo...
					$serviceStatus="";

					// defaultni serviceType
					$serviceType="";

					// ako je repair status = 103 (ili 104) service->type mora biti_
					/*
					Ovo su keywordi koje smo dogovorili sa ostalim servisima:
						FIX     = Popravljeno
						FIXMB   = Popravljeno uz zamjenu matične ploče
						DAP     = DAP
						DOA     = DOA
						RPL     = Zamjena uređaja
						RJC     = Odbijeno
					 */

					// kod DOA/DAP prati se inicijalni zahtjev
					//

					// ako je ZATVORENO ili OTPREMLJENO šalji i serviceTYPE!
            		if($repairStatus==103 || $repairStatus==104){

            			// servis odbijen
            			if ($servicerejected==1) {
            				$serviceType="RJC";

            			// da li je bio tražen i prihvaćen doa ili dap?
            			} else if ($doadap==1 && in_array($claimtype, array(self::$claimtype_dap, self::$claimtype_doa))){
        					if ($claimtype == self::$claimtype_doa) {
								$serviceType="DOA";
        					} else {
								$serviceType="DAP";
							}

            			} else if($mbswap==1) { 
	           				$serviceType="FIXMB";

            			} else if ($deviceswap==1) {
            				$serviceType="RPL";

            			} else {
            				$serviceType="FIX";
            			}

            		} 

					// imam sve, pošalji soap paket... ALI prije toga, u PIVOT statusa,
					// spremi da sam probao poslati...

					$tried_at = date("Y-m-d H:i:s");

					$sql=DB::table('repairorder_repairstatus')
						->where('id',$status_pivot_id)
						->update(['ws_triedtoinform'=>$tried_at]);

					/*
					 * ili ovak nekak...
					https://laracasts.com/discuss/channels/general-discussion/updating-a-recorde-on-a-pivot-table
					  $patient->medications()
						->newPivotStatement()
						->where('id', $id)->update(....)
					*/

					$soapcallresponse= $soapClientController->callOrderUpdate($caseId, $repairStatus, $remark, $comment, $imei, $reason, $serviceStatus, $serviceType);


					// provjeri da li response ima formu:
					/*
						[status] => stdClass Object
								(
									[errorCode] => 0
									[message] => OK
								)
					*/
					// u pivot treba upisati i odgovore
					$ec="SOAP: nema error code";
					$em="SOAP: nema error message";
					if (isset($soapcallresponse->status)){
						if (isset($soapcallresponse->status->errorCode)) $ec=$soapcallresponse->status->errorCode;
						if (isset($soapcallresponse->status->message)) $em=$soapcallresponse->status->message;
					}
					$sql=DB::table('repairorder_repairstatus')
						->where('id',$status_pivot_id)
						->update(['ws_responsecode'=>$ec, 'ws_responsemsg'=>$em]);



					return true;

				} catch (\Exception $e) { //\Illuminate\Database\Eloquent\ModelNotFoundException
					return false; //$code=null;
				}

		}


	public function rejectSoapCase(SoapClientController $soapClientController, $t2sID)
	{
		//if (self::$testing) return false;

		// odbaci nalog (tipa, krivo su poslali)
		// 
		//ALTER TABLE `ws_tele2soap` ADD `rejected` DATETIME NULL DEFAULT NULL AFTER `sts_repairorder_number`, ADD `rejectedbyuser` INT(11) NULL AFTER `rejected`;
		// staviti da uzima u obzir i rejected u SERVICES, shell gore badge desno

		$t2nalog=\App\Models\Soap\ws_tele2soap::find($t2sID);
		if (!empty($t2nalog) && !($t2nalog->sts_repairorder_number > 0) && is_null($t2nalog->rejected)) {

				// zapisati kad je rejected i tko je rejecto
				$t2nalog->rejected=date("Y-m-d H:i:s");  // na datetime sadašnje
				$t2nalog->rejectedbyuser = Auth::user()->id; // userid
				$t2nalog->save();

				// poslati 103 i servicetype ="RJC" 
				$caseId = $t2nalog->caseId; 
				$imei = $t2nalog->device_imei;
				$repairStatus = 103; // ili 104? otpremljeno? 
				$serviceType="RJC";
				$remark = "Uređaj ne spada u naš servis"; 
				$comment = "";
				$reason = ""; 
				$serviceStatus = "";


				try {

					$soapClientController->callOrderUpdate(
						$caseId, 
						$repairStatus, 
						$remark, 
						$comment, 
						$imei, 
						$reason, 
						$serviceStatus, 
						$serviceType
						);

				} catch (\Exception $e) { //\Illuminate\Database\Eloquent\ModelNotFoundException
					//	dd($e);
					return false; 
				} 

		}

		return back()->with('message','Tele2 nalog odbijen!');
	}

	public function createFromSoap($t2sID)
	{

		$slucaj=null;
		$t2s=null;

		// provjeri da li je još uvijek "otvoren"
		$t2nalog=\App\Models\Soap\ws_tele2soap::find($t2sID);
		if (!empty($t2nalog) && !($t2nalog->sts_repairorder_number > 0) && $t2nalog->rejected==null) {


			// ok, može dalje...
			//$equipment1 = "W3siaWQiOjYsImRlc2NyaXB0aW9uIjoiQmx1ZXRvb3RoIn0seyJpZCI6OCwiZGVzY3JpcHRpb24iOiJNaWNyb3NpbSBob2xkZXIifV0=";
			//$equipment2 = "W3siaWQiOjQsImRlc2NyaXB0aW9uIjoiQmF0ZXJpamEifSx7ImlkIjo2LCJkZXNjcmlwdGlvbiI6IkJsdWV0b290aCJ9LHsiaWQiOjEsImRlc2NyaXB0aW9uIjoiSmFtc3R2byJ9LHsiaWQiOjUsImRlc2NyaXB0aW9uIjoiTWVtb3JpanNrYSBrYXJ0aWNhIn0seyJpZCI6OCwiZGVzY3JpcHRpb24iOiJNaWNyb3NpbSBob2xkZXIifSx7ImlkIjoxMiwiZGVzY3JpcHRpb24iOiJOaVx1MDE2MXRhIn0seyJpZCI6NywiZGVzY3JpcHRpb24iOiJPbG92a2EifSx7ImlkIjoxMCwiZGVzY3JpcHRpb24iOiJQb2tsb3BhYyBiYXRlcmlqZSJ9LHsiaWQiOjEzLCJkZXNjcmlwdGlvbiI6IlByb2Rham5pIHBha2V0In0seyJpZCI6MywiZGVzY3JpcHRpb24iOiJQdW5qYVx1MDEwZCJ9LHsiaWQiOjIsImRlc2NyaXB0aW9uIjoiUmFcdTAxMGR1biJ9LHsiaWQiOjksImRlc2NyaXB0aW9uIjoiU0lNIGthcnRpY2EifSx7ImlkIjoxMSwiZGVzY3JpcHRpb24iOiJTbHVcdTAxNjFhbGljZSJ9XQ==";
			//$equipment3 = "W3siaWQiOjUsImRlc2NyaXB0aW9uIjoiTWVtb3JpanNrYSBrYXJ0aWNhIn0seyJpZCI6MTMsImRlc2NyaXB0aW9uIjoiUHJvZGFqbmkgcGFrZXQifV0=";
			//$damage1 = "W3siaWQiOjMsImRlc2NyaXB0aW9uIjoiS3ZhciAtIFVyZVx1MDExMWFqIHNlIGdhc2kifV0=";
			//$damage2 = "W3siaWQiOjQsImRlc2NyaXB0aW9uIjoiS3ZhciAtIEd1Ymkgc2lnbmFsXC9tcmVcdTAxN2VhIn0seyJpZCI6MTYsImRlc2NyaXB0aW9uIjoiS3ZhciAtIEhETUkgcHJpa2xqdVx1MDEwZGthIn0seyJpZCI6NSwiZGVzY3JpcHRpb24iOiJLdmFyIC0gTmVpc3ByYXZuYSBiYXRlcmlqYSJ9LHsiaWQiOjE0LCJkZXNjcmlwdGlvbiI6Ikt2YXI";
			//$damage3 = "W3siaWQiOjQsImRlc2NyaXB0aW9uIjoiS3ZhciAtIEd1Ymkgc2lnbmFsXC9tcmVcdTAxN2VhIn0seyJpZCI6MywiZGVzY3JpcHRpb24iOiJLdmFyIC0gVXJlXHUwMTExYWogc2UgZ2FzaSJ9LHsiaWQiOjMzLCJkZXNjcmlwdGlvbiI6Ik9cdTAxNjF0ZVx1MDEwN2VuamUgLSBwcmVkbmplZyBrdVx1MDEwN2lcdTAxNjF0YSJ9XQ==";

			// PRIPREMI / PROCESIRAJ POLJA IZ T2SOAP NALOGA
			$slucaj = new \App\Models\Repairorder();
			$t2s = array();

				//dd($t2nalog['attributes']);

				$t2s = $t2nalog['attributes'];

				// - Oprema / equipment
				// 		Base64 encoded json (k=>v)
				// 		$t2nalog->repairorder_equipment;
				$equipment=json_decode(base64_decode($t2s['repairorder_equipment']));
				$ids=array();
				$opremaString="";
				if (!is_null($equipment)) {
					// daj za svaki izvadi ID i OPIS (sve opise ćemo poslati u "other")
					foreach($equipment as $eq) {
						$ids[]=$eq->id;
						$opremaString.=$eq->description.", ";
						}
					// izvadi svu opremu čiji se idjevi nalaze u polju
					$oprema=(!empty($ids)) ? \App\Models\Soap\ws_tele2soap_equipment::whereIn('id', $ids)->get() : null;
					//ako ima opreme, nađi polja iz šifrarnika i postavi ih na "1"
					//dd($oprema);
					if (!is_null($oprema)){
						foreach ($oprema as $item) {
							$polje=$item->sts_equipment_field;
							$slucaj->$polje=1;
						}
						//dd($slucaj);
	
					}
					$slucaj->deviceaccrest=$opremaString;
					//$slucaj['deviceaccrest']=$opremaString;
				}



				// - Opis kvara / damage - CUSTROMER SYMPTOMS
				// 		Base64 encoded json (k=>v)
				// 		$t2nalog->repairorder_damage;
				$damage=json_decode(base64_decode($t2s['repairorder_damage']));
				$ids=array();
				$damageString="";
				//dd($damage);
				if (!is_null($damage)) {
					// daj za svaki izvadi ID i OPIS (sve opise ćemo poslati u "other")
					foreach($damage as $dm) {
						$ids[]=$dm->id;
						$damageString.=$dm->description.", \n";
						}
					$slucaj['damage']=(empty($ids)) ? null : $ids;
					$slucaj->devicefailuredescription=$damageString;
				}

				// - Brand - probati ću preskoćiti za sada...
				//		*MOGUĆI BRANDOVI:
				//			TELE2FON
				//			NOA
				//			ZOPO
				// 		probaj prema dolaznom stringu po tablici odredi naš ID, ako nejde ostavi prazno
				// 		id spremi u novo polje!
				//$temp = strtoupper($t2nalog->device_brand);
				//$tempField=\App\Models\Soap\ws_tele2soap_brands::where('title',$temp)->first();
				//$t2s['device_brand_id'] = (!empty($tempField)) ? $tempField->sts_brand_id : null;

				// - Model
				// 		probaj prema dolaznom stringu po tablici odredi naš ID, ako nejde ostavi prazno
				// 		id spremi u novo polje!
				$temp = strtoupper($t2nalog->device_model);
				$tempField=\App\Models\Model::where('name','LIKE','%'.$temp.'%')->first();
				//$slucaj['devicemodel_id'] = (!empty($tempField)) ? $tempField->id : null;
				$slucaj->devicemodel_id = (!empty($tempField)) ? $tempField->id : null;

				// - Grad korisnika
				// 		probaj prema dolaznom stringu po tablici odredi naš ID, ako nejde ostavi prazno
				$temp = strtoupper($t2s['contact_address_postcode']);
				$tempField=\App\Models\Locplace::where('postalcode','=',$temp)->first();
				//$slucaj['customerplace_id'] = (!empty($tempField)) ? $tempField->id : null;
				$slucaj->customerplace_id = (!empty($tempField)) ? $tempField->id : null;

				// ostali...

				$slucaj->devicetcode=$t2s['device_code'];
				$slucaj->deviceincomingimei=$t2s['device_imei'];
				$slucaj->posrepairorderno=$t2s['caseId'];
				$slucaj->customerstreet=$t2s['contact_address_street'];
				$slucaj->customername=$t2s['contact_firstname'];
				$slucaj->customerlastname=$t2s['contact_lastname'];
				$slucaj->customerphone1=$t2s['contact_phone1'];
				$slucaj->customerphone2=$t2s['contact_phone2'];
				$slucaj->pospriority=$t2s['repairorder_priority'];
				$slucaj->posmessage=$t2s['repairorder_comment'];
				$slucaj->devicetype=$t2s['device_type'];

	
				if (!is_null($t2s['device_buydate'])) {
					try {

						$slucaj->devicebuydate	= \Carbon\Carbon::parse($t2s['device_buydate']);//->format('Y-m-d H:i:s');
					} catch (Exception $e){
						dd($e);
					}
					//$slucaj->devicebuydate	= \Carbon\Carbon::createFromFormat('Y/m/d H:i:s', $t2s['device_buydate']);//->format('Y-m-d H:i:s');
				}

				if (!is_null($t2s['repairorder_receiveddate'])) {
					try {

						$slucaj->posclaimdate	= \Carbon\Carbon::parse($t2s['repairorder_receiveddate']);//->format('Y-m-d H:i:s');
					} catch (Exception $e){
						dd($e);
					}
					
					//$slucaj->posclaimdate = \Carbon\Carbon::createFromFormat('Y/m/d H:i:s', $t2s['repairorder_receiveddate']);//->format('Y-m-d H:i:s');
				}

								
				// - POS 
				// ako taj posID ne postoji, onda postavi na id=119 ( posID=0, gdje je principal = 2 (Tele2))
				// to je defaultni za Tele2, a podatke o POSU dodaj u napomenu!!
				$tempField=\App\Models\Pos::where('posid',$t2s['pos_id'])->where('principal_id','2')->first();
				//if (!empty($tempField)) $slucaj['pos_id']=$tempField->id;
				if (!empty($tempField)) $slucaj->pos_id=$tempField->id;
				else {
					//$slucaj['pos_id']=self::$no_posID;
					$slucaj->pos_id=self::$no_posID;
					//$slucaj['posmessage'].=" || Nepostojeći POS: "
					$slucaj->posmessage.=" |*| Nepostojeći POS: "
											."POSID=".$t2s['pos_id']
											.", NAZIV=".$t2s['pos_name']
											.", TEL=".$t2s['pos_phone']
											.", ADRESA=".$t2s['pos_address_street']
											.", GRAD=".$t2s['pos_address_place']
											.", PBR=".$t2s['pos_address_postcode']
											.", ZEMLJA=".$t2s['pos_address_country'];		
				}


				// - CLAIM TYPE 
				// izvadi claimtype i prema njemu iz ws_tele2soap_claimtypes tablice sts_claimtype i to utrpaj, ak ne onda je 1
				$tip= \App\Models\Soap\ws_tele2soap_claimtypes::where('code', $t2s['claimtype'])->first();
				//ako ima opreme, nađi polja iz šifrarnika i postavi ih na "1"
				if (!is_null($tip)){
					//$slucaj['posclaimtype_id']=$tip->sts_posclaimtype_id;
					$slucaj->posclaimtype_id=$tip->sts_posclaimtype_id;
				} else {					
					//$slucaj['posclaimtype_id']=1;
					$slucaj->posclaimtype_id=1;
				}


			/*
			

			*u polju 'service->type' će se slati vrijednost „DOA“ u slučaju prihvaćenog/utvrđenog DOA statusa u servisu

			*POVRATNI STATUSI:
				101         Servisni centar: Zaprimljeno
				105         Servisni centar: U servisnoj obradi
				102         Servisni centar: Na čekanju
				103         Servisni centar: Završeno
				104         Servisni centar: Otpremljeno

					Želim samo napomenuti da je dodan status ”105 - Servisni centar: U servisnoj obradi” i da se njega treba koristiti kada se uređaj uzme u servisnu proceduru, te ukoliko se tada uoči mehaničko oštećenje može se koristiti status ”102 - Servisni centar: Na čekanju“ dok se čeka recimo uplata korisnika za servis.				
				S ovim dodatnim statusom omogućavamo da se vide promjene u komentarima kada se čeka na neki dio ili na uplatu od korisnika. Jedino na što treba jako pripaziti jest broj novog statusa br. 105 dok je br. 104 finalni status kada je uređaj završen.
			 */

		} // od provjere da li je SOAP nalog već zaprimljen u servis

		return $this->create($slucaj, $t2s);	
	}




	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create($slucaj = null, $t2s = null)
	{
		//http://www.neontsunami.com/posts/using-lists%28%29-in-laravel-with-custom-attribute-accessors		

		//default values
		if (!isset($slucaj['pos_id']) || !($slucaj['pos_id']>0)) $slucaj['pos_id']=self::$no_posID;
		if (!isset($slucaj['customerplace_id']) || !($slucaj['customerplace_id']>0) ) $slucaj['customerplace_id']=self::$no_gradID;


		$modeli_brandovi=\App\Models\Model::get()->lists('brand_model','id')->all();
		$statusi=\App\Models\Repairstatus::lists('name','id')->all();
		$posevi = \App\Models\Pos::with('postype')->orderBy('posid')->get()->lists('custom_name','id')->all();
		$gradovi = \App\Models\Locplace::orderBy('name', 'asc')->get()->lists('custom_name','id')->all();
		$claimtypes = \App\Models\Claimtype::lists('name','id')->all();
		$servicelevels = \App\Models\Stsservicelevel::lists('name','id')->all();
		$servicelocations = \App\Models\Pos::where('principal_id','=',self::$stsPrincipalId)->lists('posname','id')->all();

		//logirani korisnik!
		$servicepersonid = Auth::user()->id; //id
		$servicepersonuser = Auth::user()->user; //ime		
		$servicepersons = \App\Models\User::lists('user','id')->all(); //lista svih zaposlenika
		$servicepersonscustom = \App\Models\User::all()->lists('servis_user','id');
		$servicepersonlocation = Auth::user()->location;
		$servicepersonlocationname = \App\Models\Pos::where('id','=',$servicepersonlocation)->first()->posname;

		$customersymptoms = \App\Models\Customersymptom::get()->lists('custom_name','id')->all();
		$faultyelements = \App\Models\Faultyelement::get()->lists('custom_name','id')->all();
		$techniciansymptoms = \App\Models\Techniciansymptom::get()->lists('custom_name','id')->all();
		$spareparts = \App\Models\Sparepart::with('stock')->get();//get()->lists('custom_name', 'id');
		$stsservices = \App\Models\Stsservice::all();//get()->lists('custom_name','id');
	
		$posdevicereturntypes=\App\Models\Devicereturntype::where('showonspp','=','1')->lists('name','id')->all();
		$devicerecievetypes=\App\Models\Devicerecievetype::lists('name','id')->all();

		$pickuppos_id = $servicepersonlocation;
		$pickupposname = $servicepersonlocationname;

		$showServis=false;
		$showOtprema=false;
		$activeServis="";
		$activeUlaz="active";
		$activeIzlaz="";
		$disabledFields=null;

		$caseZaprimljen=false;

		$SPPuser=false;

		// AKO JE POS / SPP - pošalji i podatke o komitentu.
		if (Bouncer::is(\Auth::user())->an('spp')){
			$SPPuser=true;
			$slucaj['pos_id']=Auth::user()->location;
			$slucaj['posclaimdate']=date("d.m.Y");
			$disabledFields['pos_id']=true;
			$disabledFields['posrepairorderno']=true;
		}

		return View::make('slucaj.create')->with(array(
			'SPPuser'=>$SPPuser, 'disabledFields'=>$disabledFields,
			'pickuppos_id'=>$pickuppos_id,'pickupposname'=>$pickupposname, 
			'servicepersonlocationname'=>$servicepersonlocationname, 
			'showOtprema'=>$showOtprema,'activeIzlaz'=>$activeIzlaz, 
			'activeUlaz'=>$activeUlaz, 'activeServis'=>$activeServis,
			'showServis'=>$showServis, 'devicerecievetypes'=>$devicerecievetypes, 
			'devicerecieveother'=>'', 'posdevicereturnother'=>'','posdevicereturntypes'=>$posdevicereturntypes,
			'slucaj'=>$slucaj, 't2s'=>$t2s, 
			'servicepersonuser'=>$servicepersonuser, 'servicepersonid'=>$servicepersonid, 
			'servicepersonlocation'=>$servicepersonlocation,'modeli_brandovi'=>$modeli_brandovi,
			'statusi'=>$statusi, 'posevi'=>$posevi, 'gradovi'=>$gradovi, 'claimtypes'=>$claimtypes, 
			'servicelevels'=>$servicelevels, 'servicelocations'=>$servicelocations, 
			'servicepersons'=>$servicepersons, 'customersymptoms'=>$customersymptoms, 
			'techniciansymptoms'=>$techniciansymptoms, 'faultyelements'=>$faultyelements, 
			'spareparts'=>$spareparts, 'stsservices'=>$stsservices, 'caseZaprimljen'=>$caseZaprimljen
			)); 
	}







	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(SoapClientController $sc, Request $request, $id=null)
	{
		// validate
		//http://daylerees.com/trick-validation-within-models

		// vidjeti koje rules koristiti (imei ili ne?) -------------------------------------------------------
		$testmodel= \App\Models\Model::find($request->input('devicemodel_id'));
		if (empty($testmodel)) {
			return Redirect::back()->withInput()->withErrors(self::$ro_messages['devicemodel_id.required']);
		} else if ($testmodel->devicetype->imaimei== 1) {
			$this->validate($request, self::$ro_rules_imei+self::ro_rules_all(), self::$ro_messages+self::$ro_messages_imei);
		} else {
			$this->validate($request, self::$ro_rules_noimei+self::ro_rules_all(), self::$ro_messages+self::$ro_messages_noimei);
		}
		// ---------------------------------------------------------------------------------------------------

		/* TODO: DA LI IMA NEKI NAČIN ZA SPREMITI PUNO POLJA (MOŽDA IMA AK SE ISTO ZOVU POLJA U FORMI KO ONA U DB) */
		$myErrors=array();

		// slučaj
		$slucaj=(is_null($id)) ? new \App\Models\Repairorder : \App\Models\Repairorder::withTrashed()->find($id);

		// SERVICE PROVIDER ID 
		$slucaj->serviceprovider_id			= self::$serviceprovider_id;
		// DOPREMA
		$slucaj->devicerecievetype_id		= $request->input('devicerecievetype_id');
		if (null !== $request->input('devicerecieveother')) $slucaj->devicerecieveother =  $request->input('devicerecieveother');
		// DEVICE
		$slucaj->devicemodel_id				= $request->input('devicemodel_id');
		$slucaj->deviceincomingimei			= $request->input('deviceincomingimei');
		$slucaj->devicefailuredescription	= $request->input('devicefailuredescription');
		$slucaj->devicetcode				= $request->input('devicetcode');
		$slucaj->devicefailuredescription	= $request->input('devicefailuredescription');
		$slucaj->deviceincomingsasref		= $request->input('deviceincomingsasref');
		$slucaj->deviceincomingswversion	= $request->input('deviceincomingswversion');
		$slucaj->deviceinvoiceno			= $request->input('deviceinvoiceno');
		$slucaj->devicepostpaid				= $request->input('devicepostpaid');
		$slucaj->devicewarrantycardno		= $request->input('devicewarrantycardno');
		$slucaj->deviceotherbuyplace		= $request->input('deviceotherbuyplace');
		// OPERMA
		$slucaj->deviceaccbattery			= ($request->input('deviceaccbattery') === '1') ?  '1' : '0';
		$slucaj->deviceacccharger			= ($request->input('deviceacccharger') === '1') ?  '1' : '0';
		$slucaj->deviceaccantenna		 	= ($request->input('deviceaccantenna') === '1') ?  '1' : '0';
		$slucaj->deviceaccsim		 		= ($request->input('deviceaccsim') === '1') ?  '1' : '0';
		$slucaj->deviceaccusbcable			= ($request->input('deviceaccusbcable') === '1') ?  '1' : '0';
		$slucaj->deviceaccmemorycard		= ($request->input('deviceaccmemorycard') === '1') ?  '1' : '0';
		$slucaj->deviceaccheadphones		= ($request->input('deviceaccheadphones') === '1') ?  '1' : '0';
		$slucaj->deviceaccrest				= $request->input('deviceaccrest');
		// KORISNIK
		$slucaj->customername				= $request->input('customername');
		$slucaj->customerlastname			= $request->input('customerlastname');
		$slucaj->customerstreet				= $request->input('customerstreet');
		$slucaj->customerplace_id			= $request->input('customerplace_id');
		$slucaj->customeremail				= $request->input('customeremail');
		$slucaj->customerphone1				= $request->input('customerphone1');
		$slucaj->customerphone2				= $request->input('customerphone2');			
		// KOMITENT
		$slucaj->pos_id						= $request->input('pos_id');
		$slucaj->posmessage					= $request->input('posmessage');
		$slucaj->pospriority				= $request->input('pospriority');
		$slucaj->posrepairorderno			= $request->input('posrepairorderno');
		$slucaj->posclaimtype_id			= $request->input('posclaimtype_id');
		// OTPREMA
		$slucaj->posdevicereturntype_id		= $request->input('posdevicereturntype_id');
		if (null !== $request->input('posdevicereturnother')) {
			$slucaj->posdevicereturnother 		=  $request->input('posdevicereturnother');
		}
		// CARBON dates:
		if (!empty($request->input('devicebuydate'))) $slucaj->devicebuydate					= \Carbon\Carbon::createFromFormat('d.m.Y', $request->input('devicebuydate'))->format('Y-m-d');
		if (!empty($request->input('devicemanufactureddate'))) $slucaj->devicemanufactureddate	= \Carbon\Carbon::createFromFormat('m/Y', $request->input('devicemanufactureddate'))->format('Y-m-d');
		if (!empty($request->input('posclaimdate'))) $slucaj->posclaimdate						= \Carbon\Carbon::createFromFormat('d.m.Y', $request->input('posclaimdate'))->format('Y-m-d');
		if (!empty($request->input('devicerecievedate')) && Bouncer::is(\Auth::user())->notAn('spp')) $slucaj->devicerecievedate = \Carbon\Carbon::createFromFormat('d.m.Y', $request->input('devicerecievedate'))->format('Y-m-d');
		// STS DATA
		$slucaj->stsnotice					= $request->input('stsnotice');
		$slucaj->stsservicelevel_id			= $request->input('stsservicelevel_id');
		$slucaj->stsdoadap					= ($request->input('stsdoadap') === '1') ?  '1' : '0';
		$slucaj->stsfailuredetected			= ($request->input('stsfailuredetected') === '1') ?  '1' : '0';
		$slucaj->stsqc						= ($request->input('stsqc') === '1') ?  '1' : '0';
		$slucaj->stsmbswap					= ($request->input('stsmbswap') === '1') ?  '1' : '0';
		$slucaj->devicewarranty				= ($request->input('devicewarranty') === '1') ?  '1' : '0';
		$slucaj->stsdeviceswap				= ($request->input('stsdeviceswap') === '1') ?  '1' : '0';
		$slucaj->stsdeviceswap				= ($request->input('stsdeviceswap') === '1') ?  '1' : '0';

		$slucaj->devicereturnnotice			= $request->input('devicereturnnotice');

		if (is_null($id)) { // STORE NOVI ================================================================================
			// na početku spremi isto kao i SPP unos
			$slucaj->devicereturntype_id		= $request->input('posdevicereturntype_id');
			if (null !== $request->input('posdevicereturnother')) {
				$slucaj->devicereturnother 			=  $request->input('posdevicereturnother');
			}
			// novi radni nalog, snimi samo defaultne startne vrijednosti 
			$slucaj->deviceoutgoingimei			= $request->input('deviceincomingimei');
			$slucaj->stsroopendate				= date('Y-m-d');//Input::get('stsroopendate');
			// nađi koji je pickup pos_id (iz korisnika?) Treba negdje napisati?
			$slucaj->pickuppos_id				= $request->input('pickuppos_id');
			$slucaj->stsserviceperson_id		= Auth::user()->id;//Input::get('stsserviceperson_id');
			//$slucaj->stsnotice					= "\n\n\n".self::$disclaimers[0];
			$slucaj->stsservicelocation_id		= $request->input('stsservicelocation_id');//Auth::user()->location;
			$slucaj->stsrepairorderno 			= "NOVI NALOG";

			//za sada (novi nalog) stavi da je sts_claimtype_id = posclaimtype_id
			$slucaj->stsclaimtype_id			= $request->input('posclaimtype_id');



			// prikupi podatke koji trebaju za naziv naloga, spremi nalog, naziv, prvi status
			// i vidi da li treba obavjestiti T2
			try {
					// prikupiti podatke za naziv naloga:
					$servis=\App\Models\Pos::find($slucaj->stsservicelocation_id);
					$prefiks= $servis->posid;			

					// SPREMITI RADNI NALOG DA DOBIJEM ID ----------------------------------------
					$noviid=$slucaj->save();

					// SPREMITI NAZIV RADNOG NALOGA ----------------------------------------------
					$slucaj->stsrepairorderno = sprintf($prefiks.'%09d',$slucaj->id); 

					// SPREMITI 1. STATUS --------------------------------------------------------
					// ako logistika otvara, staviti 2
					// ako servis otvara staviti 2
					// ako admin otvara nek isto bude 2
					$status=self::$status_inhouse;
					// ako spp otvara, staviti 3
					if (Bouncer::is(\Auth::user())->an('spp')) 
					{
						$status=self::$status_pos;
					}
					$slucaj->repairstatuses()->attach($status, array('locationspp_id'=>Auth::user()->location,'loggeduser_id'=>Auth::user()->id,'user_id'=>$slucaj->stsserviceperson_id)); //In this example, the repairorder_id field will automatically be set on the inserted comment.

					// DA LI JE NALOG STIGAO PUTEM T2 WEB SERIVSA?
					if ($request->input("wsTele2id")>0) {
						$zadnji_pivot_id= $slucaj->repairstatuses->first()->pivot->id;
						$slucaj->fromws=1; // da se zna da je od web servisa (radi updatea)
						// zatvori SOAP nalog i vrati caseId 
						$caseId=$this->closeSoapCase($request->input("wsTele2id"),$slucaj->id);
						if ($caseId>0) {
							// javi da je zaprimljen!
							$this->updateSoapStatus($sc, $caseId, self::$status_inhouse, $zadnji_pivot_id);
						}		
					}

			} catch (Exception $e){
				dd($e);
			}


		} else { // EDIT STARI ===========================================================================================

			$slucaj->deviceoutgoingimei			= $request->input('deviceoutgoingimei')|"";
			$slucaj->deviceoutgoingsasref		= $request->input('deviceoutgoingsasref')|"";
			$slucaj->deviceoutgoingswversion	= $request->input('deviceoutgoingswversion')|"";

			$inputStatusID=$request->input('status');
			$stari_stsserviceperson_id = $slucaj->stsserviceperson_id;

			$slucaj->stsclaimtype_id			= $request->input('stsclaimtype_id');

			$slucaj->devicereturntype_id		= $request->input('devicereturntype_id');
			if (null !== $request->input('devicereturnother')) {
				$slucaj->devicereturnother 			=  $request->input('devicereturnother');
			}
			
		}

		// SPREMITI RADNI NALOG ------------------------------------------------------------
		$slucaj->save();

		// Customersymptoms ------------------.---------------------------------------------
		$coltemp=$request->input('customersymptoms');
		$slucaj->customersymptoms()->sync($coltemp ?: []);  // znači mora imati ARRAY, makar prazan [], nesmije biti null

		// AKO JE EDIT ---------------------------------------------------------------------
		if (!is_null($id)) {

			// #########################################################
		// ### SPREMALJE SVIH OSTALIH RELACIJA (OSIM PRVOG STATUSA!)
		// 

			// # Status -----------------------------------------------------------
			if (!empty($inputStatusID)){ // a nebi smio biti prazan!

				if(
						(				
						// da li nepostoji nikakav status?
						is_null($slucaj->repairstatuses->first()) 
						// ili je novi status?
						|| $slucaj->repairstatuses->first()->repairstatus_id != $inputStatusID
						// ili je novi korisnik?
						|| $stari_stsserviceperson_id != $request->input('stsserviceperson_id')
						// ili je nalog s SPP-a, a korisnik nije SPP
						|| ($inputStatusID == self::$status_pos && Bouncer::is(\Auth::user())->notAn('spp'))
						// ili se promijenilo mjesto za relokaciju
						|| $slucaj->repairstatuses->first()->relocationspp_id !== $request->input('relocationspp_id')	
						) && (
							(
							// ako je nalog otpremljen, nema više spremanja statusa!
							!is_null($slucaj->repairstatuses->first()) 
							&& $slucaj->repairstatuses->first()->repairstatus_id != self::$status_shipped
							)

							||

							(
							// osim ako je novi status INHOUSE i to radi ADMIN (tipa korisnik ne želi preuzeti uređaj i on se vraća na servis)
							!is_null($slucaj->repairstatuses->first())  && 
							$slucaj->repairstatuses->first()->repairstatus_id==self::$status_shipped && $inputStatusID==self::$status_inhouse && Bouncer::is(Auth::user())->an('admin')
							)
						)					
				) {

					$newattachedstatus=false;

					// da li nepostoji nikakav status za nalog?
					if(is_null($slucaj->repairstatuses->first()))  {

						// valjda je došlo do neke greške kod otvaranja naloga... 
						// SPREMITI 1. STATUS --------------------------------------------------------
						// ako logistika ili servis ili admin otvara, staviti 2
						$status=self::$status_inhouse;
						// ako spp otvara, staviti 3
						if (Bouncer::is(\Auth::user())->an('spp')) {
							$newattachedstatus=self::$status_pos;
						}
						$slucaj->repairstatuses()->attach($newattachedstatus, array('loggeduser_id'=>Auth::user()->id,'user_id'=>$slucaj->stsserviceperson_id, 'locationspp_id'=>\App\Models\User::find($slucaj->stsserviceperson_id)->location)); //In this example, the repairorder_id field will automatically be set on the inserted comment.
						// SPREMITI NALOG --------------------------------------------------------
						if ($slucaj->stsrepairorderno=="NOVI NALOG"){
							$servis=\App\Models\Pos::find($slucaj->stsservicelocation_id);
							$prefiks= $servis->posid;			
							$slucaj->stsrepairorderno = sprintf($prefiks.'%09d',$slucaj->id); 
							$slucaj->save();
						}

						$zadnji_pivot_id= $slucaj->repairstatuses->first()->pivot->id;


					} else { // postoji "prvi" status

						// zapamti ovo prije promjena
						$zadnji_pivot_id= $slucaj->repairstatuses->first()->pivot->id;
						$zadnji_status_id= $slucaj->repairstatuses->first()->repairstatus_id;





						// AKO JE BRISANJE, BRIŠI...
						if ($zadnji_status_id !== self::$status_deleted && $inputStatusID == self::$status_deleted && Bouncer::is(\Auth::user())->an('admin')) {
							$newattachedstatus = self::$status_deleted;
								
							/*
								kod brisanja, u
								App\Providers\AppServiceProvider.php
								je postavljeno da se automatski sprema i status! (tak da tu netreba)

								$slucaj->repairstatuses()->attach($newattachedstatus, array('loggeduser_id'=>Auth::user()->id, 'user_id'=>Auth::user()->id,'locationspp_id'=>Auth::user()->location));

							 */

							$slucaj->delete();


						}
						// ako nije SPP user a postavljen je zaprimljen na SPP, onda zaprimi u servisu ili relokacija
						elseif ($zadnji_status_id == self::$status_pos) {
							
							// tko smije promijeniti ako nalog ima status ZAPR NA SPP
							//$smijupromijeniti = array('admin','logistika');

							if ($inputStatusID==self::$status_inhouse && Bouncer::is(\Auth::user())->a('admin','logistika')) {
								$newattachedstatus=self::$status_inhouse;
								$slucaj->repairstatuses()->attach($newattachedstatus, array('loggeduser_id'=>Auth::user()->id, 'user_id'=>Auth::user()->id,'locationspp_id'=>Auth::user()->location));
							} elseif (in_array($inputStatusID, array(self::$status_moved, self::$status_movedfix)) && Bouncer::is(\Auth::user())->a('admin','logistika')) {
								// spremi i odredište (za move i movefix)
								$newattachedstatus=$inputStatusID;
								$slucaj->repairstatuses()->attach($newattachedstatus, array('loggeduser_id'=>Auth::user()->id, 'user_id'=>$request->input('stsserviceperson_id'), 'relocationspp_id'=>$request->input('relocationspp_id'),'locationspp_id'=>\App\Models\User::find($request->input('stsserviceperson_id'))->location ));
							}

						// ako je poslan na doradu ili isporuku?
						} elseif (in_array($zadnji_status_id, array(self::$status_movedfix, self::$status_moved))) {
							
							// tko smije promijeniti ako nalog ima status relokacija za isporuku ili rel za servis
							//$smijupromijeniti = array('admin','logistika');

							if (in_array($inputStatusID, array(self::$status_inhouse, self::$status_shipped)) && Bouncer::is(\Auth::user())->a('admin','logistika')) {

								$newattachedstatus=$inputStatusID;

								$slucaj->repairstatuses()->attach($newattachedstatus, array('loggeduser_id'=>Auth::user()->id, 'user_id'=>Auth::user()->id,'locationspp_id'=>Auth::user()->location));
							} elseif (in_array($inputStatusID, array(self::$status_moved, self::$status_movedfix)) && Bouncer::is(\Auth::user())->a('admin','logistika')) {
								// spremi i odredište (za move i movefix)
								
								$newattachedstatus=$inputStatusID;

								$slucaj->repairstatuses()->attach($newattachedstatus, array('loggeduser_id'=>Auth::user()->id, 'user_id'=>$request->input('stsserviceperson_id'), 'relocationspp_id'=>$request->input('relocationspp_id'),'locationspp_id'=>\App\Models\User::find($request->input('stsserviceperson_id'))->location));
							}

						// ako treba ići na doradu
						} elseif ($zadnji_status_id==self::$status_needrelocation) {
							
							// tko smije promijeniti ako nalog ima status prebaci u drugi servis
							//$smijupromijeniti = array('admin','logistika');

							// Vanja tražio 20.05.2016 - admin može vratiti u servis u tijeku
							if ($inputStatusID==self::$status_service && Bouncer::is(\Auth::user())->an('admin')){


								// zakvači status
								$newattachedstatus=$inputStatusID;
								$slucaj->repairstatuses()->attach($newattachedstatus, array('loggeduser_id'=>Auth::user()->id, 'user_id'=>$request->input('stsserviceperson_id'),'locationspp_id'=>\App\Models\User::find($request->input('stsserviceperson_id'))->location));



							// sa tog može ići samo na moveFIX
							} elseif (in_array($inputStatusID, array(self::$status_movedfix)) && Bouncer::is(\Auth::user())->a('admin','logistika')) {
								// spremi i odredište (za movefix)

								$newattachedstatus=$inputStatusID;

								$slucaj->repairstatuses()->attach($newattachedstatus, array('loggeduser_id'=>Auth::user()->id, 'user_id'=>$request->input('stsserviceperson_id'), 'relocationspp_id'=>$request->input('relocationspp_id'),'locationspp_id'=>\App\Models\User::find($request->input('stsserviceperson_id'))->location));
							}

						} elseif ($zadnji_status_id==self::$status_shipped && $inputStatusID==self::$status_inhouse && Bouncer::is(\Auth::user())->an('admin')) {
							// admin smo rekli 19.04 kod štefa da može vratiti nalog u servis
							// obrisati returned data...

							$newattachedstatus=self::$status_inhouse;

							$slucaj->repairstatuses()->attach($newattachedstatus, array('loggeduser_id'=>Auth::user()->id, 'user_id'=>Auth::user()->id,'locationspp_id'=>Auth::user()->location));
							$slucaj->stsroclosingdate		= null;
							$slucaj->devicereturndate		= null;
							$slucaj->devicereturnperson_id	= null;			
							$slucaj->save();
																

						} else { // NIJE NALOG sa statusom zaprimljen na SPP, niti poslan na doradu ili isporuku

							// ako je status koji treba odredište, u pivot spremi i odredište
							if (in_array($inputStatusID,array(self::$status_needrelocation,self::$status_moved, self::$status_movedfix))) {		

								// tko smije promijeniti na te statuse?
								if ($inputStatusID==self::$status_needrelocation && Bouncer::is(\Auth::user())->a('admin','servis')) {

									// SPREMI I SERVISEPERSON!!
									$slucaj->stsserviceperson_id	= $request->input('stsserviceperson_id');
									$slucaj->stsservicelocation_id	= \App\Models\User::find($request->input('stsserviceperson_id'))->location;
									$slucaj->save();
									// spremi taj novi status u pivot, zajedno sa logiranim userom i odredištem!

									$newattachedstatus=$inputStatusID;

									$slucaj->repairstatuses()->attach($newattachedstatus, array('loggeduser_id'=>Auth::user()->id, 'user_id'=>$request->input('stsserviceperson_id'), 'relocationspp_id'=>$request->input('relocationspp_id'),'locationspp_id'=>\App\Models\User::find($request->input('stsserviceperson_id'))->location));					
								}
									
								if (in_array($inputStatusID,array(self::$status_movedfix,self::$status_moved)) && Bouncer::is(\Auth::user())->a('admin','logistika')) 
								{

									$newattachedstatus=$inputStatusID;

									// spremi taj novi status u pivot, zajedno sa logiranim userom i odredištem!
									$slucaj->repairstatuses()->attach($newattachedstatus, array('loggeduser_id'=>Auth::user()->id, 'user_id'=>$request->input('stsserviceperson_id'), 'relocationspp_id'=>$request->input('relocationspp_id'),'locationspp_id'=>\App\Models\User::find($request->input('stsserviceperson_id'))->location));					
								}

							// ako je otprema, trebam u bazu spremiti današnji datum i logiranu osobu
							} elseif ($inputStatusID==self::$status_shipped){

								if (Bouncer::is(\Auth::user())->a('admin','logistika')) {
									$slucaj->devicereturndate		= date('Y-m-d');
									$slucaj->devicereturnperson_id	= Auth::user()->id;			
									$slucaj->save();

									$newattachedstatus=$inputStatusID;

									$slucaj->repairstatuses()->attach($newattachedstatus, array('loggeduser_id'=>Auth::user()->id, 'user_id'=>$request->input('stsserviceperson_id'),'locationspp_id'=>\App\Models\User::find($request->input('stsserviceperson_id'))->location));
								}

							// ako su servisni statusi...
							} elseif ( in_array($inputStatusID, array(self::$status_service,self::$status_waitresponse, self::$status_waitpart, self::$status_over, self::$status_rejected)) ){ 		

								// tko smije?
								if (Bouncer::is(\Auth::user())->a('admin','servis')) {

									// može samo ako je nalog još uvijek "otvoren" - nije imao "servis završen" ili "odustanak" do sada!! (možda bolje provjeriti da li je valid datum)
									

									// dodano kod ŠTefa: ADMIN MOŽE "otvoriti" nalog -> poslije ZAVRŠEN staviti U TIJEKU
									// 
									if (Bouncer::is(\Auth::user())->an('admin') && $inputStatusID==self::$status_service && in_array($zadnji_status_id, array(self::$status_rejected,self::$status_over)) ) {

										// OTVORI!
										$slucaj->stsroclosingdate	= null;
										$slucaj->stsserviceperson_id	= $request->input('stsserviceperson_id');
										$slucaj->stsservicelocation_id	= \App\Models\User::find($request->input('stsserviceperson_id'))->location;
										$slucaj->save();
										// zakvači status
										$newattachedstatus=$inputStatusID;
										$slucaj->repairstatuses()->attach($newattachedstatus, array('loggeduser_id'=>Auth::user()->id, 'user_id'=>$request->input('stsserviceperson_id'),'locationspp_id'=>\App\Models\User::find($request->input('stsserviceperson_id'))->location));


									} elseif (is_null($slucaj->stsroclosingdate) || trim($slucaj->stsroclosingdate)=="") {


										########
										#
										#  NE MOŽE SPREMITI OVER ili REJECTED AKO NIJE BIO U SERVISU! 
										#  		znači, prije ta dva, morao je imati jedan od:
										#  			- status_service
										#  			- status_waitpart
										#  			- status_waitresponse
										#  			
										# 
										########
										if (in_array($inputStatusID,array(self::$status_over,self::$status_rejected) )&& !in_array($zadnji_status_id, array(self::$status_service,self::$status_waitpart,self::$status_waitresponse)) )	
										{

											// ne spremaj niš,
											// javi poruku
											$myErrors[]='Nalog ne može biti zatvoren i ne može se od njega odustati ako ga nije pregledao serviser, odnosno ako prethodno nema postavljen neki od statusa: SERVIS U TIJEKU, ČEKA SE DIO, ČEKA SE ODGOVOR OD STRANKE.';

										} else {

												if (in_array($inputStatusID,array(self::$status_over,self::$status_rejected))){
													// ako su završeni servisi, onda spremi i datum zatvaranja
													$slucaj->stsroclosingdate		= date('Y-m-d');
												}

												// spremi servisera koji je radio i njegovu lokaciju
												$slucaj->stsserviceperson_id	= $request->input('stsserviceperson_id');
												$slucaj->stsservicelocation_id	= \App\Models\User::find($request->input('stsserviceperson_id'))->location;
												$slucaj->save();

												$newattachedstatus=$inputStatusID;

												$slucaj->repairstatuses()->attach($newattachedstatus, array('loggeduser_id'=>Auth::user()->id, 'user_id'=>$request->input('stsserviceperson_id'),'locationspp_id'=>\App\Models\User::find($request->input('stsserviceperson_id'))->location));


										}




									} else { // od provjere jel zatvoren?
										$myErrors[]='Nalog je zatvoren za servis i nije moguće postaviti novi servisni status. Admin može postaviti iz SERVIS ZAVRŠEN u SERVIS U TIJEKU';
									}

								}

							}


						} // od NIJE SPP NALOG

					} // postoji neki nalog

					// TELE2 - javi im status, ako ih zanima!
					// vidi da li je to TELE2SOAP radni nalog
					if($slucaj->fromws == 1) {

						$comment=$request->input('stsnotice');
						$imei=$request->input('deviceincomingimei');
						$reason="";
						$remark="";
	 					$mbswap=($slucaj->stsmbswap == 1) ? 1 : 0;
	 					$doadap=($slucaj->stsdoadap == 1) ? 1 : 0;
	 					$claimtype=$slucaj->posclaimtype_id;
	 					$deviceswap=($slucaj->stsdeviceswap == 1) ? 1 : 0;
	 					$servicerejected= (in_array($inputStatusID, array(self::$status_rejected))) ? 1 : 0;
						$this->updateSoapStatus($sc, $slucaj->posrepairorderno, $inputStatusID, $zadnji_pivot_id, $comment, $remark, $imei, $reason, $mbswap, $doadap, $claimtype, $deviceswap, $servicerejected);
					}

				} // dal ima neke promjene
				else {
					$myErrors[]='Nema promjene statusa. Ukoliko je nalog trenutno "OTPREMLJEN IZ SERVISA" Admin može postaviti na "ZAPRIMLJENO U SERVISU".';
				}


			} // dal je prazan inputStatusID = SPREMANJE STATUSA



			// # Spareparts -------------------------------------------------------
					$coltemp=$request->input('sppt');

					//prvo zapamti kaj ima i onda isprazni sve staro vezano uz slučaj
					$tempsppts=$slucaj->spareparts->all();
					// isprazni REPAIRORDER-SPAREPARTS
					$slucaj->spareparts()->detach();

					// ok, sad si oslobodio, trebalo bi te količine vratiti na lager!!
					foreach ($tempsppts as $tempsppt) {
						// INSERT ... ON DUPLICATE KEY UPDATE
						$sp=\App\Models\Sparepart::find($tempsppt->pivot->sparepart_id);
						$whid=$tempsppt->pivot->spwarehouse_id;

						$qtypivot=$sp->stock()->where('spwarehouse_id',$whid)->first();
						if (is_null($qtypivot)) {
							$sp->stock()->attach($whid, array('qty'=>$tempsppt->pivot->qty));
						} else {
							$qtypivot->pivot->qty += $tempsppt->pivot->qty;
							$qtypivot->pivot->save();
						}
					}		

					if (is_array($coltemp)){
						foreach ($coltemp as $item) {
							if ($item['qty']>0) {
								// smanji sa tog lagera
								$cq= \App\Models\Sparepart::find($item['ids'])->stock()->where('spwarehouse_id',$item['whs'])->first();
								if (!is_null($cq)) {
									// kolko ima na lageru?
									$lagerqty=$cq->pivot->qty;
									// jel ima na lageru dovoljno...
									if ($lagerqty>=$item['qty']) {
										// smanji
										$lagerqty= $lagerqty-$item['qty'];
										$ro_sp_qty=$item['qty'];
									} else {
										$ro_sp_qty=$lagerqty;
										$lagerqty= 0;
										$myErrors[]='nedovoljne količine';
									}
									$cq->pivot->qty=$lagerqty;
									$cq->pivot->save();
									// i spremi u pivot REPAIRORDER-SPAREPARTS 
									$slucaj->spareparts()->attach($item['ids'], array('spwarehouse_id'=>$item['whs'],'price'=>$item['prc'],'qty'=>$ro_sp_qty)); 
								}
							}
						}
					}


			// # Services ---------------------------------------------------------
					$coltemp=$request->input('stssrv');
					// ako je edit, prvo isprazni sve staro vezano uz slučaj
					if(!is_null($id)) $slucaj->stsservices()->detach();
					if (is_array($coltemp)){
						foreach ($coltemp as $item) {
							if ($item['qty']>0)
								$slucaj->stsservices()->attach($item['ids'], array('price'=>$item['prc'],'qty'=>$item['qty'],'savedname'=>$item['nme'], 'savedjm'=>$item['jm'])); 
						}
					}


			// # Faultyelements ---------------------------------------------------
					$coltemp=$request->input('faultyelements');
					$slucaj->faultyelements()->sync($coltemp ?: []);  // znači mora imati ARRAY, makar prazan [], nesmije biti null




			// # Technician symptoms ----------------------------------------------
					// ako je edit, prvo isprazni sve staro vezano uz slučaj
					if(!is_null($id)) $slucaj->techniciansymptoms()->detach();

					$coltemp=$request->input('techniciansymptom_id'); // GLAVNI 
					if (!empty($coltemp)) $slucaj->techniciansymptoms()->attach($coltemp, array('main'=>'1'));  

					$coltemp=$request->input('techniciansymptomothers'); // OSTALI
					if (!empty($coltemp)) $slucaj->techniciansymptoms()->attach($coltemp, array('main'=>'0'));  


		} // spremanje relacija kod EDITa
		// #########################################################




		// #########################################################
		// ajax ili normalni submit?
		if($request->ajax()){


					$newstatuslist=false;
					$newstatusid=false;
					$historymodalcontent=false;
					if(isset($newattachedstatus) && $newattachedstatus) {
						$newstatuslist=$this->availableStatuses($newattachedstatus);
						$newstatusid=$newattachedstatus;
						$historymodalcontent=\App\Http\Controllers\ModalController::iscrtajHistoryModal($slucaj->id);
					} 

		
			        if (empty($myErrors)){
						$response = array(
					            'status' => 'success',
					            'msg' => 'Spremljeno',
					            'newstatuslist' => $newstatuslist,
					            'newstatusid' => $newstatusid,
					            'historymodalcontent'=>$historymodalcontent
				        );
				    } else {
						$response = array(
					            'status' => 'warning',
					            'msg' => 'Pažnja: '.implode(",",$myErrors),//.", ".implode(",",$errors),
					            'newstatuslist' => $newstatuslist,
					            'newstatusid' => $newstatusid,
					            'historymodalcontent'=>$historymodalcontent
				        );
				    }
			        return \Response::json($response);  // <<<<<<<<< see this line

		} else {

		        // redirect
		        if (empty($myErrors)){

		        	$msg='Radni nalog:<strong>'.$slucaj->stsrepairorderno.'</strong> uspješno spremljen.
						<a target="_blank" href="'.\URL::to('printPrijemniView/rn/'.$slucaj->id).'" class="btn btn-default">Prijemni list</a>';
					if (!empty($inputStatusID) && 
						($inputStatusID==self::$status_over || 
						 $inputStatusID==self::$status_rejected ||
						 $inputStatusID==self::$status_shipped 
						 )) {
						$msg.='<a target="_blank"  href="'.\URL::to('printView/rn/'.$slucaj->id).'" class="btn btn-default">Radni nalog</a>';
					}
					$msg.='<a href="'.\URL::to('slucaj/'.$slucaj->id.'/edit').'" class="btn btn-default">Izmjeni nalog</a>';
					Session::flash('message', $msg);

				} else {

					// ak ima greški, opet otvori edit
					return Redirect::to('slucaj/'.$slucaj->id.'/edit')->withErrors($myErrors);

				}




			
				switch ($request->input('doafterrostore')) {
					case "edit":
						return Redirect::to('slucaj/'.$slucaj->id.'/edit');
						break;
					case "new":
						return Redirect::to('slucaj/create');
						break;
					default:	//case "home":
						return Redirect::to('slucaj')->withErrors($myErrors);
				}

		} // Ajax submit?

	}



	public function edit($id)
	{

		if (!($slucaj = \App\Models\Repairorder::withTrashed()->find($id))) {
			return $this->zabrani();
		}


		// ako nesmije vidjeti sve naloge, a nije njegov ili ostavljen kod njega, zabrani!
		if (Bouncer::denies('vidiSveNaloge') 
			 && 
			!in_array(\Auth::user()->location, array($slucaj['pickuppos_id'], $slucaj['pos_id']))
			) {
			return $this->zabrani();
		}



		//$brandovi = Brand::lists('name','id'); // TODO: - chained sa model //
		//$modeli = Model::lists('name','id');

			$modeli_brandovi=\App\Models\Model::get()->lists('brand_model','id')->all();

			$statusi=\App\Models\Repairstatus::lists('name','id')->all();
			$gradovi = \App\Models\Locplace::orderBy('name', 'asc')->get()->lists('custom_name','id')->all();
		//http://www.neontsunami.com/posts/using-lists%28%29-in-laravel-with-custom-attribute-accessors		
			$posevi = \App\Models\Pos::with('postype')->orderBy('posid')->get()->lists('custom_name','id')->all();	
			$claimtypes = \App\Models\Claimtype::lists('name','id')->all();
			$servicelevels = \App\Models\Stsservicelevel::lists('name','id')->all();
			$servicelocations = \App\Models\Pos::where('principal_id','=',self::$stsPrincipalId)->lists('posname','id')->all();
			$servicepersons = \App\Models\User::withTrashed()->get()->lists('user','id');
			$servicepersonscustom = \App\Models\User::withTrashed()->get()->lists('servis_user','id');
			$activeservicepersonscustom = \App\Models\User::get()->lists('servis_user','id');



		$servicepersonid = Auth::user()->id;
		$servicepersonuser = Auth::user()->user;		
		$servicepersonlocation = Auth::user()->location;	
		$servicepersonlocationname = \App\Models\Pos::where('id','=',$servicepersonlocation)->first()->posname;

			$customersymptoms = \App\Models\Customersymptom::get()->lists('custom_name','id')->all();
			$techniciansymptoms = \App\Models\Techniciansymptom::get()->lists('custom_name','id')->all();
			$faultyelements = \App\Models\Faultyelement::get()->lists('custom_name','id')->all();
		$spareparts = \App\Models\Sparepart::with('stock')->get();//get()->lists('custom_name', 'id');
		$stsservices = \App\Models\Stsservice::all();//get()->lists('custom_name','id');


		$otherserviceid=self::$otherservice;



	$showServis=false;
	$showOtprema=false;
	$activeUlaz="active";
	$activeIzlaz="";
	$activeServis="";
	$disabledFields=null;
	$SPPuser=false;


/*
	ostaviti za odabir SAMO MOGUĆE STATUSE + trenutni!

 */


//	if (!is_null($slucaj) && count($slucaj->repairstatuses)) {
	if (!is_null($slucaj) && !is_null($slucaj->repairstatuses->first())) {
		$currentStatus=$slucaj->repairstatuses->first();
		$currentStatus=$currentStatus->repairstatus_id;
	} else {
		$currentStatus="";
	}
	



		$statusi = $this->availableStatuses($currentStatus, $statusi);

		// KOJI TAB PRIKAZATI:
		if (Bouncer::is(\Auth::user())->a('admin','logistika'))  { 				
				$showOtprema=true;
				$showServis=true;
				$activeUlaz="active";
				$activeIzlaz="";
				$activeServis="";
				
				if(in_array($currentStatus, array(self::$status_needrelocation, self::$status_moved, self::$status_over, self::$status_rejected))) {
					$activeUlaz="";
					$activeIzlaz="active";
				}

			}


		if (Bouncer::is(\Auth::user())->a('admin','servis')) {
			$showServis=true;
			$activeUlaz="";
			$activeIzlaz="";
			$activeServis="active";
		}

		
		if (Bouncer::is(\Auth::user())->a('admin','spp')) {
			$showOtprema=false;
			$SPPuser=true;
		}

		if (Bouncer::is(\Auth::user())->an('admin')) {
			$showServis=true;
			$showOtprema=true;
			$activeUlaz="";
			$activeIzlaz="";
			$activeServis="active";
			$SPPuser=false;
		} 



		/* 
			bez obzira na ko otvara nalog, ako je status:
				- OTVOREN NALOG 
			znači da još nije zaprimljen u SERVISU, onemogući SERVIS TAB! - samo zaprimanje
		*/
		if ($currentStatus==self::$status_open) {

				$showServis=false;
				$activeUlaz="active";
				$activeServis="";
				$activeIzlaz="";

		}

		//	ako je ZAPRIMLJEN NA SPP-u 
		// 	a korisnik je bilo tko tko nije SPP, može SERVIS
		if ($currentStatus==self::$status_pos) {

			// ako je zaprimljen na posu...
				$showServis=false;
				$activeUlaz="active";
				$activeServis="";
				$activeIzlaz="";

		}







		// da li je zatvoren radni nalog?
		$caseZaprimljen = (in_array($currentStatus, array(self::$status_open, self::$status_pos))) ? false : true;
		$caseClosed = (in_array($currentStatus, array(self::$status_shipped))) ? true : false;
		$serviceOver = (in_array($currentStatus, array(self::$status_over, self::$status_rejected))) ? true : false;
		$adminUser = Bouncer::is(\Auth::user())->an('admin');

		$ct_DOA=self::$claimtype_doa;
		$ct_DAP=self::$claimtype_dap;


		$devicereturntypes=\App\Models\Devicereturntype::lists('name','id')->all();
		$posdevicereturntypes=\App\Models\Devicereturntype::where('showonspp','=','1')->lists('name','id')->all();
		$devicerecievetypes=\App\Models\Devicerecievetype::lists('name','id')->all();

		$pickuppos_id = $slucaj->pickuppos_id;
		if ($slucaj->id < 1627) {
			$pickupposname="STS (zajednički)"; 
		} else {
			$pickupposname = \App\Models\Pos::where('id','=',$pickuppos_id)->first()->posname;
			// :PROMJENA: za naloge do uvođenja SPP-a nećemo pisati GDJE su zaprimljeni!
		}

		$historymodalcontent=\App\Http\Controllers\ModalController::iscrtajHistoryModal($slucaj->id);

		// DA LI JE VIŠE PUTA NA SERVISU _ START -_________________
		$exservisi= $slucaj->exservisi()->get();
		// DA LI JE VIŠE PUTA NA SERVISU _ END   -_________________

		$sendData=array(
				'exservisi'=>$exservisi,
				'historymodalcontent'=>$historymodalcontent,
				'pickuppos_id'=>$pickuppos_id,
				'pickupposname'=>$pickupposname,
				'activeUlaz'=>$activeUlaz,
				'activeIzlaz'=>$activeIzlaz,
				'activeServis'=>$activeServis,
				'showServis'=>$showServis,
				'showOtprema'=>$showOtprema,
				'devicerecievetypes'=>$devicerecievetypes,
				'devicerecieveother'=>$slucaj->devicerecieveother,
				'devicereturntypes'=>$devicereturntypes,
				'devicereturnother'=>$slucaj->devicereturnother,
				'posdevicereturntypes'=>$posdevicereturntypes,
				'posdevicereturnother'=>$slucaj->posdevicereturnother,
				'ct_DOA'=>$ct_DOA, 
				'ct_DAP'=>$ct_DAP,
				'serviceOver'=>$serviceOver,
				'servicepersonlocation'=>$servicepersonlocation,
				'servicepersonlocationname'=>$servicepersonlocationname,
				'adminUser'=>$adminUser,
				'caseClosed'=>$caseClosed,
				'caseZaprimljen'=>$caseZaprimljen,
				'servicepersonid'=>$servicepersonid,
				'servicepersonuser'=>$servicepersonuser,
				'no_posID'=>self::$no_posID,
				'no_gradID'=>self::$no_gradID,
				'editingID'=>$id,
				'slucaj'=>$slucaj,
				'modeli_brandovi'=>$modeli_brandovi,
				'statusi'=>$statusi, 'posevi'=>$posevi,
				'gradovi'=>$gradovi, 'claimtypes'=>$claimtypes,
				'servicelevels'=>$servicelevels,
				'servicelocations'=>$servicelocations,
				'servicepersons'=>$servicepersons,
				'customersymptoms'=>$customersymptoms,
				'techniciansymptoms'=>$techniciansymptoms,
				'faultyelements'=>$faultyelements,
				'spareparts'=>$spareparts,'stsservices'=>$stsservices,
				'otherserviceid'=>$otherserviceid,
				'disclaimers'=>self::$disclaimers, 
				'disabledFields'=>$disabledFields,
				'SPPuser'=>$SPPuser,
				'servicepersonscustom'=>$servicepersonscustom,
				'activeservicepersonscustom'=>$activeservicepersonscustom,
			);


		return View::make('slucaj.create')->with($sendData); 
	}









	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(SoapClientController $sc, Request $request, $id)
	{

		return $this->store($sc, $request, $id);

	}




	/**
	 * vraća moguće statuse, ovisno o trenutnom i logiranom korisniku
	 */
	public function availableStatuses($currentStatus, $statusi=null){

		if (!$statusi) $statusi=\App\Models\Repairstatus::lists('name','id')->all();

		$statusIDs=array();


		if (Bouncer::is(\Auth::user())->a('admin','logistika'))  { 

				if($currentStatus==self::$status_pos){
				// zaprimljen na SPP
					array_push($statusIDs,self::$status_inhouse);
					array_push($statusIDs,self::$status_movedfix);

				} elseif($currentStatus==self::$status_needrelocation) {
				// treba poslati na doradu
					array_push($statusIDs,self::$status_movedfix);

				} elseif($currentStatus==self::$status_open) {
				// samo otvoren 
					array_push($statusIDs,self::$status_inhouse);
					array_push($statusIDs,self::$status_movedfix);

				} elseif(in_array($currentStatus, array(self::$status_over, self::$status_rejected))) {
				// gotov servis
					array_push($statusIDs,self::$status_shipped); 
					array_push($statusIDs,self::$status_moved); 

				} elseif($currentStatus==self::$status_movedfix) {
				// stigao na doradu
					array_push($statusIDs,self::$status_movedfix);
					array_push($statusIDs,self::$status_moved);
					array_push($statusIDs,self::$status_inhouse);

				} elseif($currentStatus==self::$status_moved) {
				// stigao za isporuku
					array_push($statusIDs,self::$status_movedfix);
					array_push($statusIDs,self::$status_moved);
					array_push($statusIDs,self::$status_shipped);
					array_push($statusIDs,self::$status_inhouse);
				}

			}


		// SERVIS
		if (Bouncer::is(\Auth::user())->a('admin','servis')) {

			if(in_array($currentStatus,array(self::$status_inhouse, self::$status_service, self::$status_waitpart, self::$status_waitresponse))) {

				array_push($statusIDs,self::$status_service,self::$status_needrelocation, self::$status_waitresponse, self::$status_waitpart, self::$status_over, self::$status_rejected);
			}
		}

		
		// SPP
		if (Bouncer::is(\Auth::user())->a('admin','spp')) {

			if ($currentStatus==self::$status_pos) {
				array_push($statusIDs,self::$status_pos); 
			}
		}


		// DODATNO JOŠ KAO ADMIN
		if (Bouncer::is(\Auth::user())->an('admin')) {

			if($currentStatus==self::$status_shipped) {
				array_push($statusIDs,self::$status_inhouse);
			} elseif (in_array($currentStatus, array(self::$status_rejected, self::$status_over))) {
				array_push($statusIDs,self::$status_service);
			} elseif (in_array($currentStatus, array(self::$status_needrelocation))) {
				array_push($statusIDs,self::$status_service);
			}
			array_push($statusIDs,self::$status_deleted);

		} 



		// da li je trenutni u listi? ako nije, dodaj ga!
		if (!in_array($currentStatus,$statusIDs)) array_push($statusIDs, $currentStatus);

		// ostavi samo te ID'e
		foreach ($statusi as $k=>$v) {
			if (!in_array($k,$statusIDs)) unset($statusi[$k]);
		}

		return $statusi;

	}

















	public function relocirajNalog(Request $request, $id){
		return $this->otpremiNalog($request, $id, true);
	}
	/**
	 * otprema naloga 
	 *
   	 * ovisno o trenutnom statusu (završen ili odustanak) postavi status (otpremljeno ili otpreljeno nepopravljeno)
   	 * osim kaj se postavi novi status, poslije i zapisivati u log...
	 */
	public function otpremiNalog(Request $request, $id, $relociraj = false){
		// setup
		$myErrors=array();

		// logic
		// -- nađi nalog
		// -- provjeri status - da li je jedan od ona dva ili onaj treći
		// -- prema njima postavi na zadnji
		// -- spremi nalog
		// -- u log piši tko i kada postavio koji status (otpremio)

		$nalog= \App\Models\Repairorder::find($id);
		if ($nalog === null) { // $nalog->exists()
			$myErrors[]="Nepostojeći nalog";
		} else {

			// nađi status
			$status = $nalog->lateststatus->first();
 			$status_id = $status->repairstatus_id;			
 			$odrediste = $status->relocationspp_id;

 			// logirani korisnik
 			$usr=Auth::user();
 			$korisnik=$usr->id;



 			if ($relociraj) {
 				// za servis
				if ($status_id == self::$status_needrelocation) {
					$nalog->repairstatuses()->attach(self::$status_movedfix, array('loggeduser_id'=>Auth::user()->id,'user_id'=>$korisnik,'locationspp_id'=>$usr->location, 'relocationspp_id'=>$odrediste));
				} else {
				// za isporuku	
					$nalog->repairstatuses()->attach(self::$status_moved, array('loggeduser_id'=>Auth::user()->id,'user_id'=>$korisnik,'locationspp_id'=>$usr->location, 'relocationspp_id'=>$odrediste));
				}

 			} else {
				// postavi novi - "otpremi"
				if (in_array($status_id, array(self::$status_over,self::$status_rejected))) {
					$nalog->repairstatuses()->attach(self::$status_shipped, array('loggeduser_id'=>Auth::user()->id,'user_id'=>$korisnik, 'locationspp_id'=>$usr->location));

					// zapiši tko i kada je otpremio
					$nalog->devicereturnperson_id = $korisnik;
					$nalog->devicereturndate = date("Y-m-d");		 			
					$nalog->save();
				
				}
			} 
			


			// u log...

		}



		// return
		if ($request->ajax()){

		        if (empty($myErrors)){
					$response = array(
				            'status' => 'success',
				            'msg' => 'Otpremljeno',
			        );
			    }else{
					$response = array(
				            'status' => 'error',
				            'msg' => 'Greška: '.implode(",",$myErrors),
			        );
			    }
		        return \Response::json($response);

		} else {




				return Redirect::to('slucaj')->withErrors($myErrors);
		}

	}






















	/*
	
	public function imeiSearch(){
	
		// ak je POST prazan renderiraj index....
	
		$search=true;		
		
		$searchFieldsQueries = Input::all();
		//var_dump($searchFieldsQueries);echo "<br>";
	
        $validation = Validator::make($searchFieldsQueries, 
			array(
			'search_imei' => 'digits:15',
			//'search_email' => 'email',
			
			));

        if ($validation->passes()) //if ($validation->fails())
        {		
		
			// ovo nekak izvuči van iz INPUT (po prefiksu "search_")
			$search_stsrn=$searchFieldsQueries['search_stsrepairorderno'];
			$search_imei=$searchFieldsQueries['search_imei'];			

			$search_slucaj= Repairorder::query();
			
			// provjeri i bildaj query
			if (trim($search_imei)!=="") 		$search_slucaj->where('deviceincomingimei', '=', $search_imei);
			if (trim($search_stsrn)!=="") 		$search_slucaj->where('stsrepairorderno', '=', $search_stsrn);
			
			//ima pagination / nema pagination
			$search_slucaj = (self::$paginationCount > 0) ?  $search_slucaj->paginate(self::$paginationCount) : $search_slucaj->get();
						
			// da mogu koristiti Form::old
			Input::flash();
			
			return View::make('slucaj.search', compact('search_slucaj'));
        }
		else 
		{
			return Redirect::to('slucaj')->withErrors($validation);
		}	
		
			
	}

*/	
	
	
	
	
	
	
	
	
	
	
	
	
	




	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{


		return $this->edit($id);
		/*
		// get slučaj
		$slucaj = Repairorder::with(
					array(	//'brand',
							'model',
							'pos',
							'repairorderslogs'=>function($q) {
											$q->with('repairstatus')->orderBy('updated_at', 'desc')->first();
										}
						  ))->find($id);
						  
		// get log
		$slucajlog = Repairorderslog::with('repairstatus')->where('repairorder_id','=',$id)->orderBy('updated_at','desc')->get();		

        // show the view and pass the nerd to it
        return View::make('slucaj.show')->with(array('slucaj'=>$slucaj,'slucajlog'=>$slucajlog));
		*/	
    }














	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy(Request $request, $id)
	{



		if (!($slucaj = \App\Models\Repairorder::withTrashed()->find($id))) {
			return $this->zabrani();
		}

		if (!(Bouncer::is(\Auth::user())->an('admin'))) {
			return $this->zabrani();
		}
		
		// delete slučaj
		$slucaj->delete();

        // redirect
		Session::flash('message', 'Radni nalog '.$slucaj->stsrepairorderno.' uspješno izbrisan');
		if(!is_null($request->input('dest')) && $request->input('dest')!=='') {
			return Redirect::to($request->input('dest'));
		} else {
			return Redirect::to('slucaj');
		}

	}








	/* PRINT - REPORTI */

	public function pdfView_radniNalog($id){
		return $this->printView_radniNalog($id, true);
	}

	public function printView_radniNalog($id, $pdf=false){

		//$slucaj = Repairorder::find($id);
		if (!($slucaj = \App\Models\Repairorder::withTrashed()->find($id))) {
			return $this->zabrani();
		}

		// ako nesmije vidjeti sve naloge, a nije njegov ili ostavljen kod njega, zabrani!
		if (Bouncer::denies('vidiSveNaloge') 
			 && 
			!in_array(\Auth::user()->location, array($slucaj['pickuppos_id'], $slucaj['pos_id']))
			) {
			return $this->zabrani();
		}

		$zaprimanje = in_array($slucaj->repairstatuses->first()->repairstatus_id, array(self::$status_pos, self::$status_inhouse, self::$status_open));
		$popravakgotov=in_array($slucaj->repairstatuses->first()->repairstatus_id, array(self::$status_over, self::$status_shipped));

		$pribor=array();
		if ($slucaj->deviceaccbattery) $pribor[]="Baterija";
		if ($slucaj->deviceacccharger) $pribor[]="Punjač";
		if ($slucaj->deviceaccantenna) $pribor[]="Antena";
		if ($slucaj->deviceaccsim) $pribor[]="SIM";
		if ($slucaj->deviceaccusbcable) $pribor[]="USB kabel";
		if ($slucaj->deviceaccmemorycard) $pribor[]="Memorijska kartica";
		if ($slucaj->deviceaccheadphones) $pribor[]="Slušalice";
		if (trim($slucaj->deviceaccrest)!=="") $pribor[]=$slucaj->deviceaccrest;
		$pribor=implode(", ", $pribor);

		$izradio=Auth::user()->first_name." ".substr(Auth::user()->last_name, 0,1);

		$dijeloviusluge = false;
		$ukupnacijena=0;

		// ako NIJE u jamstvu ispiši dijelove i usluge
		if($slucaj->devicewarranty !== 1) {
			$dijeloviusluge = array();
			$i=0;
			//svi dijelovi:
			foreach($slucaj->spareparts->all() as $part){
				$dijeloviusluge[$i]['tip']='dio';
				$dijeloviusluge[$i]['naziv']=$part->name;
				$dijeloviusluge[$i]['qty']=$part->pivot->qty;
				$dijeloviusluge[$i]['jm']='kom';
				$dijeloviusluge[$i]['prc']=$part->pivot->qty*$part->pivot->price;
				$ukupnacijena+=$dijeloviusluge[$i]['prc'];
				$i++;
			}
			//sve usluge:
			foreach($slucaj->stsservices->all() as $service){
				$dijeloviusluge[$i]['tip']='usluga';
				$dijeloviusluge[$i]['naziv']=$service->pivot->savedname;//->name;
				$dijeloviusluge[$i]['jm']=$service->pivot->savedjm;//->name;
				//$dijeloviusluge[$i]['naziv']=$service->name;
				$dijeloviusluge[$i]['qty']=$service->pivot->qty;
				$dijeloviusluge[$i]['prc']=$service->pivot->qty*$service->pivot->price;
				$ukupnacijena+=$dijeloviusluge[$i]['prc'];
				$i++;
			}
		}

			$datumzavrsetka= null;

			if (!is_null($slucaj->stsroclosingdate)) {
				try {

					$datumzavrsetka	= \Carbon\Carbon::parse($slucaj->stsroclosingdate);//->format('Y-m-d H:i:s');
				} catch (\Exception $e){
					//dd($e);
				}
			}


			/*
			vidjeti gdje je kupljeno. ako postoji deviceotherbuyplace onda daj njega, ak ne onda adresa POSa
			 */
			if (trim($slucaj->deviceotherbuyplace) == "" || is_null($slucaj->deviceotherbuyplace)) {
				$buyplace = $slucaj->pos->posname;
			} else {				
				$buyplace = $slucaj->deviceotherbuyplace;
			}


			$viewdata=array(
					'slucaj'=>$slucaj,
					'zaprimanje'=>$zaprimanje,
					'pribor'=>$pribor,
					'buyplace'=>$buyplace,
					'izradio'=>$izradio,
					'popravakgotov'=>$popravakgotov,
					'dijeloviusluge'=>$dijeloviusluge,
					'ukupnacijena'=>$ukupnacijena,
					'datumzavrsetka'=>$datumzavrsetka,
				);

			//http://www.tcpdf.org/examples.php
			//https://github.com/milon/barcode

		if ($pdf) {
			$pdf = PDF::loadView('pdfreporti.radninalog_pdf', $viewdata);
			return $pdf->download('STSRN_'.$slucaj->stsrepairorderno.'.pdf'); //Slugify::slugify($mbr->name)
		} else {
			return View::make('pdfreporti.radninalog')->with($viewdata); 
		}


	}



	public function pdfView_prijemniList($id){
		return $this->printView_prijemniList($id, true);
	}

	public function printView_prijemniList($id, $pdf=false){


		//$slucaj = Repairorder::find($id);
		if (!($slucaj = \App\Models\Repairorder::withTrashed()->find($id))) {
			return $this->zabrani();
		}

		// ako nesmije vidjeti sve naloge, a nije njegov ili ostavljen kod njega, zabrani!
		if (Bouncer::denies('vidiSveNaloge') 
			 && 
			!in_array(\Auth::user()->location, array($slucaj['pickuppos_id'], $slucaj['pos_id']))
			) {
			return $this->zabrani();
		}


		$zaprimanje = in_array($slucaj->repairstatuses->first()->repairstatus_id, array(self::$status_pos, self::$status_inhouse, self::$status_open));
		$popravakgotov=in_array($slucaj->repairstatuses->first()->repairstatus_id, array(self::$status_over, self::$status_shipped));

		$pribor=array();
		if ($slucaj->deviceaccbattery) $pribor[]="Baterija";
		if ($slucaj->deviceacccharger) $pribor[]="Punjač";
		if ($slucaj->deviceaccantenna) $pribor[]="Antena";
		if ($slucaj->deviceaccsim) $pribor[]="SIM";
		if ($slucaj->deviceaccusbcable) $pribor[]="USB kabel";
		if ($slucaj->deviceaccmemorycard) $pribor[]="Memorijska kartica";
		if ($slucaj->deviceaccheadphones) $pribor[]="Slušalice";
		if (trim($slucaj->deviceaccrest)!=="") $pribor[]=$slucaj->deviceaccrest;
		$pribor=implode(", ", $pribor);

		$izradio=Auth::user()->first_name." ".substr(Auth::user()->last_name, 0,1);

		$dijeloviusluge = false;
		$ukupnacijena=0;

		// ako NIJE u jamstvu ispiši dijelove i usluge
		if($slucaj->devicewarranty !== 1) {
			$dijeloviusluge = array();
			$i=0;
			//svi dijelovi:
			foreach($slucaj->spareparts->all() as $part){
				$dijeloviusluge[$i]['tip']='dio';
				$dijeloviusluge[$i]['naziv']=$part->name;
				$dijeloviusluge[$i]['qty']=$part->pivot->qty;
				$dijeloviusluge[$i]['jm']='kom';
				$dijeloviusluge[$i]['prc']=$part->pivot->qty*$part->pivot->price;
				$ukupnacijena+=$dijeloviusluge[$i]['prc'];
				$i++;
			}
			//sve usluge:
			foreach($slucaj->stsservices->all() as $service){
				$dijeloviusluge[$i]['tip']='usluga';
				$dijeloviusluge[$i]['naziv']=$service->pivot->savedname;//->name;
				$dijeloviusluge[$i]['jm']=$service->pivot->savedjm;//->name;
				//$dijeloviusluge[$i]['naziv']=$service->name;
				$dijeloviusluge[$i]['qty']=$service->pivot->qty;
				$dijeloviusluge[$i]['prc']=$service->pivot->qty*$service->pivot->price;
				$ukupnacijena+=$dijeloviusluge[$i]['prc'];
				$i++;
			}
		}

			$datumzavrsetka= null;

			if (!is_null($slucaj->stsroclosingdate)) {
				try {

					$datumzavrsetka	= \Carbon\Carbon::parse($slucaj->stsroclosingdate);//->format('Y-m-d H:i:s');
				} catch (\Exception $e){
					//dd($e);
				}
			}


			/*
			vidjeti gdje je kupljeno. ako postoji deviceotherbuyplace onda daj njega, ak ne onda adresa POSa
			 */
			if (trim($slucaj->deviceotherbuyplace) == "" || is_null($slucaj->deviceotherbuyplace)) {
				$buyplace = $slucaj->pos->posname;
			} else {				
				$buyplace = $slucaj->deviceotherbuyplace;
			}

			$pickuppos_id = $slucaj->pickuppos_id;
			$pickupposname = \App\Models\Pos::where('id','=',$pickuppos_id)->first()->posname;

			$pickupposname=$pickupposname; // provjeriti jel spp ili servis

			$viewdata=array(
					'slucaj'=>$slucaj,
					'zaprimanje'=>$zaprimanje,
					'pribor'=>$pribor,
					'izradio'=>$izradio,
					'popravakgotov'=>$popravakgotov,
					'dijeloviusluge'=>$dijeloviusluge,
					'ukupnacijena'=>$ukupnacijena,
					'datumzavrsetka'=>$datumzavrsetka,
					'buyplace' => $buyplace,
					'pickupposname'=>$pickupposname,
				);

			//http://www.tcpdf.org/examples.php
			//https://github.com/milon/barcode

		if ($pdf) {
			$pdf = PDF::loadView('pdfreporti.prijemnilist_pdf', $viewdata);
			return $pdf->download('STSPL_'.$slucaj->stsrepairorderno.'.pdf'); //Slugify::slugify($mbr->name)
		} else {
			return View::make('pdfreporti.prijemnilist')->with($viewdata); 
		}


	}

 public function showTele2Naloge(){


		$adminUser = Bouncer::is(\Auth::user())->an('admin');
		$t2nalozi=ws_tele2soap::whereNull('rejected')
						->where(function($q){
								$q->where('sts_repairorder_number','<','1')
								->orWhereNull('sts_repairorder_number');
						})->OrderBy('created_at','desc')->get();

		return View::make('slucaj.t2nalozi')->with(['t2nalozi'=>$t2nalozi, 'adminUser'=>$adminUser]);		

	 }


 public function showSPPNaloge(){

 		// nalozi zaprimljeni na POS-evima (samo status=self::status_pos)
 		// znači da je zadnji status = self::status_pos

//jel Grammar::parameterize() u helpers?
//DB::enableQueryLog();

 		$zadnji=	\DB::table('repairorder_repairstatus as t1')
 				->select('t1.repairorder_id')
 				->leftJoin('repairorder_repairstatus as t2', function ($q) {
 					$q->on('t1.repairorder_id', '=', 't2.repairorder_id' );
 					$q->on('t1.created_at','<', 't2.created_at');
 					})
 				->whereNull('t2.created_at')
 				//->whereIn('t1.repairstatus_id',[])
 				->where('t1.repairstatus_id',self::$status_pos)
 				->lists('repairorder_id');

 //dd(print_r(DB::getQueryLog()[0]['query']));



		$sppnalozi=\App\Models\Repairorder::whereIn('id',$zadnji)->OrderBy('created_at','desc')->get();
		return View::make('slucaj.sppnalozi')->with(['sppnalozi'=>$sppnalozi, 'adminUser'=>Bouncer::is(\Auth::user())->an('admin')]);		

	 }



















	public function catchupT2(\App\Http\Controllers\SoapClientController $sc){

		// idem naći za koje sve ro treba poslati status
		// za te ro nađem zadnji status
		// pošaljem im i zapišem response

		$target_nalozi=array(5373,5381,5440,5368,5518,5390,5463,5351,5378,5363,5716,5521,5500,5464,5448,5384,5589,5540,5673,5396,5489,5507,5466,5376,5383,5517,5375,5387,5674,5371,5348,5622,5623,5457,5483,5515,5388,5389,5350,5513,5620,5600,5584,5382,5392,5625,5394,5677,5379,5364,5759,5539,5386,5504);

		/*
		  	SELECT 		zs.*, ro.posrepairorderno, ro.stsnotice, ro.deviceincomingimei,
		  				ro.stsmbswap, ro.stsdoadap, ro.posclaimtype_id, ro.stsdeviceswap
			FROM ZS
			LEFT JOIN repairorders ro on zs.repairorder_id = ro.id

			WHERE zs.repairorder_id IN (5373,5381,5440,5368,5518,5390,5463,5351,5378,5363,5716,5521,5500,
										5464,5448,5384,5589,5540,5673,5396,5489,5507,5466,5376,5383,5517,
										5375,5387,5674,5371,5348,5622,5623,5457,5483,5515,5388,5389,5350,
										5513,5620,5600,5584,5382,5392,5625,5394,5677,5379,5364,5759,5539,5386,5504)
		 */

		$nalozi=DB::table('zs')
					->addSelect('zs.id as statuspivot_id','zs.repairstatus_id','co.code', 'ro.posrepairorderno', 'ro.stsnotice', 'ro.deviceincomingimei',
		  				'ro.stsmbswap', 'ro.stsdoadap', 'ro.posclaimtype_id', 'ro.stsdeviceswap')
					->leftJoin('repairorders as ro','zs.repairorder_id','=','ro.id')
					->leftJoin('ws_tele2soap_statuses as co','co.sts_repairstatus_id','=','zs.repairstatus_id')
					->whereIn('zs.repairorder_id',$target_nalozi)
					;


		foreach ($nalozi->get() as $nalog) {

			// pripremi
			$caseId=$nalog->posrepairorderno;
			$statusId=$nalog->repairstatus_id;
			$statusPivotId=$nalog->statuspivot_id;
			$comment=$nalog->stsnotice;
			$remark="";
			$imei=$nalog->deviceincomingimei;
			$reason="";
			$mbswap=($nalog->stsmbswap == 1) ? 1 : 0;
			$doadap=($nalog->stsdoadap == 1) ? 1 : 0;
			$claimtype=$nalog->posclaimtype_id;
			$deviceswap=($nalog->stsdeviceswap == 1) ? 1 : 0;
			$servicerejected= (in_array($statusId, array(self::$status_rejected))) ? 1 : 0;

			// pošalji
			$this->updateSoapStatus($sc, $caseId, $statusId, $statusPivotId, $comment, $remark, $imei, $reason, $mbswap, $doadap, $claimtype, $deviceswap, $servicerejected);

			// pričekaj?
			usleep(100000);
		}




	}




}
