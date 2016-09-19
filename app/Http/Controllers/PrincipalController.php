<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Principal, View;
use Bouncer;

class PrincipalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($komitent=null)
    {
        //
        $komitenti  =\App\Models\Principal::all();
        $adminUser  = Bouncer::is(\Auth::user())->an('admin');
        if ($komitent) $komitent = \App\Models\Principal::find($komitent);
        $sendData=array(
            'komitent' => $komitent,
            'komitenti'  => $komitenti,
            'adminUser'     => $adminUser
            );

        return View::make('komitenti.index')->with($sendData);
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

        $principal=new \App\Models\Principal();
        $principal->naziv=$request->input('naziv');
        $principal->save();
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

        $principal = \App\Models\Principal::find($id);
        $principal->naziv=$request->input("naziv");
        $principal->save();
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
            \App\Models\Principal::destroy($id);
        } catch (\Exception $e) {
            return $this->index()->withErrors('Nije moguće brisati, postoje stavke za komitenta.');
        }
        return $this->index();

    }
}
