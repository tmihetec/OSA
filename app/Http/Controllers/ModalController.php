<?php

namespace App\Http\Controllers;

//use App\Models\User;
use Illuminate\Http\Request;

use Bouncer, URL;
use App\Models\Repairorder;

class ModalController extends Controller {




	public static function  iscrtajExservisiModal($value=null){

		if (!$value) return "nema u bazi";

		$exservisi = \DB::table('repairorders')
			->select('*',\DB::raw(' IF(deviceoutgoingimei<>deviceincomingimei,"da","ne") as zamjena' ))			

			->where(function ($q) use ($value) {
				$q->where('deviceincomingimei','=', $value)
				  ->orWhere(function($qu) use ($value) {
				  		$qu->where('deviceoutgoingimei','=',$value)
				  			->where('deviceincomingimei','<>',$value);
				});
			})			
			
			->whereNotNull('deviceincomingimei')
			->whereNull('deleted_at')
			->get();


		if (!$exservisi) {
			return "nema u bazi ništa s tim imei/sn";
		}	else {



		$modalcontent = '

		      <div class="modal-body">
		        <table class="table ">
					<thead>
						<tr>
							<th>RADNI NALOG</th>
							<th>OTVOREN</th>
							<th>OTPREMLJEN</th>
							<th>KORISNIK</th>
							<th>OPIS ZAHVATA</th>
						</tr>
					</thead>
					<tbody>';

        foreach($exservisi as $exservis)
        {
			$modalcontent .= '
				        	<tr>
				        		<td class="text-nowrap"><a href="'.URL::to("slucaj/".$exservis->id).'" target="_blank">'.$exservis->stsrepairorderno.'</a></td>
				        		<td class="text-nowrap">'.$exservis->stsroopendate.'</td>
				        		<td class="text-nowrap">'.$exservis->devicereturndate.'</td>
				        		<td class="">'.$exservis->customername." ".$exservis->customerlastname.'</td>
				        		<td>'.$exservis->stsnotice;			        		

			if($exservis->zamjena=="da")
			{
				$modalcontent .= '
				        			<span style="color:#f00"> ZAMJENA </span>
				        		';
			
			}
			$modalcontent .= '
									        		</td>
				        	</tr>
				        	';
        }

		$modalcontent .= '
					</tbody>
				</table>
		      </div>
		';

		return $modalcontent;
		}

	}




