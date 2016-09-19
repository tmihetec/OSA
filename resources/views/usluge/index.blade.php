@extends('layouts.shell')

@section('container')


<!-- if there are creation errors, they will show here -->
{!! HTML::ul($errors->all(),array('class'=>'list-unstyled alert alert-danger', 'role'=>'alert')) !!}


<h1>Preddefinirane usluge</h1>
	<hr />
	
<!-- will be used to show any messages -->
@if (Session::has('message'))
    <div class="alert alert-info">{{ Session::get('message') }}</div>
@endif



<table class="tmkdt tmkdt-services table table-striped table-bordered" cellpadding="0" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>ID</th>
            <th>USLUGA</th>
            <th>JM</th>
            <th>CIJENA</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    @foreach($usluge as $key => $value)
        <!-- ako je id od 1-5 može samo edit cijene -->
        <!-- ako je id 6 nemože ništa -->
        <tr>
            <td>{{ $value->id }}</td> <!--$value->stsrepairorderno-->
            <td>{{ $value->name }}</td>
            <td>{{ $value->jm }}</td>
            <td>{{ $value->price }}</td>
            <td>
                @if ($value->id !== $uslugadrugo)
                <span class="btn-group btn-group-xs" role="group">
                    <a class="btn btn-small btn-info" title="edit" href="{{ URL::to('usluge/' . $value->id . '/edit') }}"><i class="glyphicon glyphicon-pencil"></i></a>
                    @if ($adminUser && $value->id > $zadnjapreddefiniranausluga)
                    <a class="btn  btn-small btn-danger" data-placement="left" title="Delete?" data-delete="{{ csrf_token() }}" data-myhref="{{ URL::to('usluge/' . $value->id) }}"><i class="glyphicon glyphicon-trash"></i></a>
                    @endif
                </span>
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<hr />



@if (!is_null($usluga))
<!-- edit -->
{!! Form::model($usluga, ['url' => 'usluge/'.$usluga->id, 'method' => 'put', 'class'=>'form-inline']) !!}
 


<div class="row">

    <div class="col-sm-12">
        <h3>Izmjena usluge</h3>
        @if($usluga->id < $zadnjapreddefiniranausluga)
        <p class="notice">Kod usluga 1-5 moguće je mijenjati samo cijenu.</p>
        @endif
    </div>

    @if($usluga->id !== $uslugadrugo)

                    <div class="col-sm-12">
                        
                        <div class="form-group">
                            {!! Form::label('name', 'Naziv') !!} <br />
                            @if($usluga->id < $zadnjapreddefiniranausluga)
                                {!! Form::text('fakename', $usluga->name, array('disabled'=>'disabled', 'class' => 'form-control')) !!}
                                {!! Form::hidden('name', $usluga->name, array('class' => 'form-control')) !!}
                            @else
                                {!! Form::text('name', null, array('class' => 'form-control')) !!}
                            @endif     
                        </div>
                    
                        <div class="form-group">
                            {!! Form::label('jm', 'jm') !!} <br />
                            @if($usluga->id < $zadnjapreddefiniranausluga)
                                {!! Form::text('fakejm', $usluga->jm, array('disabled'=>'disabled', 'class' => 'form-control')) !!} 
                                {!! Form::hidden('jm', $usluga->jm, array('class' => 'form-control')) !!} 
                            @else
                                {!! Form::text('jm', null, array('class' => 'form-control')) !!} 
                            @endif     
                        </div>

                        <div class="form-group">
                            {!! Form::label('price', 'cijena') !!} <br />
                            {!! Form::text('price', null, array('class' => 'form-control decimalInput')) !!} 

                            <button type="submit" class="btn btn-default btn-large"><i class="glyphicon glyphicon-ok-sign"></i> IZMJENI</button>
                            <a href="{{ URL::to('usluge')}}" class="btn btn-default btn-large"><i class="glyphicon glyphicon-ban-circle"></i> ODUSTANI</a>
                        </div>

                    </div>
    @else 
        <div class="col-sm-12">
            <p class="notice">Uslugu 6 (Drugo) nije moguće mijenjati.</p>
        </div>
    @endif                

</div>


@else 
<!-- create -->
{!! Form::model($usluga, ['url' => 'usluge', 'method' => 'post', 'class'=>'form-inline']) !!}
 
<div class="row">

    <div class="col-sm-12">
    {!! Form::label('name', 'Nova usluga') !!}
    </div>

                <div class="col-sm-12">
                        
                        <div class="form-group">
                            {!! Form::label('name', 'naziv') !!} <br />
                            {!! Form::text('name', null, array('class' => 'form-control')) !!}
                        </div>
                    
                        <div class="form-group">
                            {!! Form::label('jm', 'jm') !!} <br />
                            {!! Form::text('jm', null, array('class' => 'form-control')) !!} 
                        </div>

                        <div class="form-group">
                            {!! Form::label('price', 'cijena') !!} <br />
                            {!! Form::text('price', null, array('class' => 'form-control decimalInput')) !!} 

                            <button type="submit" class="btn btn-default btn-large"><i class="glyphicon glyphicon-plus-sign"></i> DODAJ</button>
                        </div>

                    </div>
    </div>


@endif


   


{!! Form::close() !!}

	
@stop