@extends('layouts.shell')

@section('container')




<!-- if there are creation errors, they will show here -->
{!! HTML::ul($errors->all(),array('class'=>'list-unstyled alert alert-danger', 'role'=>'alert')) !!}


<h1>
    @if (is_null($editingID))
    Novo prodajno mjesto / SPP
    @else
    Uređivanje prodajnog mjesta / SPP
    @endif
    <a href="{{URL::to("prodajnamjesta")}}" title="Popis prodajnim mjesta" class="label label-default pull-right" style="font-size:.5em; margin-top:10px" ><i class="glyphicon glyphicon-list"></i></a>
</h1>
<hr />

<!-- will be used to show any messages -->
@if (Session::has('message'))
<div class="alert alert-info">{{ Session::get('message') }}</div>
@endif




@if (isset($editingID))
<!-- edit -->
<?php /* {!! Form::model($primka, ['route' => array('primke.update', $primka->id), 'method' => 'put']) !!} */ ?>
{!! Form::model($pos, ['url' => URL::to("prodajnamjesta/".$pos->id), 'method' => 'put']) !!}
@else 
<!-- create -->
{!! Form::model($pos, ['url' => URL::to("prodajnamjesta"), 'method' => 'post']) !!}
@endif


<div class="row">


    <div class="col-sm-6">
        {!! Form::label('posname', 'Naziv prodajnog mjesta / SPP') !!}  
        {!! Form::text('posname',null,array('class' => 'form-control')) !!}
        {!! Form::label('posadresa', 'Adresa') !!}  
        {!! Form::text('posadresa',null,array('class' => 'form-control')) !!}
        {!! Form::label('posplace_id', 'Grad') !!}  
        {!! Form::select('posplace_id',$gradovi, null, array('class' => 'form-control tmk-sing-nc-select2')) !!}
        <div class="row clearfix">
            <div class="col-sm-6">
                {!! Form::label('posphone1', 'Telefon') !!}  
                {!! Form::text('posphone1',null,array('class' => 'form-control')) !!}
            </div>
            <div class="col-sm-6">
                {!! Form::label('posemail', 'Email') !!}  
                {!! Form::text('posemail',null,array('class' => 'form-control')) !!}
            </div>
        </div>
        <hr />
        {!! Form::label('posmanagername', 'Voditelj prodajnog centra') !!}  
        {!! Form::text('posmanagername',null,array('class' => 'form-control')) !!}
        <div class="row clearfix">
            <div class="col-sm-6">
                {!! Form::label('posmanagerphone', 'Telefon voditelja') !!}  
                {!! Form::text('posmanagerphone',null,array('class' => 'form-control')) !!}
            </div>
            <div class="col-sm-6">
                {!! Form::label('posmanagermail', 'Email voditelja') !!}  
                {!! Form::text('posmanagermail',null,array('class' => 'form-control')) !!}
            </div>
        </div>
    </div>
    <div class="col-sm-6">

        {!! Form::label('posid', 'Broj prodajnog mjesta / SPP (POSID ako postoji)') !!}  
        {!! Form::text('posid',null,array('class' => 'form-control')) !!}

        {!! Form::label('principal_id', 'Komitent') !!}  
        {!! Form::select('principal_id',array(""=>"")+$principals, null, array('class' => 'form-control tmk-sing-nc-select2')) !!}

        {!! Form::label('posstatus_id', 'Status') !!}  
        {!! Form::select('posstatus_id',$posstatuses, null, array('class' => 'form-control')) !!}

        <hr />
          <div class="well well-sm">
            {!! Form::label('distributer_id', 'Tele2 Distributer') !!}  
            {!! Form::select('distributer_id',array(""=>"")+$distributers, null, array('class' => 'form-control tmk-sing-select2')) !!}
            {!! Form::label('partner_id', 'Tele2 Partner') !!}  
            {!! Form::select('partner_id',array(""=>"")+$partners, null, array('class' => 'form-control tmk-sing-select2')) !!}
            {!! Form::label('postype_id', 'Tele2 Tip') !!}  
            {!! Form::select('postype_id',array(""=>"")+$postypes, null, array('class' => 'form-control tmk-sing-select2')) !!}
        </div>

    </div>


</div>


<!-- spremi samo ako je nova ili otvorena primka -->
@if($adminUser) 
<div class="row">
<div class="col-sm-5">

         {!! Form::submit('Spremi', array('class' => 'btn btn-primary')) !!}
            <a href="{{URL::to('prodajnamjesta')}}" class='btn btn-success'>Odustani</a>

</div>
</div>
@endif


{!! Form::close() !!}


@stop