	public static function  iscrtajHistoryModal($id=null){

		if (!($slucaj = Repairorder::withTrashed()->find($id))) {
			dd("modalcontroller - forbidden");
		}





	$modalcontent = 
		'<div class="modal-dialog modal-lg" role="document">
	 	   <div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="historyModalLabel">Statusi za nalog '.$slucaj->stsrepairorderno.'</h4>
			</div>

			<div class="modal-body">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
							<table class="table">
								<thead>
									<tr>
										<th>LOKACIJA</th>
										<th>STATUS</th>
										<th>ODREDIŠTE</th>
										<th>DATUM I VRIJEME</th>
										<th>KORISNIK</th>
										<th></th>
									</tr>
								</thead>
								<tbody>';

		foreach($slucaj->repairstatuses as $status) 
		{

			$modalcontent.= '
				<tr>
					<td>';
			$modalcontent.= (count($status->lokacija)) ? $status->lokacija->posname : "";
			$modalcontent.= '</td><td>';
			$modalcontent.= $status->name;
			$modalcontent.= '</td><td>';
			$modalcontent.= (count($status->odrediste)) ? $status->odrediste->posname : "";
			$modalcontent.= '</td><td>';
			$modalcontent.= $status->pivot->created_at;
			$modalcontent.= '</td><td>';
            $modalcontent.= ($status->user != $status->loggeduser) ? $status->loggeduser." za ".$status->user : $status->user;
			$modalcontent.= '</td><td>';

			if(Bouncer::is(\Auth::user())->an('superadmin')) {

					$modalcontent.='
						<span class="btn-group btn-group-xs" style="display:flex;" role="group">

							<a class="btn btn-xs btn-danger detailsModalBtn" data-placement="left" title="Briši status?" 

									data-pivotid="'.$status->pivot->id.'" 
									data-status="'.$status->pivot->repairstatus_id.'" 
									data-order="'.$slucaj->id.'" 
									data-action="deleteStatus" 
									data-token="'.csrf_token().'"

									><i class="glyphicon glyphicon-trash"></i></a>

							<a class="btn btn-xs btn-warning detailsModalBtn" disabled="disabled" data-placement="left" title="Izmjeni zapis?"  

									data-pivotid="'.$status->pivot->id.'" 
									data-status="'.$status->pivot->repairstatus_id.'" 
									data-order="'.$slucaj->id.'" 
									data-action="editStatus" 
									data-token="'.csrf_token().'"

									><i class="glyphicon glyphicon-pencil"></i></a>
					';

					// samo ako su servis over i rejected statusi 
					if(in_array($status->repairstatus_id,\App\Http\Controllers\RepairorderController::getStatusipredotpremu())) {
						$modalcontent.='	
							<a class="btn btn-xs btn-info detailsModalBtn" data-placement="left" title="Postavi podatke za zatvoren nalog (osoba, lokacija, closing date)?" 

								data-pivotid="'.$status->pivot->id.'" 
								data-status="'.$status->pivot->repairstatus_id.'" 
								data-order="'.$slucaj->id.'" 
								data-action="setClosingData" 
								data-token="'.csrf_token().'"

								><i class="glyphicon glyphicon-off"></i></a>';
					} else { 
						$modalcontent.='
							<a class="btn btn-xs btn-info disabled " disabled="disabled"><i class="glyphicon glyphicon-off"></i></a>';
					}


					//samo ako su servisni statusi + servis over i rejected statusi 
					if(in_array($status->repairstatus_id,array_merge(\App\Http\Controllers\RepairorderController::getStatusiServis(),\App\Http\Controllers\RepairorderController::getStatusipredotpremu()))){
						$modalcontent.='
							<a class="btn btn-xs btn-info detailsModalBtn" data-placement="left" title="Postavi podatke o servisu (osoba, lokacija)?" 

								data-action="setServiceData" 
								data-pivotid="'.$status->pivot->id.'" 
								data-status="'.$status->pivot->repairstatus_id.'" 
								data-order="'.$slucaj->id.'" 
								data-token="'.csrf_token().'"

								><i class="glyphicon glyphicon-wrench"></i></a>';
					} else { 
						$modalcontent.='	
							<a class="btn btn-xs btn-info disabled" disabled="disabled"><i class="glyphicon glyphicon-wrench"></i></a>';
					}


					// samo ako je status "otpremljeno" 
					if(in_array($status->repairstatus_id,\App\Http\Controllers\RepairorderController::getStatusiOtpremljeno())) {
						$modalcontent.='	
							<a class="btn btn-xs btn-info detailsModalBtn" data-placement="left" title="Postavi podatke za otpremljen nalog (osoba, datum)?" 

								data-action="setReturnedData"  
								data-pivotid="'.$status->pivot->id.'" 
								data-status="'.$status->pivot->repairstatus_id.'" 
								data-order="'.$slucaj->id.'" 
								data-token="'.csrf_token().'"

								><i class="glyphicon glyphicon-plane"></i></a>';
					} else { 
						$modalcontent.='
							<a class="btn btn-xs btn-info disabled " disabled="disabled"><i class="glyphicon glyphicon-plane"></i></a>';
					}

					$modalcontent.='</span>';

				} // da li je superadmin

				$modalcontent.='</td>';

			$modalcontent.='</tr>';

		} // foreach 

		$modalcontent.='</tbody>
					</table>';


		//ISPOD TABLICE SA HISTORIJEM: samo SUPER ADMIN:: BRISANJE 
		if(Bouncer::is(\Auth::user())->an('superadmin')) {

			$modalcontent.='<hr />';
			$modalcontent.='
				<div class="row">
					<div class="col-xs-6">
						<table class="table table-bordered table-condensed">
			';

			if (is_null($slucaj->stsroclosingdate)) {

					$modalcontent.='
						<tr>
							<td>
								ZADNJI SERVISER KOJI JE RADIO: '.$slucaj->serviser->user.'
							</td>
						</tr>';
			} else {

				if (count($slucaj->serviser)) {

					if (is_null($slucaj->stsroclosingdate)) {

					$modalcontent.='
						<tr>
							<td>
								ZADNJI SERVISER KOJI JE RADIO: {{$slucaj->serviser->ime_prezime}}
							</td>
						</tr>';

					} else {
						
					$modalcontent.='				
						<tr>
							<td>
								ZADNJI SERVISER 
							</td>
							<td colspan="2">
								 '.$slucaj->serviser->ime_prezime.'
							</td>
							<td>
								<a class="btn btn-xs btn-warning detailsModalBtn" data-placement="left" title="Promijeni zadnjeg servisera?" data-action="changeServiceperson" disabled="disabled"  data-order="'.$slucaj->id.'" data-token="'.csrf_token().'"><i class="glyphicon glyphicon-pencil"></i></a>
							</td>
						</tr>

						<tr>
							<td>
								<span title="SERVIS ZAVRŠEN ili ODUSTANAK OD SERVISA">ZATVOREN</span> 
							</td>
							<td colspan="2">
								'.$slucaj->stsroclosingdate.'
							</td>
							<td>
								<a class="btn btn-xs btn-danger detailsModalBtn" data-placement="left" title="Briši datum zatvaranja?" data-action="deleteClosingDate" data-order="'.$slucaj->id.'" data-token="'.csrf_token().'"><i class="glyphicon glyphicon-trash"></i></a>
												<a class="btn btn-xs btn-warning detailsModalBtn" data-placement="left" title="Promijeni zadnjeg servisera?" data-action="changeClosingDate" disabled="disabled"  data-order="'.$slucaj->id.'" data-token="'.csrf_token().'"><i class="glyphicon glyphicon-pencil"></i></a>			
							</td>
						</tr>
						';

										
					} // da li je još uvijek otvoren nalog2?


				} // da li postoji serviser


				if (count($slucaj->logisticarotpremio)) {

					$modalcontent.='
						<tr>
							<td>
								<span title="OTPREMLJENO IZ SERVISA">OTPREMIO</span>
							</td>
							<td>
								'.$slucaj->logisticarotpremio->user.'
							</td>
							<td>
								'.$slucaj->devicereturndate.'
							</td>
							<td>
								<a class="btn btn-xs btn-danger detailsModalBtn" data-placement="left" title="Briši podatke o otpremi (osoba, datum)?" data-action="deleteReturnData"  data-order="'.$slucaj->id.'" data-token="'.csrf_token().'"><i class="glyphicon glyphicon-trash"></i></a>
								<a class="btn btn-xs btn-warning detailsModalBtn" data-placement="left" title="Izmjeni podatke o otpremi?" data-action="editReturnData" disabled="disabled" data-order="'.$slucaj->id.'" data-token="'.csrf_token().'"><i class="glyphicon glyphicon-pencil"></i></a>			
							</td>
						</tr>
					';

				} // da li postoji osoba koja je otpremila?


			} // da li je još uvijek otvoren nalog2?

			$modalcontent.='
						</table>							
					</div>
				</div>
			';
		} // da li je SUPERADMIN


		// završetak modala
		$modalcontent.='
			   </div>
			  </div>
			 </div>
			<!-- main GRID content -->
			</div>
		   <div class="modal-footer">
		  <button type="button" class="btn btn-default" data-dismiss="modal">Zatvori</button>
		 </div>
		</div>
	   </div>
		';

		return $modalcontent;

}





















