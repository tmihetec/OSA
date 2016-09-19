<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Brand, View, Bouncer;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($brand=null)
    {
        //
        $brandovi  =\App\Models\Brand::all();
        $adminUser  = Bouncer::is(\Auth::user())->an('admin');
        if ($brand) $brand = \App\Models\Brand::find($brand);
        $sendData=array(
            'brand' => $brand,
            'brandovi'  => $brandovi,
            'adminUser'     => $adminUser
            );

        return View::make('brandovi.index')->with($sendData);
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

        $brand=new \App\Models\Brand();
        $brand->name=$request->input('name');
        $brand->save();
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

        $brand = \App\Models\Brand::find($id);
        $brand->name=$request->input("name");
        $brand->save();
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
            \App\Models\Brand::destroy($id);
        } catch (\Exception $e) {
            return $this->index()->withErrors('Nije moguće brisati, postoje stavke za brand.');
        }
        return $this->index();

    }
}
