<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Model, Devicetype, Brand, View;
use Bouncer;

class ModelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($model=null)
    {
        //
        $modeli  =\App\Models\Model::all();
        $tipovi  =\App\Models\Devicetype::lists('naziv', 'id')->all();
        $brandovi  =\App\Models\Brand::lists('name', 'id')->all();
        $adminUser  = Bouncer::is(\Auth::user())->an('admin');
        if ($model) $model = \App\Models\Model::find($model);
        $sendData=array(
            'model' => $model,
            'modeli'  => $modeli,
            'brandovi'=> $brandovi,
            'tipovi'=> $tipovi,
            'adminUser'     => $adminUser
            );

        return View::make('modeli.index')->with($sendData);
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
            'devicetype_id' => 'required',
            'brand_id' => 'required'
            ], [
            'name.required' => 'Niste unjeli naziv',
            'devicetype_id.required' => 'Niste odabrali tip',
            'brand_id.required' => 'Niste odabrali brand'
            ]);

        $model=new \App\Models\Model();
        $model->name=$request->input('name');
        $model->brand_id=$request->input('brand_id');
        $model->devicetype_id=$request->input('devicetype_id');
        $model->save();
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
            'devicetype_id' => 'required',
            'brand_id' => 'required'
            ], [
            'name.required' => 'Niste unjeli naziv',
            'devicetype_id.required' => 'Niste odabrali tip',
            'brand_id.required' => 'Niste odabrali brand'
            ]);

        $model = \App\Models\Model::find($id);
        $model->name=$request->input("name");
        $model->brand_id=$request->input('brand_id');
        $model->devicetype_id=$request->input('devicetype_id');
        $model->save();
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
            \App\Models\Model::destroy($id);
        } catch (\Exception $e) {
            return $this->index()->withErrors('Nije moguće brisati, postoje stavke za taj uređaj.');
        }
        return $this->index();

    }
}
