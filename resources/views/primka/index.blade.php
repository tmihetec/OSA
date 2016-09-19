@extends('layouts.shell')

@section('container')


<!-- if there are creation errors, they will show here -->
{!! HTML::ul($errors->all(),array('class'=>'list-unstyled alert alert-danger', 'role'=>'alert')) !!}


<h1>Primke
    <a href="{{URL::to('primke/create')}}" title="Nova primka" class="label label-default pull-right" style="font-size:.5em; margin-top:10px" ><i class="glyphicon glyphicon-plus-sign"></i></a>
</h1>
	<hr />
	
<!-- will be used to show any messages -->
@if (Session::has('message'))
    <div class="alert alert-info">{{ Session::get('message') }}</div>
@endif


<!-- OTVORENE PRIMKE -->
@if(!empty($primke_open))
<h3>Otvorene primke</h3>
<table class="tmkdt tmkdt-primke table table-striped table-bordered" cellpadding="0" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>PRIMKA</th>
            <th>DATUM</th>
            <th>SKLADIŠTE</th>
            <th>DOBAVLJAČ</th>
            <th>DOKUMENT</th>
            <th>DATUM DOKUMENTA</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    @foreach($primke_open as $key => $value)
        <tr>
            <td>{{ $value->receipt_no }}</td> <!--$value->stsrepairorderno-->
            <td>{{ $value->receipt_datetime->format("d.m.Y") }}</td>
            <td>{{ $value->warehouse->code}}</td>
            <td>{{ $value->supplier->name }}</td>
            <td>{{ $value->document_no }}</td>
            <td>{{ $value->document_date->format("d.m.Y") }}</td>
            <td>
				<span class="btn-group btn-group-xs" role="group">
					<a class="btn btn-small btn-info" title="edit" href="{{ URL::to('primke/' . $value->id . '/edit') }}"><i class="glyphicon glyphicon-pencil"></i></a>
                    @if ($adminUser)
					<a class="btn  btn-small btn-danger" data-placement="left" title="Delete?" data-delete="{{ csrf_token() }}" data-myhref="{{ URL::to('primke/' . $value->id) }}"><i class="glyphicon glyphicon-trash"></i></a>
                    @endif
				</span>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
<hr />
@endif


<!-- ZATVORENE PRIMKE -->
<h3>Zaprimljene primke</h3>
<table class="tmkdt tmkdt-primke table table-striped table-bordered" cellpadding="0" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>PRIMKA</th>
            <th>DATUM</th>
            <th>SKLADIŠTE</th>
            <th>DOBAVLJAČ</th>
            <th>DOKUMENT</th>
            <th>DATUM DOKUMENTA</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    @foreach($primke_closed as $key => $value)
        <tr>
            <td>{{ $value->receipt_no }}</td> <!--$value->stsrepairorderno-->
            <td>{{ $value->receipt_datetime->format("d.m.Y") }}</td>
            <td>{{ $value->warehouse->code}}</td>
            <td>{{ $value->supplier->name }}</td>
            <td>{{ $value->document_no }}</td>
            <td>{{ (is_null($value->document_date)) ? "" : $value->document_date->format("d.m.Y") }}</td>
            <td>
                <span class="btn-group btn-group-xs" role="group">
                    <a class="btn btn-small btn-success" title="view" href="{{ URL::to('primke/' . $value->id . '/edit') }}"><i class="glyphicon glyphicon-eye-open"></i></a>
                    @if ($adminUser)
                        <a class="btn btn-small btn-info" title="edit" href="{{ URL::to('primke/' . $value->id . '/edit') }}"><i class="glyphicon glyphicon-pencil"></i></a>
                        <a class="btn  btn-small btn-danger" data-placement="left" title="Delete?" data-delete="{{ csrf_token() }}" data-myhref="{{ URL::to('primke/' . $value->id) }}"><i class="glyphicon glyphicon-trash"></i></a>
                    @endif
                </span>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>










	
@stop