	public function detailsModalBtn(Request $request){


		if($request->ajax()){

			// DEFAULT, SUCCESS CALL
			$msg="";
			$statusresp = "success";
			$postaction= false;
			$postdata= false;

			$action = $request->input("action");
			$order_id = $request->input("order");
			$status_id = $request->input("status");
			$pivot_id= $request->input("pivotid");

			// deleteStatus, editStatus, setClosingData, setServiceData, setReturnedData
			// changeServiceperson
			// deleteClosingDate, changeClosingDate
			// deleteReturnData, changeReturnData

			if ($action == "deleteStatus"   && Bouncer::is(\Auth::user())->an('superadmin') ){

				try{

					// spremiti u log
					$logaction = "delete status";
					$lognalog = $order_id;
					$logdata =\DB::table('repairorder_repairstatus')->where('id','=',$pivot_id)->first();
				    //$log = app()->make('\App\Models\Osalog',array($logaction, $logdata));
					//$log->store();
					\Log::notice($logaction." for repairorder: ".$lognalog." - data: ".json_encode($logdata));

					$msg="obrisan status id: ".$status_id." za nalog: ".$lognalog;


					// ovisno koji je status..
					switch ($status_id) {
						// DELETED
						case \App\Http\Controllers\RepairorderController::getStatusDeleted():

							// ako je obrisan onda treba:
							//  - logirati kaj je u pivot entryju
							//  - izbrisati taj PIVOT entry
							//  - unsoftdeletati taj repairorder
							 
							// HARD:
							\DB::table('repairorder_repairstatus')->where('id','=',$pivot_id)->delete();
							
							// SOFT: (onda treba prilagoditi i kontrolere za prikaz zadnjeg statusa...)
							/* \DB::table('repairorder_repairstatus')->where('id','=',$pivot_id)
								->update(array(
									'deleted_at'=>\DB::raw('NOW()')
									));
							*/
							$nalog=\App\Models\Repairorder::withTrashed()->findOrFail($order_id)->restore();
							break;

						default:
					} 


					$postaction= "reload";

				} catch(\Exception $e){
					$msg=$e." greška kod brisanja statusa; ModalController XX375";
					$statusresp = "error";
				}

			} elseif ($action == "deleteReturnData"   && Bouncer::is(\Auth::user())->an('superadmin') ) {

			
				try{

					$order=Repairorder::where('id','=',$order_id)->first();
					$order->devicereturndate= null;
					$order->devicereturnperson_id=null;
					$order->save();

					$msg="Obrisani podaci o dostavi za nalog ".$order->stsrepairorderno;
					$postaction= "reload";

				} catch(\Exception $e){
					$msg=$e." greška kod brisanja podataka za otpremu; ModalController XX307";
					$statusresp = "error";
				}



			} elseif ($action == "setReturnedData"  && Bouncer::is(\Auth::user())->an('superadmin') ){

				try{

					$order=Repairorder::where('id','=',$order_id)->first();
					$pivotrow=\DB::table('repairorder_repairstatus')->where('id',$pivotid)->first();					
					$order->devicereturndate= date("Y-m-d", strtotime($pivotrow->created_at));
					$order->devicereturnperson_id=$pivotrow->user_id;
					$order->save();

					$msg="Postavljeni podaci o otpremi za nalog ".$order_id." : ".date("Y-m-d", strtotime($pivotrow->created_at)).", ".$pivotrow->user_id;
					$postaction= "reload";
				
				} catch(\Exception $e){
					$msg=$e." greška kod postavljanja podataka za otpremu; ModalController XX314";
					$statusresp = "error";
				}
				


			} elseif ($action == "deleteClosingDate" && Bouncer::is(\Auth::user())->an('superadmin') ) {

			
				try{

					$order=Repairorder::where('id','=',$order_id)->first();
					$order->stsroclosingdate= null;
					$order->save();

					$msg="Obrisan datum zatvaranja naloga ".$order->stsrepairorderno;
					$postaction= "reload";

				} catch(\Exception $e){
					$msg=$e." greška kod brisanja datuma zatvaranja; ModalController XX429";
					$statusresp = "error";
				}



			}





			// VRATI
			$response = array(
		            'status' => $statusresp,
		            'msg' => $msg,
		            'postaction' => $postaction,
		            'postdata'=>array('id'=>$order_id),
	        );
			return \Response::json($response);


		} else {

			// Nije ajax call
			return "iz kontrolera, nije ajax";

		}

	}


}
