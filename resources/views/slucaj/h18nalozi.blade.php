@extends('layouts.shell')

@section('container')


<!-- if there are creation errors, they will show here -->
{!! HTML::ul($errors->all(),array('class'=>'list-unstyled alert alert-danger', 'role'=>'alert')) !!}


<!-- will be used to show any messages -->
@if (Session::has('message'))
    <div class="alert alert-info">{{ Session::get('message') }}</div>
@endif

<!--

<h1>Dashboard</h1>
    <hr />
    

<div class="row">
    <div class="col-xs-4">
        <div class="panel panel-primary dash-blue">
            <div class="panel-heading">
                <div class="panelhead">TELE2</div>
            </div>
            <div class="panel-footer">
                    <div data-toggle="tooltip" data-placement="bottom" title="nalozi koji čekaju zaprimanje" class="value">4445</div>
            </div>
        </div>
    </div>
    <div class="col-xs-4">
        <div class="panel panel-primary dash-magenta">
            <div class="panel-heading">
                <div class="panelhead">RADNI NALOZI</div>
            </div>
            <div class="panel-footer">
            <div class="row">
                <div class="col-xs-6 rightdotted">
                    <div data-toggle="tooltip" data-placement="bottom" title="nalozi koji čekaju servis" class="value">4445</div>
                </div>
                <div class="col-xs-6">
                    <div data-toggle="tooltip" data-placement="bottom" title="jučer zatvoreni nalozi" class="value">4445</div>
                </div>
            </div>
            </div>
        </div>
    </div>
    <div class="col-xs-4 ">
        <div class="panel panel-primary dash-red">
            <div class="panel-heading">
                <div class="panelhead">TOP SERVISER</div>
            </div>
            <div class="panel-footer">
                    <div  data-toggle="tooltip" data-placement="bottom" title="najviše zatvorenih naloga tekući mjesec" class="value">4445</div>
            </div>
        </div>
    </div>
</div>
-->


<!-- TABLICA SA T2 SOAP -->

<h2>SPP nalozi na čekanju</h2>
<p class="notice">Zaprimljeni samo na SPP (još nisu zaprimljeni u servisu)</p>
<hr />
<table class="tmkdt tmkdt-t2 table table-striped table-bordered table-hover nowrap" cellspacing="0" width="100%">   
   <thead>
        <tr>
            <th>STS RN</th>
            <th>ZAPRIMLJEN</th>
            <th>OTVOREN ZA SERVIS</th>
            <th>UREĐAJ</th>
            <th>IMEI</th>
            <th>POS</th>
            <th></th>
        </tr>
    </thead>
    <tbody>

    @foreach($h18nalozi as $nalog)
        <tr>
            <td>{{$nalog->stsrepairorderno}}</td>
            <td>{{$nalog->stsroopendate->format("d.m.Y")}}</td>
            <td>{{$nalog->servicelocation->posname}}</td>
            <td>{{$nalog->model->brand->name." ".$nalog->model->name}}</td>
            <td>{{$nalog->deviceincomingimei}}</td>
            <td>{{$nalog->pos->posname}}</td>
            <td>

                <a class="btn btn-small btn-info" title="kreiraj nalog" href="{{ URL::to('slucaj/'.$nalog->id.'/edit') }}"><i class="glyphicon glyphicon-export"></i></a>

            </td>
        </tr>
    @endforeach

</tbody>
</table>


	
@stop