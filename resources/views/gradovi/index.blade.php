@extends('layouts.shell')

@section('container')


<!-- if there are creation errors, they will show here -->
{!! HTML::ul($errors->all(),array('class'=>'list-unstyled alert alert-danger', 'role'=>'alert')) !!}


<h1>Gradovi</h1>
	<hr />
	
<!-- will be used to show any messages -->
@if (Session::has('message'))
    <div class="alert alert-info">{{ Session::get('message') }}</div>
@endif




@if (!is_null($grad->id) &&  \Auth::user()->is('admin'))
<!-- edit -->
{!! Form::model($grad, ['url' => 'gradovi/'.$grad->id, 'method' => 'put']) !!}
 

<div class="row">
    <h3 class="col-sm-12"> Izmjena podataka o gradu </h3>
</div>

<div class="row">
<div class="col-sm-2">
    <div class="form-group ">
    {!! Form::label('name', 'Naziv') !!}<br />
    {!! Form::text('name', null, array('class' => 'form-control')) !!} 
    </div>
</div>
<div class="col-sm-2">
    <div class="form-group">
    {!! Form::label('postalcode', 'Poštanski broj') !!}<br />
    {!! Form::text('postalcode', null, array('class' => 'form-control')) !!} 
    </div>
</div>


<div class="col-sm-3">
    <div class="form-group">
    {!! Form::label('locarea_id', 'Županija') !!}<br />
    {!! Form::select('locarea_id', $areas, $grad->area->id, ['class' => 'form-control tmk-sing-select2']) !!}    
    </div>
</div>
<div class="col-sm-2">
    <div class="form-group">
    {!! Form::label('loccountry_id', 'Grad') !!}<br />
    {!! Form::select('loccountry_id', $countries, $grad->country->id, ['class' => 'form-control tmk-sing-nc-select2']) !!}    
    </div>
</div>
<div class="col-sm-3">
    <div class="form-group">
    <button type="submit" class=" btn btn-success btn-large"><i class="glyphicon glyphicon-ok-sign"></i> IZMJENI</button>
    <a href="{{ URL::to('gradovi')}}" class="btn btn-danger btn-large"><i class="glyphicon glyphicon-ban-circle"></i> ODUSTANI</a>
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
            <th>NAZIV</th>
            <th>POŠTANSKI</th>
            <th>ŽUPANIJA</th>
            <th>ZEMLJA</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    @foreach($gradovi as $key => $value)
        <tr>
            <td>{{ $value->id }}</td> <!--$value->stsrepairorderno-->
            <td>{{ $value->name }}</td>
            <td>{{ $value->postalcode }}</td>
            <td>{!! (isset($value->area->name)) ? $value->area->name : "<span class='crveno'>NEMA</span>" !!}</td>
            <td>{!! (isset($value->country->name)) ? $value->country->name : "<span class='crveno'>NEMA</span>" !!}</td>
            <td>
                <span class="btn-group btn-group-xs" role="group">
                    @if (\Auth::user()->is('admin'))
                    <a class="btn btn-small btn-info" title="edit" href="{{ URL::to('gradovi/' . $value->id . '/edit') }}"><i class="glyphicon glyphicon-pencil"></i></a>
                  
                    
                    @endif
                </span>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>




@if(is_null($grad->id) && \Auth::user()->is('admin'))
<hr />
<!-- create -->
{!! Form::model($grad, ['url' => 'gradovi', 'method' => 'post']) !!}

<div class="row">
    <h3 class="col-sm-12"> Novi grad </h3>
</div>
 

<div class="row">
<div class="col-sm-2">
    <div class="form-group ">
    {!! Form::label('name', 'Naziv') !!}<br />
    {!! Form::text('name', null, array('class' => 'form-control')) !!} 
    </div>
</div>
<div class="col-sm-2">
    <div class="form-group">
    {!! Form::label('postalcode', 'Poštanski broj') !!}<br />
    {!! Form::text('postalcode', null, array('class' => 'form-control')) !!} 
    </div>
</div>

<div class="col-sm-3">
    <div class="form-group">
    {!! Form::label('locarea_id', 'Županija') !!}<br />
    {!! Form::select('locarea_id', $areas, null, ['class' => 'form-control tmk-sing-select2']) !!}    
    </div>
</div>
<div class="col-sm-2">
    <div class="form-group">
    {!! Form::label('loccountry_id', 'Zemlja') !!}<br />
    {!! Form::select('loccountry_id', $countries, null, ['class' => 'form-control tmk-sing-nc-select2']) !!}    
    </div>
</div>
<div class="col-sm-3">
    <div class="form-group">
    <button type="submit" class=" btn btn-success btn-large"><i class="glyphicon glyphicon-ok-sign"></i> KREIRAJ</button>
    <a href="{{ URL::to('gradovi')}}" class="btn btn-danger btn-large"><i class="glyphicon glyphicon-ban-circle"></i> ODUSTANI</a>
    </div>
</div>

</div><!-- row -->


{!! Form::close() !!}

@endif




	
@stop