<?php
namespace App\Http\Controllers;
use Input, View, Validator, Session, Redirect, Auth;
use User, Repairorder, Model, Repairstatus, Locplace, Pos;
use Claimtype, Stsservicelevel, Stsservicelocation;
use Customersymptom,Techniciansymptom, Faultyelement, Sparepart, Stsservice;
use App\Models\Soap\ws_tele2soap;

use \Illuminate\Http\Request;

class DashboardController extends Controller {

	public function __construct(){
		//echo "dashboard";
	}


	 public function show(){


		$t2nalozi=\App\Models\Soap\ws_tele2soap::where('sts_repairorder_number','<','1')->orWhereNull('sts_repairorder_number')->OrderBy('created_at','desc')->get();

		return View::make('dash')->with('t2nalozi', $t2nalozi);		

	 }

}
