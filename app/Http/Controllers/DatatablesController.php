<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Yajra\Datatables\Datatables;
use Repairorder;
use Cache, Session;
use Bouncer, URL;

class DatatablesController extends Controller
{


/**
 *	#http://datatables.yajrabox.com/fluent/advance-filter
 *  #recimo za "više od x dana na servisu" - i za REPORT ONE!
*/


/**
 * Process datatables ajax request. - prikupi naloge i napravi json
 * 
 *
 * @return \Illuminate\Http\JsonResponse
 */
public function dohvatiArhivaNaloge(Request $request)
{
		$kolekcijaNaloga=$this->generirajArhivaCollection($request);
		$pripremljeniNaloziZaDatatable=$this->napraviDtJSON($request, $kolekcijaNaloga);
		return $pripremljeniNaloziZaDatatable;
}





/**
 *  za prikaz prve stranice na ARHIVA - bolji UX, prvo prikaži prvih 25 (ili kolko već treba)
 *  i onda dalje rješavaj prek ajaxa
 *
 *  vraća ukupan broj redova i prvih 25
 */
public static function dohvatiArhivaIntro(Request $request, $limit=false)
{
	$o=new self;
	$col=$o->generirajArhivaCollection($request);
	$total=$col->count();
	if ($limit) 
	{			
		$col->take($limit);
	}

	// napravi polja kak trebaju biti, jedini je bed kaj će se vratiti JsonResponse
	$pripremljeniNaloziZaDatatable=$o->napraviDtJSON($request, $col);

	// pošto je napravljen json objekt za DT, raspakiraj i uzmi samo redove
	$nalozi=json_decode($pripremljeniNaloziZaDatatable->getContent())->data;

	// vrati
	return ["total"=>$total, "nalozi"=>$nalozi];
}









// ===========================================================================================
// HELPERS
// ===========================================================================================





/**
 *  genereira kolekciju svih naloga za daljnju manupulaciju
 */
public function generirajArhivaCollection(Request $request, $samoOtvoreni=false)
{
		// koje naloge vidi?
		if (Bouncer::allows('vidiSveNaloge')){
			$spp_id=false;
		} else {
			// onda samo sa svoje lokacije
			$spp_id=\Auth::user()->location;
		}


		// zadnjistatusi 
		$zssql=' ';

		// polja
		$slucajevi=\DB::table('repairorders')
						->select(
							'repairorders.id',
							'repairorders.stsrepairorderno',
							'repairorders.stsroopendate',
							'repairorders.deviceincomingimei',
							'repairorders.posrepairorderno',
							'poses.posname',
							'repairstatuses.name as zadnjistatus',
							\DB::raw('if(repairorders.deleted_at IS NULL, 0,1) as brisan'),

							\DB::raw('CONCAT(brands.name," ",models.name) as uredjaj'),
							\DB::raw('CONCAT(repairorders.customername," ",repairorders.customerlastname) as korisnik')
							)
						;
		// join table
		$slucajevi->leftJoin('poses','repairorders.pos_id','=','poses.id')
							 ->leftJoin('models','repairorders.devicemodel_id','=','models.id')
							 ->leftJoin('brands','models.brand_id','=','brands.id')
							 ->leftJoin('zs','zs.repairorder_id','=','repairorders.id')
							 ->leftJoin('repairstatuses', 'zs.repairstatus_id','=','repairstatuses.id')
							;

		// zatvoreni / otvoreni?
		if ($samoOtvoreni) {
			$slucajevi->whereNull('repairorders.devicereturndate');
			//$slucajevi=$slucajevi->whereNotNull('repairorders.devicereturndate');
		}

		// spp ili admin?
		if ($spp_id) {
			$slucajevi->where(function ($q) use ($spp_id)  {
									$q->where('repairorders.pickuppos_id',$spp_id)
										->orWhere('repairorders.pos_id',$spp_id);
									});
		}

		$slucajevi->orderBy('stsroopendate','desc');

		return $slucajevi;
}







/**
 * Pripremi DT JSONRESPONSE od naloga za
 *
 * @return \Illuminate\Http\JsonResponse
 */
public function napraviDtJSON(Request $request, $slucajevi){

	//ob_start('ob_gzhandler');
     $data = Datatables::of($slucajevi)
			->addColumn('DT_RowId',function($ro){return $ro->id;}) //https://datatables.net/examples/server_side/ids.html
	       	->addColumn('alati', function ($ro){
	       			$tools= '<span class="btn-group btn-group-xs" style="display:flex;" role="group">';
	                $tools.=    '<a class="btn btn-small btn-warning" target="_blank" title="edit" href="'.URL::to("slucaj/".$ro->id).'/edit"><i class="glyphicon glyphicon-pencil"></i></a>';
	                $tools.=    '<a class="btn btn-small btn-info" target="_blank" title="Prijemni list" href="'.URL::to("printPrijemniView/rn/".$ro->id).'"><i class="glyphicon glyphicon-import"></i></a>';
	                $tools.=    '<a class="btn btn-small btn-primary" target="_blank" title="Radni nalog" href="'.URL::to("printView/rn/".$ro->id).'"><i class="glyphicon glyphicon-export"></i></a>';
	                if (Bouncer::is(\Auth::user())->an('admin')) {

	                	if ($ro->brisan) {
	                    	$tools.= '<a class="btn btn-small btn-danger disabled" ><i class="glyphicon glyphicon-trash"></i></a>';
						} else {
	                    	$tools.= '<a class="btn btn-small btn-danger" data-placement="left" title="Delete?" data-delete="'.csrf_token().'" data-myhref="'.URL::to("slucaj/".$ro->id).'"><i class="glyphicon glyphicon-trash"></i></a>';
						}
	                }
	                $tools.= '</span>';
	                return $tools;
		       	})
    		->editColumn('posname', function ($ro) {
                return (($ro->posname)) ? $ro->posname : "<span class='crveno'>NEMA</span>";
            })
    		->editColumn('stsroopendate', function ($ro) {
                return $ro->stsroopendate ? with(new \Carbon\Carbon($ro->stsroopendate))->format('d.m.Y') : '';
            });


	// Global search function
    if ($keyword = $request->get('search')['value']) {
        // // override users.name global search
        // $data->filterColumn('users.name', 'where', 'like', "$keyword%");
        // override users.id global search - demo for concat
        $data->filterColumn('uredjaj', 'whereRaw', "CONCAT(brands.name,' ',models.name) like ? ", ["%$keyword%"]);
        $data->filterColumn('korisnik', 'whereRaw', "CONCAT(repairorders.customername,' ',repairorders.customerlastname) like ? ", ["%$keyword%"]);
        $data->filterColumn('zadnjistatus', 'whereRaw', "repairstatuses.name like ? ", ["%$keyword%"]);
    }

    return $data->make(true);

}





}
