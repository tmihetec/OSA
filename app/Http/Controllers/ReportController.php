<?php

namespace App\Http\Controllers;

//use App\Models\User;
use Principal, Stsservicelocation, Repairstatus, Repairorder;
use Illuminate\Http\Request;

use Brand, Model;
use Yajra\Datatables\Datatables;

use Bouncer, URL;


class ReportController extends Controller {

	/*
	dnevni report - GET - prikaz
	 */


	public function showOne($postedData=null,$nalozi=null){

		$dateFrom=date("d.m.Y");
		$dateTo=date("d.m.Y");
		$statusi=\App\Models\Repairstatus::lists('name','id')->all();
		$lokacije = \App\Models\Pos::where('principal_id','=',RepairorderController::getStsPrincipalId())->lists('posname','id')->all();//Stsservicelocation::lists('name','id')->all();
		// MOŽDA IZVADITI SAMO ONE KOJI SU PRIMILI BAR JEDAN... (where id in (select pickuppos_id from repairorders))
		$spp = \App\Models\Pos::lists('posname','id')->all();
		$serviseri = \App\Models\User::withTrashed()->where('id','>','1')->lists('user','id')->all();
		$komitenti = \App\Models\Principal::lists('naziv','id')->all();
		$brandovi = \App\Models\Brand::lists('name','id')->all();
		$modeli = \App\Models\Model::get()->lists('brand_model','id')->all();
		$tipovi = \App\Models\Devicetype::get()->lists('naziv','id')->all();


		if(is_null($postedData)){
			$app = app();
    		$postedData = $app->make('stdClass');
			$postedData->dateFrom=date("d.m.Y");
			$postedData->dateTo=date("d.m.Y");
			$postedData->statusi=null;
			$postedData->imastatuse=null;
			$postedData->imanekestatuse=null;
			$postedData->lokacije=null;
			$postedData->serviseri=null;
			$postedData->komitenti=null;
			$postedData->brandovi=null;
			$postedData->modeli=null;
			$postedData->tipovi=null;
			$postedData->spp=null;
		}

		$sendData=array(
				'dateFrom' 	=> $dateFrom,
				'dateTo' 	=> $dateTo,
				'statusi'	=> $statusi,
				'lokacije'	=> $lokacije,
				'serviseri'	=> $serviseri,
				'komitenti'	=> $komitenti,
				'brandovi'	=> $brandovi,
				'modeli'	=> $modeli,
				'tipovi'	=> $tipovi,
				'postedData'=> $postedData,
				'nalozi'	=> $nalozi,
				'spp'		=> $spp
			);
		return view('reporti.one')->with($sendData);
	}

	/*
	kompletni report nakon POST
	 */
	public function doOne(Request $request){

		$nalozi=$this->rptOneQuery($request);
		// filtere u objekt i onda ga poslati
		// $nalozi=$this->rptOneFilter($request);

		$nalozi=$nalozi->get();

		// složi postData objekt



    	//$nalozi=null;
    	$dateFrom=null;
    	$dateFromObj=null;
    	$dateTo=null;
    	$dateToObj=null;
       	// procesiraj ulazne
    	try{
    		if(!empty($request->input('dateFrom'))) {
    			$dateFromObj 	=\Carbon\Carbon::parse($request->input('dateFrom'));
				$dateFrom = $dateFromObj->format("Y-m-d")." 00:00:00"; // ponoć tog dana ">="
			}
    		if(!empty($request->input('dateTo'))) {
	    		$dateToObj 	=\Carbon\Carbon::parse($request->input('dateTo'));
				$dateTo = $dateToObj->addDay()->format("Y-m-d")." 00:00:00"; // + 1 dan, pa u ponoć "<"
			}
			$statusi = $request->input('status'); // ili null
			$imastatuse = $request->input('imastatuse'); // ili null
			$imanekestatuse = $request->input('imanekestatuse'); // ili null
			$lokacije = $request->input('serviceLocation'); // ili null
			$komitenti = $request->input('komitenti'); // ili null
			$serviseri = $request->input('servicePerson'); // ili null
			$brandovi = $request->input('brand'); // ili null
			$modeli = $request->input('model'); // ili null
			$tipovi = $request->input('tip'); // ili null
			$spp = $request->input('spp'); // ili null
		} catch (\Exception $e) {
			dd('RptCtrl_doOne.'.$e);
		}





		//$postedData = new \stdClass();
		$app = app();
    	$postedData = $app->make('stdClass');

		$postedData->dateFrom=(is_null($dateFromObj)) ? null : $dateFromObj->format("d.m.Y");
		$postedData->dateTo=(is_null($dateToObj)) ? null : $dateToObj->subDay()->format("d.m.Y");
		$postedData->statusi=$statusi;
		$postedData->imastatuse=$imastatuse;
		$postedData->imanekestatuse=$imanekestatuse;
		$postedData->lokacije=$lokacije;
		$postedData->serviseri=$serviseri;
		$postedData->komitenti=$komitenti;
		$postedData->brandovi=$brandovi;
		$postedData->modeli=$modeli;
		$postedData->tipovi=$tipovi;
		$postedData->spp=$spp;

		
		// temp---
		ini_set('memory_limit','256M');

    	// pošalji ih u view
		return $this->showOne($postedData, $nalozi);
	}



