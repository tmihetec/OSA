@extends('layouts.shell')

@section('container')


<!-- if there are creation errors, they will show here -->
{!! HTML::ul($errors->all(),array('class'=>'list-unstyled alert alert-danger', 'role'=>'alert')) !!}


<h1>Brandovi</h1>
	<hr />
	
<!-- will be used to show any messages -->
@if (Session::has('message'))
    <div class="alert alert-info">{{ Session::get('message') }}</div>
@endif



<table class="tmkdt tmkdt-simple table table-striped table-bordered" cellpadding="0" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>ID</th>
            <th>BRAND</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    @foreach($brandovi as $key => $value)
        <tr>
            <td>{{ $value->id }}</td> <!--$value->stsrepairorderno-->
            <td>{{ $value->name }}</td>
            <td>
                <span class="btn-group btn-group-xs" role="group">
                    <a class="btn btn-small btn-info" title="edit" href="{{ URL::to('brandovi/' . $value->id . '/edit') }}"><i class="glyphicon glyphicon-pencil"></i></a>
                    @if ($adminUser)
                    <a class="btn  btn-small btn-danger" data-placement="left" title="Delete?" data-delete="{{ csrf_token() }}" data-myhref="{{ URL::to('brandovi/' . $value->id) }}"><i class="glyphicon glyphicon-trash"></i></a>
                    @endif
                </span>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<hr />



@if (!is_null($brand))
<!-- edit -->
{!! Form::model($brand, ['url' => 'brandovi/'.$brand->id, 'method' => 'put']) !!}
 
<div class="row">

    <div class="col-sm-12">
    {!! Form::label('name', 'Izmjena branda') !!}
    </div>

    <div class="col-sm-6">
        
        {!! Form::text('name', null, array('class' => 'form-control')) !!} 
    </div>
        <button type="submit" class="pull-left btn btn-default btn-large"><i class="glyphicon glyphicon-ok-sign"></i> IZMJENI</button>
    <div class="col-sm-1">
        <a href="{{ URL::to('brandovi')}}" class="btn btn-default btn-large"><i class="glyphicon glyphicon-ban-circle"></i> ODUSTANI</a>
    </div>

</div>


@else 
<!-- create -->
{!! Form::model($brand, ['url' => 'brandovi', 'method' => 'post']) !!}
 
<div class="row">

    <div class="col-sm-12">
    {!! Form::label('name', 'Novi brand') !!}
    </div>

    <div class="col-sm-6">
        
        {!! Form::text('name', null, array('class' => 'form-control')) !!} 
    </div>
        <button type="submit" class="pull-left btn btn-default btn-large"><i class="glyphicon glyphicon-plus-sign"></i> DODAJ</button>
</div>


@endif


   


{!! Form::close() !!}

	
@stop