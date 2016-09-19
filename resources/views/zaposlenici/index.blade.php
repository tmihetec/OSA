@extends('layouts.shell')

@section('container')


<!-- if there are creation errors, they will show here -->
{!! HTML::ul($errors->all(),array('class'=>'list-unstyled alert alert-danger', 'role'=>'alert')) !!}


<h1>Zaposlenici</h1>
	<hr />
	
<!-- will be used to show any messages -->
@if (Session::has('message'))
    <div class="alert alert-info">{{ Session::get('message') }}</div>
@endif




@if (!is_null($zaposlenik->id) &&  \Auth::user()->is('admin'))
<!-- edit -->
{!! Form::model($zaposlenik, ['url' => 'zaposlenici/'.$zaposlenik->id, 'method' => 'put']) !!}
 

<div class="row">
    <h3 class="col-sm-12"> Izmjena podataka o zaposleniku </h3>
</div>

<div class="row">
<div class="col-sm-2">
    <div class="form-group ">
    {!! Form::label('first_name', 'ime') !!}<br />
    {!! Form::text('first_name', null, array('class' => 'form-control')) !!} 
    </div>
    <div class="form-group">
    {!! Form::label('last_name', 'prezime') !!}<br />
    {!! Form::text('last_name', null, array('class' => 'form-control')) !!} 
    </div>
</div>
<div class="col-sm-3">
    <div class="form-group">
    {!! Form::label('user', 'username') !!}<br />
    {!! Form::text('user', null, array('class' => 'form-control')) !!} 
    </div>
    <div class="form-group">
    {!! Form::label('email', 'email') !!}<br />
    {!! Form::text('email', null, array('class' => 'form-control')) !!} 
    </div>
</div>
<div class="col-sm-4">
    <div class="form-group">
    {!! Form::label('roles', 'role') !!}<br />
    {!! Form::select('roles[]', $rolelist, $zaposlenik->roles->lists('id')->toArray(), ['multiple'=>'true', 'class' => 'form-control tmk-sing-select2']) !!}    
    </div>
    <div class="form-group">
    {!! Form::label('location', 'SPP (lokacija)') !!}<br />
    {!! Form::select('location', $lokacije, null, ['class' => 'form-control tmk-sing-nc-select2']) !!}    
    </div>
</div>
<div class="col-sm-3">
    <div class="form-group">
    {!! Form::label('aktivan', 'status') !!}<br />
    {!! Form::select('aktivan', ['1'=>'AKTIVAN', '0'=>'NEAKTIVAN'], null, ['class' => 'form-control']) !!}    
    </div>
    <div class="form-group">
    <button type="submit" class=" btn btn-success btn-large"><i class="glyphicon glyphicon-ok-sign"></i> IZMJENI</button>
    <a href="{{ URL::to('zaposlenici')}}" class="btn btn-danger btn-large"><i class="glyphicon glyphicon-ban-circle"></i> ODUSTANI</a>
    </div>
</div>


<div class="col-sm-3">
<div class="alert alert-danger">
    <div class="form-group">
    {!! Form::label('resetpassword', 'RESETIRAJ PASSWORD?') !!}<br />
    {!! Form::select('resetpassword', ['0'=>'NE', '1'=>'DA'], null, ['class' => 'form-control']) !!}    
    </div>
</div>
</div>

</div><!-- row -->

{!! Form::close() !!}
<hr />

@endif



<table class="tmkdt tmkdt-employee table table-striped table-bordered" cellpadding="0" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>ID</th>
            <th>USER</th>
            <th>IME I PREZIME</th>
            <th>LOKACIJA</th>
            <th>ROLE</th>
            <th>STATUS</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    @foreach($zaposlenici as $key => $value)
        <tr>
            <td>{{ $value->id }}</td> <!--$value->stsrepairorderno-->
            <td>{{ $value->user }}</td>
            <td>{{ $value->first_name." ".$value->last_name }}</td>
            <td>{!! (isset($value->lokacija->posname)) ? $value->lokacija->posname : "<span class='crveno'>NEMA</span>" !!}</td>
            <td>
                @if($value->roles->first())  
                    {{ implode(", ",array_pluck($value->roles->toArray(), 'name')) }}  
                @endif
            </td>
            <td>
                @if ($value->trashed())
                    NEAKTIVAN
                @else
                    AKTIVAN
                @endif
            </td>
            <td>
                <span class="btn-group btn-group-xs" role="group">
                    @if (\Auth::user()->is('admin'))
                    <a class="btn btn-small btn-info" title="edit" href="{{ URL::to('zaposlenici/' . $value->id . '/edit') }}"><i class="glyphicon glyphicon-pencil"></i></a>
                  
                    
                    @endif
                </span>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>


@if(is_null($zaposlenik->id) && \Auth::user()->is('admin'))
<hr />
<!-- create -->
{!! Form::model($zaposlenik, ['url' => 'zaposlenici', 'method' => 'post']) !!}

<div class="row">
    <h3 class="col-sm-12"> Novi zaposlenik </h3>
</div>
 
<div class="row">
<div class="col-sm-2">
    <div class="form-group ">
    {!! Form::label('first_name', 'ime') !!}<br />
    {!! Form::text('first_name', null, array('class' => 'form-control')) !!} 
    </div>
    <div class="form-group">
    {!! Form::label('last_name', 'prezime') !!}<br />
    {!! Form::text('last_name', null, array('class' => 'form-control')) !!} 
    </div>
</div>
<div class="col-sm-3">
    <div class="form-group">
    {!! Form::label('user', 'username') !!}<br />
    {!! Form::text('user', null, array('class' => 'form-control')) !!} 
    </div>
    <div class="form-group">
    {!! Form::label('email', 'email') !!}<br />
    {!! Form::text('email', null, array('class' => 'form-control')) !!} 
    </div>
</div>
<div class="col-sm-4">
    <div class="form-group">
    {!! Form::label('roles', 'role') !!}<br />
    {!! Form::select('roles[]', $rolelist, null, ['multiple'=>'true', 'class' => 'form-control tmk-sing-select2']) !!}    
    </div>
    <div class="form-group">
    {!! Form::label('location', 'SPP (lokacija)') !!}<br />
    {!! Form::select('location', $lokacije, 120, ['class' => 'form-control tmk-sing-nc-select2']) !!}    
    </div>
</div>
<div class="col-sm-3">
    <div class="form-group">
    {!! Form::label('aktivan', 'status') !!}<br />
    {!! Form::select('aktivan', ['1'=>'AKTIVAN', '0'=>'NEAKTIVAN'], null, ['class' => 'form-control']) !!}    
    </div>
    <div class="form-group">
    <button type="submit" class=" btn btn-success btn-large"><i class="glyphicon glyphicon-ok-sign"></i> KREIRAJ</button>
    <a href="{{ URL::to('zaposlenici')}}" class="btn btn-danger btn-large"><i class="glyphicon glyphicon-ban-circle"></i> ODUSTANI</a>
    </div>
</div>
<div class="col-sm-3">
    <p>Defaultni password će biti: <strong>hangar18</strong></p>
</div>
</div><!-- row -->

{!! Form::close() !!}

@endif




	
@stop