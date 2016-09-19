<?php
namespace App\Http\Controllers;

use Auth, Redirect, View, Validator, Input;
use User;
use Illuminate\Http\Request;

class LoginController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function showLogin()
	{
		// show the form
		return View::make('login');
	}

	public function doLogin(Request $request)
	{
		// process the form
		
		// validate the info, create rules for the inputs
		$rules = array(
			'user'    => 'required', // 
			'password' => 'required|alphaNum|min:3' // password can only be alphanumeric and has to be greater than 3 characters
		);

		$messages = [
			'user.required' => 'Korisničko ime je obvezno polje.',
			'password.required' => 'Lozinka je obvezno polje.',
			'password.alphaNum' => 'Neispravna lozinka',
			'password.min' => 'Neispravna lozinka'
		];

		// run the validation rules on the inputs from the form
		$validator = Validator::make($request->all(), $rules,$messages);
		
		// if the validator fails, redirect back to the form
		if ($validator->fails()) {
		
			return Redirect::to('auth/login')
				->withErrors($validator) // send back all errors to the login form
				->withInput($request->except('password')); // send back the input (not the password) so that we can repopulate the form
				
		} else {
		
			// create our user data for the authentication
			$userdata = array(
				'user' 		=> $request->user, //strtolower(Input::get('username')),
				'password' 	=> $request->password
			);

			// remember me!
			$remember = ($request->has('remember')) ? true : false;		
			
			// attempt to do the login
			if (Auth::attempt($userdata, $remember)) {

				// validation successful!
				// redirect them to the secure section or whatever
				// return Redirect::to('secure');
				// for now we'll just echo success (even though echoing in a controller is bad)
				//echo 'SUCCESS!';
				


				// admin na dashboard
				if (\Bouncer::is(Auth::user())->an('admin')) { return Redirect::to('dashboard/'); }
				// logistika na t2 naloge
				else if (\Bouncer::is(Auth::user())->an('logistika')) { return Redirect::to('t2nalozi/'); }
				// ostali (serviseri) na popis naloga
				else { return Redirect::to('slucaj/'); }
 


			} else {	 	

				// validation not successful, send back to form	
				return Redirect::to('auth/login')
					->withInput($request->except('password'))
					->with('message-error', 'Neispravni pristupni podaci.');
			}

		}
	}
	
	public function doLogout()
	{
		Auth::logout(); // log the user out of our application
		return Redirect::to('auth/login'); // redirect the user to the login screen
	}

}
