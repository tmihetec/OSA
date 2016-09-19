<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Sparepart, View, App\Models\User, App\Models\Role;
use Bouncer;


class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($employee=null)
    {
     
        //
        $zaposlenici  =\App\Models\User::withTrashed()->with('roles')->get();
        $adminUser  = Bouncer::is(\Auth::user())->an('admin');
        $lokacije = \App\Models\Pos::all()->lists('custom_name','id');
        //$rolelist = \App\Models\Role::lists('display_name','id');
        $rolelist = \App\Models\Role::lists('display_name','id');

        if ($employee) {
            $employee = \App\Models\User::withTrashed()->with('roles')->find($employee);
        } else {
            $employee=New \App\Models\User();
        }
        $sendData=array(
            'zaposlenik' => $employee,
            'zaposlenici'  => $zaposlenici,
            'adminUser'     => $adminUser,
            'lokacije'  => $lokacije,
            'rolelist'  => $rolelist,
            );

        return View::make('zaposlenici.index')->with($sendData);
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
            'user' => 'required',
            'location' => 'required|min:1|not_in:120,119',
            'roles' => 'required|array',
            'email' => 'required|email'
            ], [
            'user.required' => 'Niste unjeli korisničko ime',
            'location.not_in' => 'Odaberite lokaciju / SPP'
            ]);


        $employee = new User();

        // update polja
        $employee->first_name = $request->input('first_name');
        $employee->last_name = $request->input('last_name');
        $employee->email = $request->input('email');
        $employee->user = $request->input('user');
        $employee->location = $request->input('location');

        // neki defaultni password kod kreacije
        $employee->password = \Hash::make("hangar18");

        // ak je aktivan = 1, deleted_at na null
        if ($request->input('aktivan')) {
            $employee->deleted_at=null;
        } else {
            $employee->deleted_at=\Carbon\Carbon::now();
        }
        $employee->save();
        // roles
        $employee->roles()->sync($request->input('roles'));

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
            'user' => 'required',
            'location' => 'required|min:1|not_in:120,119',
            'roles' => 'required|array',
            'email' => 'required|email'
            ], [
            'user.required' => 'Niste unjeli korisničko ime',
            'location.not_in' => 'Odaberite lokaciju / SPP'
            ]);


        $employee = User::withTrashed()->find($id);

        // update polja
        $employee->first_name = $request->input('first_name');
        $employee->last_name = $request->input('last_name');
        $employee->email = $request->input('email');
        $employee->user = $request->input('user');
        $employee->location = $request->input('location');


        if(Bouncer::is(\Auth::user())->an('admin') && $request->input('resetpassword')==1) {
            $employee->password = \Hash::make("hangar18");
        }

        // roles
        $employee->roles()->sync($request->input('roles'));
        // ak je aktivan = 1, deleted_at na null
        if ($request->input('aktivan')) {
            $employee->deleted_at=null;
        } else {
            $employee->deleted_at=\Carbon\Carbon::now();
        }
        $employee->save();

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
            \App\Models\User::destroy($id);
        } catch (\Exception $e) {
            return $this->index()->withErrors('Nije moguće brisati.');
        }
        return $this->index();

    }
}
