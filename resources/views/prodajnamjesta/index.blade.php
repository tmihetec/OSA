@extends('layouts.shell')

@section('container')


<!-- if there are creation errors, they will show here -->
{!! HTML::ul($errors->all(),array('class'=>'list-unstyled alert alert-danger', 'role'=>'alert')) !!}


<h1>Prodajna mjesta / SPP
 <a href="{{URL::to('prodajnamjesta/create')}}" title="Novi POS" class="label label-default pull-right" style="font-size:.5em; margin-top:10px" ><i class="glyphicon glyphicon-plus-sign"></i></a>
</h1>
    <p class="notice">SPP - STS pickup point, svaka lokacija gdje je moguć prikup uređaja.</p>
    <hr />
	
<!-- will be used to show any messages -->
@if (Session::has('message'))
    <div class="alert alert-info">{{ Session::get('message') }}</div>
@endif


<table class="tmkdt tmkdt-pos table table-striped table-bordered" cellpadding="0" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>ID</th>
            <th>KOMITENT</th> <!-- fk -->
            <th>NAZIV</th> 
            <th>GRAD</th> <!-- fk -->
            <th>POSID</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    @foreach($posevi as $key => $value)
        <tr>
            <td>{{ $value->id }}</td> 
            <td>{{ $value->principal->naziv }}</td>
            <td>{{ $value->posname }}</td>
            <td>{{ (isset($value->grad)) ? $value->grad->name : ""}}</td> <! -- može biti null -->
            <td>{{ $value->posid  }}</td>
            <td>
                <span class="btn-group btn-group-xs" role="group">
                    <a class="btn btn-small btn-info" title="edit" href="{{ URL::to('prodajnamjesta/' . $value->id . '/edit') }}"><i class="glyphicon glyphicon-pencil"></i></a>
                    @if ($adminUser)
                    <a class="btn  btn-small btn-danger" data-placement="left" title="Delete?" data-delete="{{ csrf_token() }}" data-myhref="{{ URL::to('prodajnamjesta/' . $value->id) }}"><i class="glyphicon glyphicon-trash"></i></a>
                    @endif
                </span>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>


	
@stop