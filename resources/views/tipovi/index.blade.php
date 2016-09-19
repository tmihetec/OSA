@extends('layouts.shell')

@section('container')


<!-- if there are creation errors, they will show here -->
{!! HTML::ul($errors->all(),array('class'=>'list-unstyled alert alert-danger', 'role'=>'alert')) !!}


<h1>Tipovi uređaja</h1>
	<hr />
	
<!-- will be used to show any messages -->
@if (Session::has('message'))
    <div class="alert alert-info">{{ Session::get('message') }}</div>
@endif



<table class="tmkdt tmkdt-simple table table-striped table-bordered" cellpadding="0" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>ID</th>
            <th>TIP</th>
            <th>IMA IMEI?</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    @foreach($tipovi as $key => $value)
        <tr>
            <td>{{ $value->id }}</td> <!--$value->stsrepairorderno-->
            <td>{{ $value->naziv }}</td>
            <td>{{ ($value->imaimei == 1) ? "DA" : "NE" }}</td>
            <td>
                <span class="btn-group btn-group-xs" role="group">
                    <a class="btn btn-small btn-info" title="edit" href="{{ URL::to('tipovi/' . $value->id . '/edit') }}"><i class="glyphicon glyphicon-pencil"></i></a>
                    @if ($adminUser)
                    <a class="btn  btn-small btn-danger" data-placement="left" title="Delete?" data-delete="{{ csrf_token() }}" data-myhref="{{ URL::to('tipovi/' . $value->id) }}"><i class="glyphicon glyphicon-trash"></i></a>
                    @endif
                </span>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<hr />



@if (!is_null($tip))
<!-- edit -->
{!! Form::model($tip, ['url' => 'tipovi/'.$tip->id, 'method' => 'put', 'class'=>'form-inline']) !!}
 
<div class="row">

    <div class="col-sm-12">
    {!! Form::label('naziv', 'Izmjena tipa uređaja / ima imei?') !!}
    </div>

    <div class="col-sm-6">
        
        {!! Form::text('naziv', null, array('class' => 'form-control')) !!} 
        {!! Form::select('imaimei', array("1"=>"DA", "0"=>"NE"), null, array('class' => 'form-control')) !!} 
        <button type="submit" class="btn btn-default btn-large"><i class="glyphicon glyphicon-ok-sign"></i> IZMJENI</button>
        <a href="{{ URL::to('tipovi')}}" class="btn btn-default btn-large"><i class="glyphicon glyphicon-ban-circle"></i> ODUSTANI</a>
    </div>

</div>


@else 
<!-- create -->
{!! Form::model($tip, ['url' => 'tipovi', 'method' => 'post', 'class'=>'form-inline']) !!}
 
<div class="row">

    <div class="col-sm-12">
    {!! Form::label('naziv', 'Novi tip uređaja / ima imei?') !!}
    </div>

    <div class="col-sm-6">
        
        {!! Form::text('naziv', null, array('class' => 'form-control')) !!} 
        {!! Form::select('imaimei', array("1"=>"DA", "0"=>"NE"), null, array('class' => 'form-control')) !!} 
        <button type="submit" class="btn btn-default btn-large"><i class="glyphicon glyphicon-plus-sign"></i> DODAJ</button>
    </div>
</div>


@endif


   


{!! Form::close() !!}

	
@stop