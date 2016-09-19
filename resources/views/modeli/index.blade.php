@extends('layouts.shell')

@section('container')


<!-- if there are creation errors, they will show here -->
{!! HTML::ul($errors->all(),array('class'=>'list-unstyled alert alert-danger', 'role'=>'alert')) !!}


<h1>Modeli uređaja</h1>
	<hr />
	
<!-- will be used to show any messages -->
@if (Session::has('message'))
    <div class="alert alert-info">{{ Session::get('message') }}</div>
@endif



<table class="tmkdt tmkdt-modeli table table-striped table-bordered" cellpadding="0" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>ID</th>
            <th>TIP</th>
            <th>BRAND</th>
            <th>MODEL UREĐAJA</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    @foreach($modeli as $key => $value)
        <tr>
            <td>{{ $value->id }}</td> <!--$value->stsrepairorderno-->
            <td>{{ $value->devicetype->naziv }}</td>
            <td>{{ $value->brand->name }}</td>
            <td>{{ $value->name }}</td>
            <td>
                <span class="btn-group btn-group-xs" role="group">
                    <a class="btn btn-small btn-info" title="edit" href="{{ URL::to('modeli/' . $value->id . '/edit') }}"><i class="glyphicon glyphicon-pencil"></i></a>
                    @if ($adminUser)
                    <a class="btn  btn-small btn-danger" data-placement="left" title="Delete?" data-delete="{{ csrf_token() }}" data-myhref="{{ URL::to('modeli/' . $value->id) }}"><i class="glyphicon glyphicon-trash"></i></a>
                    @endif
                </span>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<hr />



@if (!is_null($model))
<!-- edit -->
{!! Form::model($model, ['url' => 'modeli/'.$model->id, 'method' => 'put', 'class'=>'form-inline']) !!}
 
<div class="row">

<div class="col-sm-12">
   <h3>Izmjena modela uređaja</h3>
    </div>

    <div class="col-sm-12">
        <div class="form-group">
        {!! Form::label('devicetype_id', 'Tip uređaja') !!} <br />
        {!! Form::select('devicetype_id', $tipovi, null, array('class' => 'form-control', 'placeholder'=>'-Odaberi-')) !!} 
        </div>

        <div class="form-group">
        {!! Form::label('brand_id', 'Brand uređaja') !!} <br />
        {!! Form::select('brand_id', $brandovi, null, array('class' => 'form-control', 'placeholder'=>'-Odaberi-')) !!} 
        </div>

        <div class="form-group">
        {!! Form::label('name', 'Model') !!} <br />
        {!! Form::text('name', null, array('class' => 'form-control')) !!}         

        <button type="submit" class="btn btn-default btn-large"><i class="glyphicon glyphicon-ok-sign"></i> IZMJENI</button>
        <a href="{{ URL::to('modeli')}}" class="btn btn-default btn-large"><i class="glyphicon glyphicon-ban-circle"></i> ODUSTANI</a>
        </div>

    </div>

</div>


@else 
<!-- create -->
{!! Form::model($model, ['url' => 'modeli', 'method' => 'post', 'class'=>'form-inline']) !!}
 
<div class="row">

    <div class="col-sm-12">
   <h3>Novi model uređaja</h3>
    </div>


    <div class="col-sm-12">
        
        <div class="form-group">
        {!! Form::label('devicetype_id', 'Tip uređaja') !!} <br />
        {!! Form::select('devicetype_id', $tipovi, null, ['class' => 'form-control', 'placeholder' => '-Odaberi-']) !!} 
        </div>

        <div class="form-group">
        {!! Form::label('brand_id', 'Brand uređaja') !!} <br />
        {!! Form::select('brand_id', $brandovi, null, array('class' => 'form-control', 'placeholder'=>'-Odaberi-')) !!} 
        </div>

        <div class="form-group">
        {!! Form::label('name', 'Model') !!} <br />
        {!! Form::text('name', null, array('class' => 'form-control')) !!} 

        <button type="submit" class="btn btn-default btn-large"><i class="glyphicon glyphicon-plus-sign"></i> DODAJ</button>
        </div>

    </div>

</div>


@endif


   


{!! Form::close() !!}

	
@stop