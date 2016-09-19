<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Devicetype, View;
use Bouncer;

class TipController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($tip=null)
    {
        //
        $tipovi  = \App\Models\Devicetype::all();
        $adminUser  = Bouncer::is(\Auth::user())->an('admin');
        if ($tip) $tip = \App\Models\Devicetype::find($tip);
        $sendData=array(
            'tip' => $tip,
            'tipovi'  => $tipovi,
            'adminUser'     => $adminUser
            );

        return View::make('tipovi.index')->with($sendData);
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
            'naziv' => 'required'
            ], [
            'naziv.required' => 'Niste unjeli naziv'
            ]);

        $tip=new \App\Models\Devicetype();
        $tip->naziv=$request->input('naziv');
        $tip->imaimei=$request->input('imaimei');
        $tip->save();
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
            'naziv' => 'required'
            ], [
            'naziv.required' => 'Niste unjeli naziv'
            ]);

        $tip = \App\Models\Devicetype::find($id);
        $tip->naziv=$request->input("naziv");
        $tip->imaimei=$request->input('imaimei');
        $tip->save();
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
            \App\Models\Devicetype::destroy($id);
        } catch (\Exception $e) {
            return $this->index()->withErrors('Nije moguće brisati, postoje stavke za tip uređaja.');
        }
        return $this->index();

    }
}
