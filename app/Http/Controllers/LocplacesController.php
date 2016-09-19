<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Sparepart, View, App\Models\User, App\Models\Locplace;
use Bouncer;

class LocplacesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($locplace=null)
    {
     
        //
        $gradovi  =\App\Models\Locplace::with(['country','area'])->get();
        $adminUser  = Bouncer::is(\Auth::user())->an('admin');
        $locareas = \App\Models\Locarea::lists('name','id');
        $loccountries = \App\Models\Loccountry::lists('name','id');

        if ($locplace) {
            $locplace = \App\Models\Locplace::with(['country','area'])->find($locplace);
        } else {
            $locplace=New \App\Models\User();
        }
        $sendData=array(
            'grad' => $locplace,
            'gradovi'  => $gradovi,
            'adminUser'     => $adminUser,
            'areas'  => $locareas,
            'countries'  => $loccountries,
            );
        return View::make('gradovi.index')->with($sendData);
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
    public function store(Request $request, $grad=null)
    {


        $myrules=[
            'name' => 'required',
            'postalcode' => 'required|unique:locplaces,postalcode',
            'loccountry_id' => 'required'
        ];


        if ($grad) { ($grad = \App\Models\Locplace::find($grad)) ? :new \App\Models\Locplace(); }
        else {$grad = new \App\Models\Locplace();}

//        if($request->method()=="PUT")
        if ($grad->postalcode)
        {
            $myrules['postalcode']='required|unique:locplaces,postalcode,'.$grad->id.',id';
        }

        $this->validate($request, $myrules, [
            'name.required' => 'Niste unjeli naziv',
            'postalcode.required' => 'Niste unjeli poštanski broj',
            'postalcode.unique' => 'Već postoji mjesto sa tim poštanskim brojem',
            'loccountry_id.required' => 'Niste unjeli zemlju'
            ]);




        // update polja
        $grad->name = $request->input('name');
        $grad->postalcode = $request->input('postalcode');
        $grad->locarea_id = $request->input('locarea_id');
        $grad->loccountry_id = $request->input('loccountry_id');

        $grad->save();

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
            \App\Models\User::destroy($id);
        } catch (\Exception $e) {
            return $this->index()->withErrors('Nije moguće brisati.');
        }
        return $this->index();

    }
}
