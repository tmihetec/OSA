<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Sparepart, View;
use Bouncer;

class SparepartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($dio=null)
    {
        //
        $dijelovi  =\App\Models\Sparepart::all();
        $adminUser  = Bouncer::is(\Auth::user())->an('admin');
        if ($dio) $dio = \App\Models\Sparepart::find($dio);
        $sendData=array(
            'dio' => $dio,
            'dijelovi'  => $dijelovi,
            'adminUser'     => $adminUser
            );

        return View::make('rezervnidijelovi.index')->with($sendData);
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
            'name' => 'required'
            ], [
            'name.required' => 'Niste unjeli naziv'
            ]);

        $dio=new \App\Models\Sparepart();
        $dio->name=$request->input('name');
        $dio->save();
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
            'name' => 'required'
            ], [
            'name.required' => 'Niste unjeli naziv'
            ]);

        $dio = \App\Models\Sparepart::find($id);
        $dio->name=$request->input("name");
        $dio->save();
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
            \App\Models\Sparepart::destroy($id);
        } catch (\Exception $e) {
            return $this->index()->withErrors('Nije moguće brisati, postoje stavke za rezervni dio.');
        }
        return $this->index();

    }
}
