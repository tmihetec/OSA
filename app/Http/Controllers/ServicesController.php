<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller, App\Http\Controllers\RepairorderController;
use Stsservice, View;
use Bouncer;

class ServicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($usluga=null)
    {
        //
        $usluge  =\App\Models\Stsservice::all();
        $adminUser  = Bouncer::is(\Auth::user())->an('admin');
        if ($usluga) $usluga = \App\Models\Stsservice::find($usluga);
        $uslugadrugo =RepairorderController::getOtherservice();
        $zadnjapreddefiniranausluga =RepairorderController::getOtherservice(); // zadnja je (6)
        $sendData=array(
            'usluga' => $usluga,
            'usluge'  => $usluge,
            'uslugadrugo' => $uslugadrugo,
            'zadnjapreddefiniranausluga'  => $zadnjapreddefiniranausluga,
            'adminUser'     => $adminUser
            );

        return View::make('usluge.index')->with($sendData);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return $this->index();
       // return View::make('komitenti.create')->with(['komitent'=>$komitent]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'name' => 'required',
            'jm' => 'required'
            ], [
            'name.required' => 'Niste unjeli naziv',
            'jm.required' => 'Niste unjeli jm'
            ]);

        $usluga=new \App\Models\Stsservice();
        $usluga->name=$request->input('name');
        $usluga->jm=$request->input('jm');
        $usluga->price=$request->input('price');
        $usluga->save();
        return $this->index();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        return $this->index();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
        return $this->index($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
         $this->validate($request, [
            'name' => 'required',
            'jm' => 'required'
            ], [
            'name.required' => 'Niste unjeli naziv',
            'jm.required' => 'Niste unjeli jm'
            ]);

        $usluga = \App\Models\Stsservice::find($id);
        $usluga->name=$request->input("name");
        $usluga->jm=$request->input("jm");
        $usluga->price=$request->input("price");
        $usluga->save();
        return $this->index();
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
            \App\Models\Stsservice::destroy($id);
        } catch (\Exception $e) {
            return $this->index()->withErrors('Nije moguće brisati, postoje stavke za uslugu.');
        }
        return $this->index();

    }
}
