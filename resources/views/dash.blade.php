@extends('layouts.shell')

@section('container')


<!-- if there are creation errors, they will show here -->
{!! HTML::ul($errors->all(),array('class'=>'list-unstyled alert alert-danger', 'role'=>'alert')) !!}


<!-- will be used to show any messages -->
@if (Session::has('message'))
    <div class="alert alert-info">{{ Session::get('message') }}</div>
@endif


<h1>Dashboard</h1>
    <hr />
    

<?php
$timezone = date_default_timezone_get();
echo "The current server timezone is: " . $timezone;
echo "<br />".date('m/d/Y h:i:s a', time());
$mytime = Carbon\Carbon::now();
echo "<br />".$mytime->toDateTimeString();
?>


<!--

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




	
@stop