<?php
namespace App\Http\Controllers;


use \Illuminate\Http\Request;
use App\Role;

class SetupController extends Controller {

	public function __construct(){
		//echo "dashboard";
	}


	 public function setupRoles(){

	 	// ENTRUST
	 	// https://github.com/Zizaco/entrust
	 	
		$role = new Role();
		$role->name         = 'admin';
		$role->display_name = 'Administrator'; // optional
		$role->description  = 'Administrator može sve'; // optional
		$role->save();

		$role = new Role();
		$role->name         = 'logistika';
		$role->display_name = 'Logistika'; // optional
		$role->description  = 'Zaprimanje i otpremanje uređaja'; // optional
		$role->save();

		$role = new Role();
		$role->name         = 'servis';
		$role->display_name = 'Servis'; // optional
		$role->description  = 'Serviseri'; // optional
		$role->save();

		$role = new Role();
		$role->name         = 'pos';
		$role->display_name = 'Prodajno mjesto'; // optional
		$role->description  = 'Zaprimanje uređaja'; // optional
		$role->save();

	 }


	 public function setupUserRoles(){

	 	$role=Role::where('name','admin');

	 }


}
