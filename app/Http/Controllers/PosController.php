<?php

namespace App\Http\Controllers;

use Pos, View, Principal, Locplace, Posstatus, Distributer, Postype, Partner;

use Illuminate\Http\Request;
use Bouncer;


class PosController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        //
        $posevi  =\App\Models\Pos::with(array('principal','grad'))->orderBy('id','desc')->get();
        $adminUser  = Bouncer::is(\Auth::user())->an('admin');
        $sendData=array(
            'posevi'  => $posevi,
            'adminUser'     => $adminUser
            );

        return View::make('prodajnamjesta.index')->with($sendData);
   	}

    
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create($editingID=null)
	{

		$pos = (is_null($editingID)) ? new \App\Models\Pos() : \App\Models\Pos::find($editingID);

		$principals=\App\Models\Principal::lists('naziv','id')->all();
		$posstatuses=\App\Models\Posstatus::lists('name','id')->all();
		$postypes=\App\Models\Postype::lists('name','id')->all();
		$distributers=\App\Models\Distributer::lists('name','id')->all();
		$partners=\App\Models\Partner::lists('name','id')->all();
		$gradovi=\App\Models\Locplace::get()->lists('custom_name','id')->all();
        $adminUser  = Bouncer::is(\Auth::user())->an('admin');


		$sendData=array(
			'principals' => $principals,
			'posstatuses' => $posstatuses,
			'postypes' => $postypes,
			'distributers' => $distributers,
			'partners' => $partners,
			'gradovi' => $gradovi,
			'pos'=>$pos,
			'editingID' => $editingID,
			'adminUser' => $adminUser
			);

		return View::make('prodajnamjesta.create')->with($sendData);
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request, $pos=null)
	{
		//

		$this->validate($request,[
			'posname'=>'required',
			'principal_id'=>'required|numeric|min:1'
			],[
			'posname.required'=>'Naziv prodajnog mjesta je obvezno polje.',
			'principal_id.required'=>'Odaberite komitenta.',
			]);

		try {
			$pos = (is_null($pos)) ? new \App\Models\Pos() : \App\Models\Pos::findOrFail($pos);

			$pos->posname 			= $request->input('posname');
			$pos->posadresa			= $request->input('posadresa');
			$pos->posplace_id		= $request->input('posplace_id');
			$pos->posphone1			= $request->input('posphone1');
			$pos->posemail			= $request->input('posemail');
			$pos->posmanagername	= $request->input('posmanagername');
			$pos->posmanagerphone	= $request->input('posmanagerphone');
			$pos->posmanagermail	= $request->input('posmanagermail');
			$pos->posid				= $request->input('posid');
			$pos->principal_id		= $request->input('principal_id');
			$pos->distributer_id	= $request->input('distributer_id');
			$pos->partner_id		= $request->input('partner_id');
			$pos->postype_id		= $request->input('postype_id');
			$pos->posstatus_id		= $request->input('posstatus_id');
			
			$pos->save();

		} catch (Exception $e) {
			dd("nepostojeće prodajno mjesto.");
		}

		return \Redirect::to('prodajnamjesta');
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
		return $this->edit($id);
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		return $this->create($id);
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(Request $request, $id)
	{
		//
		return $this->store($request, $id);
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
		try{
			\App\Models\Pos::destroy($id);

		} catch (\Exception $e) {
			return \Redirect::to('prodajnamjesta')->withErrors("Prodajno mjesto ima povezane dokumente, brisanje nije moguće.");
		}
	
		return \Redirect::to('prodajnamjesta');	

	}

}