	/*
	pripremi query za reportOne
	 */
	public function rptOneQuery(Request $request){


    	$nalozi=null;
    	$dateFrom=null;
    	$dateFromObj=null;
    	$dateTo=null;
    	$dateToObj=null;
    	
    	// procesiraj ulazne
    	try{
    		if(!empty($request->input('dateFrom'))) {
    			$dateFromObj 	=\Carbon\Carbon::parse($request->input('dateFrom'));
				$dateFrom = $dateFromObj->format("Y-m-d")." 00:00:00"; // ponoć tog dana ">="
			}
    		if(!empty($request->input('dateTo'))) {
	    		$dateToObj 	=\Carbon\Carbon::parse($request->input('dateTo'));
				$dateTo = $dateToObj->addDay()->format("Y-m-d")." 00:00:00"; // + 1 dan, pa u ponoć "<"
			}
			$statusi = $request->input('status'); // ili null
			$imastatuse = $request->input('imastatuse'); // ili null
			$imanekestatuse = $request->input('imanekestatuse'); // ili null
			$lokacije = $request->input('serviceLocation'); // ili null
			$komitenti = $request->input('komitenti'); // ili null
			$serviseri = $request->input('servicePerson'); // ili null
			$brandovi = $request->input('brand'); // ili null
			$modeli = $request->input('model'); // ili null
			$tipovi = $request->input('tip'); // ili null
			$spp = $request->input('spp'); // ili null


		} catch (\Exception $e) {
			dd('RptCtrl_doOne.'.$e);
		}


		//\DB::enableQueryLog();
		//print_r(\DB::getQueryLog()[0]['query']);



		$nalozi=\DB::table('repairorders')->distinct()
						->select(
							'repairorders.id',
							'repairorders.deviceincomingimei',
							'repairorders.stsrepairorderno',
							'repairorders.devicemodel_id',
							'devicetypes.naziv as tip',
							'repairorders.stsserviceperson_id',
							'repairorders.stsservicelocation_id',
							\DB::raw('CONCAT(brands.name," ",models.name) as uredjaj'),
							'servisi.posname as servisname',
							'poses.posname',
							'repairstatuses.name as zadnjistatus',
							'zs.datumstatusa', 
							'zs.repairstatus_id',
							\DB::raw('CONCAT(users.first_name," ",users.last_name) as serviser'),
							'users.deleted_at as zbrisankorisnik',
							'principals.naziv as komitent',
							'devicereturntypes.name as otprema',
							\DB::raw('
								CASE
									WHEN devicereturntype_id = "4" THEN repairorders.devicereturnother 
									WHEN devicereturntype_id = "3" THEN "Osobno preuzimanje"
									WHEN devicereturntype_id = "2" THEN repairorders.customerstreet
									ELSE poses.posadresa 
								END AS adresaotpreme1
								'),
							\DB::raw('
								CASE
									WHEN devicereturntype_id = "4" THEN NULL
									WHEN devicereturntype_id = "3" THEN NULL
									WHEN devicereturntype_id = "2" THEN CONCAT(gradkorisnika.postalcode," ",gradkorisnika.name )
									ELSE CONCAT(gradsppa.postalcode," ",gradsppa.name)
								END AS adresaotpreme2
								')
							)
						;

		// join tables
		$nalozi=$nalozi->leftJoin('poses','repairorders.pos_id','=','poses.id')
					->leftJoin('principals', 'principals.id','=','poses.principal_id')
					->leftJoin('poses as servisi','repairorders.stsservicelocation_id','=','servisi.id')
					->leftJoin('models','repairorders.devicemodel_id','=','models.id')
					->leftJoin('devicetypes','models.devicetype_id','=','devicetypes.id')
					->leftJoin('users','users.id','=','repairorders.stsserviceperson_id')
					->leftJoin('brands','models.brand_id','=','brands.id')
					 ->leftJoin('zs','zs.repairorder_id','=','repairorders.id')
					 ->leftJoin('repairstatuses', 'zs.repairstatus_id','=','repairstatuses.id')
					 ->leftJoin('repairorder_repairstatus','repairorders.id','=','repairorder_repairstatus.repairorder_id')
					 ->leftJoin('poses as spp', 'repairorders.pickuppos_id','=','spp.id')
					 ->leftJoin('devicereturntypes', 'devicereturntypes.id','=','repairorders.devicereturntype_id')
					 ->leftJoin('locplaces as gradkorisnika','repairorders.customerplace_id','=','gradkorisnika.id')
					 ->leftJoin('locplaces as gradsppa','poses.posplace_id','=','gradsppa.id')
					 ->leftjoin('repairorder_repairstatus as nekistatusi','nekistatusi.repairorder_id','=','repairorders.id')
					;

		/*
		public function cacheQuery($sql, $timeout = 60) {
		    return Cache::remember(md5($sql), $timeout, function() use ($sql) {
		        return DB::raw($sql);
		    });
		}

		$results = $this->cacheQuery("SELECT * FROM stuff INNER JOIN more_stuff");
		 */




		// FILTERI! ------------------------------------

		// where
		if (!is_null($dateFrom)) { $nalozi=$nalozi->where('zs.datumstatusa','>=',$dateFrom); }
		if (!is_null($dateTo)) { $nalozi=$nalozi->where('zs.datumstatusa','<',$dateTo); }
		if (!is_null($lokacije)) { $nalozi	= $nalozi->whereIn('repairorders.stsservicelocation_id',$lokacije); }
		if (!is_null($serviseri)) { $nalozi	= $nalozi->whereIn('repairorders.stsserviceperson_id',$serviseri); }
		if (!is_null($modeli)) {  $nalozi = $nalozi->whereIn('repairorders.devicemodel_id',$modeli); }
		if (!is_null($brandovi)) { $nalozi->whereIn('brands.id',$brandovi);	}
		if (!is_null($tipovi)) {  $nalozi = $nalozi->whereIn('models.devicetype_id',$tipovi); }
		if (!is_null($spp)) { $nalozi=$nalozi->whereIn('repairorders.pickuppos_id',$spp);	}
		if (!is_null($komitenti)) { $nalozi=$nalozi->whereIn('poses.principal_id',$komitenti);	}
		// -- gdje je zadnji status neki od...
		if (!is_null($statusi)) { $nalozi=$nalozi->whereIn('zs.repairstatus_id', $statusi);}
		// -- gdje je bilokoji od statusa...
		if (!is_null($imanekestatuse)) { $nalozi=$nalozi->whereIn('nekistatusi.repairstatus_id', $imanekestatuse);}
		// -- gdje ima SVE NAVEDENE statuse, ALI TREBA IMATI SVE TE STATUSE, NE BILO KOJI OD NJIH!!
		if (!is_null($imastatuse)) {
			// naći sve naloge koji imaju sve ove statuse...
			//\DB::enableQueryLog();
			$ids=\DB::table('repairorder_repairstatus')->select('repairorder_id')
					->whereIn('repairstatus_id',$imastatuse)
					->groupBy('repairorder_id')
					->havingRaw('count(distinct repairstatus_id) ='.count($imastatuse))
					->lists('repairorder_id');
			//dd(\DB::getQueryLog()[0]);
			$nalozi=$nalozi->whereIn('repairorders.id',$ids);
		}					 	

		return $nalozi;
	}







	/*
		Report One poziva ovu metodu da mu dostavi JSON podatke za 
		tablicu. (Yajra Datatables plugin)
	 */
	public function dohvatiNalogeZaOne(Request $request){


			$nalozi=$this->rptOneQuery($request);


//			dd($nalozi);

			// sad imam naloge,
			// idem dodati alate i editirati kolone
		     $data = Datatables::of($nalozi)
		    		->editColumn('datumstatusa', function ($ro) {
		                return date("d.m.Y", strtotime($ro->datumstatusa));
		            })
		    		->editColumn('zbrisankorisnik', function ($ro) {
		                $r="<span";
		                $r.= (!is_null($ro->zbrisankorisnik)) ? "style='color:#900; text-decoration:line-through;'" :"";
            			$r.= ">".$ro->serviser."</span>";
            			return $r; 
            		})
		    		->addColumn('adresaotpreme', function ($ro) {
		                return implode("<br>",array_filter(array($ro->adresaotpreme1,$ro->adresaotpreme2)));
		            })
			       	->addColumn('alati', function ($ro){
			       			$tools= '<div class="btn-group btn-group-xs" style="display:flex;" role="group">';
			                $tools.=    '<a class="btn btn-small btn-warning" target="_blank" title="edit" href="'.URL::to("slucaj/".$ro->id).'/edit"><i class="glyphicon glyphicon-pencil"></i></a>';
			                $tools.=    '<a class="btn btn-small btn-info" target="_blank" title="Print view" href="'.URL::to("printView/rn/".$ro->id).'"><i class="glyphicon glyphicon-eye-open"></i></a>';
			                /*if (\Entrust::hasRole('admin')) {
			                    $tools.= '<a class="btn btn-small btn-danger" data-placement="left" title="Delete?" data-delete="'.csrf_token().'" data-myhref="'.URL::to('slucaj/'.$ro->id).'"><i class="glyphicon glyphicon-trash"></i></a>';
			                }*/
			                $tools.= '</div>';
			                return $tools;
				       	})
		            ;

			/*
				ako će biti SERVER PROCESSING...
				treba i ove "custom" kolone podesiti

			// Global search function
		    if ($keyword = $request->get('search')['value']) {
		        // // override users.name global search
		        // $data->filterColumn('users.name', 'where', 'like', "$keyword%");
		        // override users.id global search - demo for concat
		        $data->filterColumn('uredjaj', 'whereRaw', "CONCAT(brands.name,' ',models.name) like ? ", ["%$keyword%"]);
		        $data->filterColumn('korisnik', 'whereRaw', "CONCAT(repairorders.customername,' ',repairorders.customerlastname) like ? ", ["%$keyword%"]);
		        $data->filterColumn('zadnjistatus', 'where', "repairstatuses.name like ? ", ["%$keyword%"]);
		    }
			 */
			
		    return $data->make(true);

		}

/*
<th>ADRESA OTPREME</th>{!!  !!}
 */












	/*
	otprema report - GET - prikaz
	 */
	public function showOtprema($postedData=null){


		$servicepersonlocation = \Auth::user()->location;
		$lokacije = \App\Models\Pos::where('principal_id','=',RepairorderController::getStsPrincipalId())->lists('posname','id')->all();//Stsservicelocation::lists('name','id')->all();
		$retlokacije = $lokacije;
		$serviseri = \App\Models\User::withTrashed()->where('id','>','1')->get()->lists('ime_prezime','id');



		if(is_null($postedData)){
			// prvo otvaranje - bez filtera lokacije
			$app = app();
    		$postedData = $app->make('stdClass');
			$postedData->lokacije=$servicepersonlocation;
			//$postedData->lokacije=array($servicepersonlocation);
			$postedData->retlokacije=$servicepersonlocation;
			//$postedData->retlokacije=array($servicepersonlocation);
			$postedData->datumzavrsetka=null;
			if (Bouncer::is(\Auth::user())->an('servis')) {
				$postedData->serviseri=\Auth::user()->id;
			} else {
				$postedData->serviseri=0;
			}
		} 


		// nađi sve naloge koji imaju zadnji status "servis završen" ili "odustanak"
		$statusipredotpremu=RepairorderController::getStatusipredotpremu();
		// dodaj i "prebaciti u drugi servis na doradu"
		array_push($statusipredotpremu, RepairorderController::getStatusiNeedRelocation());


		// zadnji statusi
		/*
		
		$sql='
				SELECT t1.*
				FROM repairorder_repairstatus t1 
				LEFT JOIN repairorder_repairstatus t2
					ON (t1.repairorder_id = t2.repairorder_id 
						AND t1.created_at < t2.created_at
						)
				WHERE t2.created_at IS NULL 
			';
		*/
		
		$sql=' SELECT * FROM zs WHERE 1=1 ';

		if (!is_null($statusipredotpremu)) { 
			$sql.='	AND	repairstatus_id IN ('.implode(',', $statusipredotpremu).')' ;
		}
		if ($postedData->serviseri >0) { 
			$sql.='	AND	user_id = '.$postedData->serviseri;
		}
		if (!is_null($postedData->datumzavrsetka)) { 
				try {
    				$date 	=\Carbon\Carbon::parse($postedData->datumzavrsetka);
					$dateOd = $date->format("Y-m-d")." 00:00:00"; // ponoć tog dana ">="
					$dateDo = $date->addDay()->format("Y-m-d")." 00:00:00"; // + 1 dan, pa u ponoć "<"
					$sql.='	AND	datumstatusa >= "'.$dateOd.'"';
					$sql.='	AND	datumstatusa < "'.$dateDo.'"';
				}catch(\Exception $e){
				   Log::error("showOtprema, ".$e->getMessage());
				}
		}


		//dd($sql);
		$nalozi = \DB::select($sql);

		$ids=array();
		foreach($nalozi as $id) {
			$ids[]=$id->repairorder_id;
		}


		// izvadi one koji imaju samo tu/te lokaciju/e
		$naloziZaOtpremu = \App\Models\Repairorder::with('repairstatuses','pos','model')->whereIn('id',$ids);

		//if (!is_null($postedData->lokacije)) { $naloziZaOtpremu	= $naloziZaOtpremu->whereIn('stsservicelocation_id',$postedData->lokacije); }
		if ($postedData->lokacije>0) { $naloziZaOtpremu	= $naloziZaOtpremu->where('stsservicelocation_id','=',$postedData->lokacije); }

		$naloziZaOtpremu=$naloziZaOtpremu->get();


		if ($naloziZaOtpremu->isEmpty()) $naloziZaOtpremu=null;
		

		$sendData=array(
				'lokacije'	=> $lokacije,
				'serviseri' => $serviseri,
				'retlokacije'	=> $retlokacije,
				'postedData'=> $postedData,
				'naloziZaOtpremu'	=> $naloziZaOtpremu,
				'otpremljeniNalozi'	=> null,
				'activeOtprema' => 'active',
				'activeOtpremljeno' => '',
				'statusPrebacivanja' => RepairorderController::getStatusiNeedRelocation()
			);



// ako je nalog imao status "relokacija za servis", treba uzeti zadnji sa kojeg je stigao i ponuditi da se vrati tamo

		return view('reporti.otprema')->with($sendData);
	}

	/*
	otprema report - POST - prikaz
	 */
	public function doOtprema(Request $request){

		$retlokacije = null; // ili null
		$lokacije = $request->input('serviceLocation'); // ili null
		$serviseri = $request->input('serviseri');


		// složi postData objekt
		//$postedData = new \stdClass();
		$app = app();
    	$postedData = $app->make('stdClass');
		$postedData->lokacije=$lokacije;
		$postedData->retlokacije=$retlokacije;
		$postedData->serviseri=$serviseri;
		$postedData->datumzavrsetka=($request->input('datumzavrsetka')!=="") ? $request->input('datumzavrsetka') : null;

    	// pošalji ih u view
		return $this->showOtprema($postedData);
	}



































	public function showRealizacijaDetaljno(Request $request, $postedData=null){

		$serviseri = \App\Models\User::all()->lists('custom','id');

		if(is_null($postedData) || (!is_null($postedData) && is_null($postedData->serviser_id))){

					// prvo otvaranje - sve na default
					$app = app();
		    		$postedData = $app->make('stdClass');

					$datumOdObj 	=\Carbon\Carbon::now()->startOfMonth();
					$postedData->datumOdObj=$datumOdObj;
					$postedData->datumOd = $datumOdObj->format("Y-m-d");//." 00:00:00"; // ponoć tog dana ">="

					$datumDoObj 	=\Carbon\Carbon::now();
					$postedData->datumDoObj=$datumDoObj;
					$postedData->datumDo = $datumDoObj->format("Y-m-d");//." 00:00:00"; // ponoć tog dana ">="

					$postedData->serviser_id = null;
					$nalozi = null;

		} else {

					// izvadi zatvorene naloge za servisera
					$nalozi=\DB::table('repairorders')
						->select(
							'repairorders.id',
							'repairorders.stsrepairorderno as nalog',
							'repairorders.stsroclosingdate',
							'repairorders.stsserviceperson_id',
							'poses.posname',
				
							\DB::raw('if(repairorders.stsdeviceswap = "1", 1, 0) as swap'),
							\DB::raw('if(repairorders.stsmbswap = "1", 1, 0) as mbswap'),
							\DB::raw('if(repairorders.stsfailuredetected = "1", 1, 0) as imakvar'),
							\DB::raw('if(repairorders.devicewarranty = "1", 1, 0) as jamstvo'),

							\DB::raw('naplativiiznosi.ukupno as iznos')

 
							);

					$nalozi=$nalozi->leftJoin('poses','repairorders.stsservicelocation_id','=','poses.id')
										->leftJoin('naplativiiznosi','repairorders.id','=','naplativiiznosi.nalog')
										;

					$nalozi=$nalozi->whereNotNull('repairorders.stsroclosingdate')
									->where('repairorders.stsserviceperson_id','=',$postedData->serviser_id);

					if (!is_null($postedData->datumOd)) { $nalozi=$nalozi->where('repairorders.stsroclosingdate','>=',$postedData->datumOd); }
					if (!is_null($postedData->datumDo)) { $nalozi=$nalozi->where('repairorders.stsroclosingdate','<=',$postedData->datumDo); }


					$nalozi=$nalozi->get();
		}

		$sendData=array(
				'serviseri' => $serviseri,
				'postedData'=> $postedData,
				'nalozi'	=> $nalozi
			);

		return view('reporti.realizacijadetaljno')->with($sendData);


	}


	public function makeRealizacijaDetaljno(Request $request, $user=null, $datumOd=null, $datumDo=null){


    	// procesiraj ulazne
    	try{
    		if(!empty($datumOd)) {
    			if($datumOd == "-") {
    				$datumOdObj=null; 
    				$datumOd=null;
    			} else {
    				$datumOdObj 	=\Carbon\Carbon::parse($datumOd);
					$datumOd = $datumOdObj->format("Y-m-d");//." 00:00:00"; // ponoć tog dana ">="
				}
			}
    		if(!empty($datumDo)) {
    			if($datumDo == "-") {
    				$datumDoObj=null; 
    				$datumDo=null;
    			} else {
		    		$datumDoObj 	=\Carbon\Carbon::parse($datumDo);
					$datumDo = $datumDoObj->format("Y-m-d");//." 00:00:00"; // + 1 dan, pa u ponoć "<"
				}
			}
    		if(!empty($user)) {
    			$serviser_id=$user;
    		}else{
    			$serviser_id=null;
    		}

		} catch (\Exception $e) {
			dd('RptCtrl_doRealizacijaDetaljnoMake.'.$e);
		}


		// složi postData objekt
		//$postedData = new \stdClass();
		$app = app();
    	$postedData = $app->make('stdClass');
		$postedData->datumOd = $datumOd;
		$postedData->datumDo = $datumDo;
		$postedData->datumOdObj=$datumOdObj;
		$postedData->datumDoObj=$datumDoObj;
		$postedData->serviser_id=$serviser_id;


    	// pošalji ih u view
		return $this->showRealizacijaDetaljno($request, $postedData);

	}


	public function doRealizacijaDetaljno(Request $request){


		$datumOd=null;
    	$datumOdObj=null;
    	$datumDo=null;
    	$datumDoObj=null;
    	
    	// procesiraj ulazne
    	try{
    		if(!empty($request->input('datumOd'))) {
    			$datumOdObj 	=\Carbon\Carbon::parse($request->input('datumOd'));
				$datumOd = $datumOdObj->format("Y-m-d");//." 00:00:00"; // ponoć tog dana ">="
			}
    		if(!empty($request->input('datumDo'))) {
	    		$datumDoObj 	=\Carbon\Carbon::parse($request->input('datumDo'));
				$datumDo = $datumDoObj->format("Y-m-d");//." 00:00:00"; // + 1 dan, pa u ponoć "<"
			}
    		if(!empty($request->input('serviser_id'))) {
    			$serviser_id=$request->input('serviser_id');
    		}else{
    			$serviser_id=null;
    		}

		} catch (\Exception $e) {
			dd('RptCtrl_doRealizacijaDetaljno.'.$e);
		}


		// složi postData objekt
		//$postedData = new \stdClass();
		$app = app();
    	$postedData = $app->make('stdClass');
		$postedData->datumOd = $datumOd;
		$postedData->datumDo = $datumDo;
		$postedData->datumOdObj=$datumOdObj;
		$postedData->datumDoObj=$datumDoObj;
		$postedData->serviser_id=$serviser_id;

/*		if ($serviser_id==null) {
			$postedData->lokacije=null;
			//return redirect()->action('ReportController@showRealizacija')->with(['request'=>$request,'postedData'=>$postedData]);
			//return redirect()->action($this->showRealizacija($request, $postedData));
			//return redirect()->action('ReportController@showRealizacija', ['request'=>$request,'postedData'=>$postedData]);
			//return redirect('reportRealizacija')->with(['request'=>$request,'postedData'=>$postedData]);
		}
*/
    	// pošalji ih u view
		return $this->showRealizacijaDetaljno($request, $postedData);

	}































	/*
	realizacija report - GET - prikaz
	 */
	public function showRealizacija(Request $request, $postedData=null){

		$servicepersonlocation = \Auth::user()->location;
		$lokacije = \App\Models\Pos::where('principal_id','=',RepairorderController::getStsPrincipalId())->lists('posname','id')->all();//Stsservicelocation::lists('name','id')->all();

		if(is_null($postedData)){
			// prvo otvaranje - bez filtera lokacije
			$app = app();
    		$postedData = $app->make('stdClass');
			$postedData->lokacije=array($servicepersonlocation);

			$datumOdObj 	=\Carbon\Carbon::now()->startOfMonth();
			$postedData->datumOdObj=$datumOdObj;
			$postedData->datumOd = $datumOdObj->format("Y-m-d");//." 00:00:00"; // ponoć tog dana ">="

			$datumDoObj 	=\Carbon\Carbon::now();
			$postedData->datumDoObj=$datumDoObj;
			$postedData->datumDo = $datumDoObj->format("Y-m-d");//." 00:00:00"; // ponoć tog dana ">="
		} 



		// nađi sve naloge koji su zatvoreni
		$nalozi=\DB::table('repairorders')
						->select(
							'repairorders.id',
							'poses.posname',
							'repairorders.stsroclosingdate',
							'repairorders.stsserviceperson_id',
				
							\DB::raw('count(*) as realizacija'),
							\DB::raw('sum(if(repairorders.stsdeviceswap = "1", 1, 0)) as swap'),
							\DB::raw('sum(if(repairorders.stsmbswap = "1", 1, 0)) as mbswap'),
							\DB::raw('sum(if(repairorders.stsfailuredetected = "1", 1, 0)) as imakvar'),
							\DB::raw('sum(if(repairorders.devicewarranty = "1", 1, 0)) as jamstvo'),
							\DB::raw('sum(naplativiiznosi.ukupno) as iznos'),
 							\DB::raw('CONCAT(users.first_name," ",users.last_name) as serviser')
							)
						;
		// join table
		$nalozi=$nalozi->leftJoin('users','repairorders.stsserviceperson_id','=','users.id')
							->leftJoin('poses','repairorders.stsservicelocation_id','=','poses.id')
							->leftJoin('naplativiiznosi','repairorders.id','=','naplativiiznosi.nalog')
							;


		$nalozi=$nalozi->whereNotNull('repairorders.stsroclosingdate')
						->where('repairorders.stsserviceperson_id','>',1);

		//lokacije
		if (!is_null($postedData->lokacije)) { $nalozi	= $nalozi->whereIn('repairorders.stsservicelocation_id',$postedData->lokacije); }

		if (!is_null($postedData->datumOd)) { $nalozi=$nalozi->where('repairorders.stsroclosingdate','>=',$postedData->datumOd); }
		if (!is_null($postedData->datumDo)) { $nalozi=$nalozi->where('repairorders.stsroclosingdate','<=',$postedData->datumDo); }



		$nalozi=$nalozi->groupBy('repairorders.stsserviceperson_id');


		$nalozi=$nalozi->get();

//dd($nalozi);

		//if ($nalozi->count()) $nalozi=null;

		$sendData=array(
				'lokacije'	=> $lokacije,
				'postedData'=> $postedData,
				'realiziraniNalozi'	=> $nalozi,
				'activeOtprema' => 'active',
				'activeOtpremljeno' => '',
			);

		return view('reporti.realizacija')->with($sendData);
	}



	/*
	 */
	public function doRealizacija(Request $request){

		$lokacije = $request->input('serviceLocation'); // ili null

    	$datumOd=null;
    	$datumOdObj=null;
    	$datumDo=null;
    	$datumDoObj=null;
    	
    	// procesiraj ulazne
    	try{
    		if(!empty($request->input('datumOd'))) {
    			$datumOdObj 	=\Carbon\Carbon::parse($request->input('datumOd'));
				$datumOd = $datumOdObj->format("Y-m-d");//." 00:00:00"; // ponoć tog dana ">="
			}
    		if(!empty($request->input('datumDo'))) {
	    		$datumDoObj 	=\Carbon\Carbon::parse($request->input('datumDo'));
				$datumDo = $datumDoObj->format("Y-m-d");//." 00:00:00"; // + 1 dan, pa u ponoć "<"
			}
		} catch (\Exception $e) {
			dd('RptCtrl_doRealizacija.'.$e);
		}


		// složi postData objekt
		//$postedData = new \stdClass();
		$app = app();
    	$postedData = $app->make('stdClass');
		$postedData->lokacije=$lokacije;
		$postedData->datumOd = $datumOd;
		$postedData->datumDo = $datumDo;
		$postedData->datumOdObj=$datumOdObj;
		$postedData->datumDoObj=$datumDoObj;

    	// pošalji ih u view
		return $this->showRealizacija($request, $postedData);
	}







	/* VRAĆA JSON STATUSE ZA NALOG */
	public function dajStatuseNaloga(Request $request){
		if ($request->input('nalog') > 0) {
			$statusi=\App\Models\Repairorder::withTrashed()->find($request->input('nalog'))->repairstatuses;

			$vrati="<div class='container-fluid'>
						<div class='row'>
							<div class='col-md-12'>
								<table class='table'>
									<thead>
										<tr>
											<th>STATUS</th>
											<th>DATUM I VRIJEME</th>
											<th>KORISNIK</th>
										</tr>
									</thead>
									<tbody>";
								foreach ($statusi as $status) {
									$vrati.="<tr>";
										$vrati.="<td>".$status->name."</td>";
										$vrati.="<td>".$status->pivot->created_at."</td>";
										if ($status->user != $status->loggeduser){
											$vrati.="<td>".$status->loggeduser." za ".$status->user."</td>";
										} else {
											$vrati.="<td>".$status->user."</td>";
										}
									$vrati.="</tr>";
								}							
								$vrati.="</tr>
									</tbody>
								</table>
							</div>
						</div>
				  	</div>";
			return $vrati;			  	
		}
        return "Greška kod dohvaćanja statusa! Nema broja naloga";
	}




	/*
	otpremljeno report - GET - prikaz
	public function showOtpremljeno($postedData=null){

		$servicepersonlocation = \Auth::user()->location;
		$lokacije = \Pos::where('principal_id','=',RepairorderController::getStsPrincipalId())->lists('posname','id')->all();//Stsservicelocation::lists('name','id')->all();
		$retlokacije = $lokacije;

		if(is_null($postedData)){
			// prvo otvaranje - bez filtera lokacije
			$app = app();
    		$postedData = $app->make('stdClass');
			$postedData->retlokacije=array($servicepersonlocation);
			$postedData->lokacije=array($servicepersonlocation);
		} 

		// nađi sve naloge koji imaju zadnji status jedan od dva "otpremljena"
		$statusiOtpremljeno=RepairorderController::getStatusiOtpremljeno();
		$sql='
				SELECT t1.*
				FROM repairorder_repairstatus t1 
				LEFT JOIN repairorder_repairstatus t2
					ON (t1.repairorder_id = t2.repairorder_id 
						AND t1.created_at < t2.created_at
						)
				WHERE t2.created_at IS NULL 
			';
		if (!is_null($statusiOtpremljeno)) { 
			$sql.='	AND	t1.repairstatus_id IN ('.implode(',', $statusiOtpremljeno).')' ;
		}

		$nalozi = \DB::select($sql);

		$ids=array();
		foreach($nalozi as $id) {
			$ids[]=$id->repairorder_id;
		}


		// izvadi one koji imaju samo tu/te lokaciju/e
		$otpremljeniNalozi = Repairorder::with('repairstatuses','pos','model')->whereIn('id',$ids);
		if (!is_null($postedData->retlokacije)) { $otpremljeniNalozi	= $otpremljeniNalozi->whereIn('stsservicelocation_id',$postedData->retlokacije); }
		$otpremljeniNalozi=$otpremljeniNalozi->get();

		if ($otpremljeniNalozi->isEmpty()) $otpremljeniNalozi=null;


		$sendData=array(
				'lokacije'	=> $lokacije,
				'retlokacije'	=> $retlokacije,
				'postedData'=> $postedData,
				'naloziZaOtpremu'	=> null,
				'otpremljeniNalozi'	=> $otpremljeniNalozi,
				'activeOtprema' => '',
				'activeOtpremljeno' => 'active',
			);

		return view('reporti.otprema')->with($sendData);
	}

	public function doOtpremljeno(Request $request){

		$retlokacije = $request->input('servicereturnfromlocation'); // ili null
		$lokacije = null; // ili null

		// složi postData objekt
		//$postedData = new \stdClass();
		$app = app();
    	$postedData = $app->make('stdClass');
		$postedData->retlokacije=$retlokacije;
		$postedData->lokacije=$lokacije;

    	// pošalji ih u view
		return $this->showOtpremljeno($postedData);
	}


	 */


}
