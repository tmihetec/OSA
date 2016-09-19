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

<h2>Tele2 nalozi na čekanju</h2>
<p class="notice">Nalozi pristigli putem Tele2 web servisa (još nisu zaprimljeni u servisu)</p>
<hr />
<table class="tmkdt tmkdt-t2 table table-striped table-bordered table-hover nowrap" cellspacing="0" width="100%">   
    <thead>
        <tr>
            <th>T2 CASEID</th>
            <th>ZAPRIMLJEN</th>
            <th>NALOG PREUZET</th>
            <th>UREĐAJ</th>
            <th>IMEI</th>
            <th>POS</th>
            <th>PRIORITY</th>
            <th></th>
        </tr>
    </thead>

    <tbody>

    @foreach($t2nalozi as $t2nalog)
        <tr>
            <td>{{$t2nalog->caseId}}</td>
            <td>{{$t2nalog->repairorder_receiveddate}}</td>
            <td>{{$t2nalog->created_at}}</td>
            <td>{{$t2nalog->device_brand." - ".$t2nalog->device_model}}</td>
            <td>{{$t2nalog->device_imei}}</td>
            <td>{{"(".$t2nalog->pos_id.") ".$t2nalog->pos_name}}</td>
            <td>{{$t2nalog->repairorder_priority}}</td>
            <td>

                <a class="btn btn-small btn-info" title="kreiraj nalog" href="{{ URL::to('slucaj/createFromSoap/'.$t2nalog->id) }}"><i class="glyphicon glyphicon-arrow-up"></i></a>

                @if ($adminUser)
                <a class="btn btn-small btn-warning confirmReject" title="odbaci nalog" data-delete="{{csrf_token()}}" data-myhref="{{ URL::to('slucaj/rejectSoapCase/'.$t2nalog->id) }}"><i class="glyphicon glyphicon-remove"></i></a>
                @endif

            </td>
        </tr>
    @endforeach

</tbody>
</table>


	
@stop