@extends('layouts.shell')

@section('container')


        <!-- if there are creation errors, they will show here -->
<?php
/*
	{!! HTML::ul($errors->all(),array('class'=>'list-unstyled alert alert-danger', 'role'=>'alert')) !!}
*/
?>

        <!-- will be used to show any messages -->
@if (Session::has('message'))
    <div id="topmsgs" class="alert alert-info">{!! Session::get('message') !!}</div>
    @endif

            <!-- errors -->
    @if(!empty($errors->all()))
        <ul class='list-unstyled alert alert-danger' role='alert'>
            @foreach ($errors->all() as $msg)
                {!!"<li>".$msg."</li>"!!}
            @endforeach
        </ul>
        @endif


                <!-- NASLOV i OTVARANJE FORME >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> -->
        @if (isset($editingID))

            <h1 style="margin-bottom:5px;">Radni nalog: {{ $slucaj->stsrepairorderno}}

                <div class="pull-right text-right">
                    <!-- LOGOI PRINCIPALA >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> -->
                    @if ((!isset($editingID) && !is_null($t2s)) || (isset($editingID)))
                        @if ($slucaj->pos->principal->id==2)
                            <img src="{{asset('inc/img/t2logo.png')}}" class="img-responsive" style="max-width:110px"
                                 title="tele2nalog"/>
                        @elseif ($slucaj->pos->principal->id==1)
                            <img src="{{asset('inc/img/h18logo.png')}}" class="img-responsive" style="max-width:110px"
                                 title="hangar18 nalog"/>
                        @elseif ($slucaj->pos->principal->id==134)
                            <img src="{{asset('inc/img/vacomlogo.png')}}" class="img-responsive" style="max-width:110px"
                                 title="vacom nalog"/>
                            @endif
                            @endif


                                    <!-- RECIDIVISTI >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> -->
                            @if (isset($editingID) && count($exservisi)>1)
                                <a class="label label-<?php echo (count($exservisi) == 2) ? "warning" : "danger"; ?>"
                                   data-toggle="modal" data-target="#exservisiModal" style="margin-right:2px;">
                                    <small style="color:#fff !important; font-weight: bold;"><span
                                                style="font-size:10px; ">imei ili sn </span>{{count($exservisi)}}. PUT U
                                        BAZI
                                    </small>
                                </a>
                            @endif
                </div>

            </h1>
            <p style="color:#777; font-size:1.3rem; margin-bottom:30px;">{{$modeli_brandovi[$slucaj->devicemodel_id]}}
                [{{$slucaj->deviceincomingimei}}]</p>

            @if($slucaj->trashed())
                <div class="alert alert-danger">OVAJ NALOG JE IZBRISAN!</div>
            @endif



            {!! Form::model($slucaj, ['route' => array('slucaj.update', $slucaj->id), 'method' => 'put', 'id'=>'caseForm']) !!}
            {!! Form::hidden('caseID',$slucaj->id, array('id'=>'caseID')) !!}
        @else

            {!! Form::model($slucaj, array('url' => 'slucaj', 'id'=>'caseForm')) !!}

            <h1 style="margin-bottom:30px;">Novi radni nalog

                @if(!is_null($t2s))
                    TELE2 <input type="hidden" name="wsTele2id" value="{{$t2s['id']}}"/>
                @endif

                za
                {!! Form::select('stsservicelocation_id', $servicelocations, $servicepersonlocation, array('class' => '', 'style'=>'display: inline-block; color:#999; padding-left:3px; margin-left:7px; font-size:.8em')) !!}

            </h1>

            @endif


                    <!-- TAB NAVIGACIJA >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> -->
            <ul class="nav nav-tabs" role="tablist" style="margin-bottom:10px">

                <li role="presentation" class="{{ $activeUlaz}}"
                ><a href="#ulazniDetalji" aria-controls="ulazniDetalji" role="tab" data-toggle="tab">Zaprimanje</a></li>

                @if ($showServis)
                    <li role="presentation" class="{{$activeServis}}"><a href="#servisniDetalji"
                                                                         aria-controls="servisniDetalji" role="tab"
                                                                         data-toggle="tab">Servis</a></li>
                @endif

                @if ($showOtprema)
                    <li role="presentation" class="{{$activeIzlaz}}"><a href="#izlazniDetalji"
                                                                        aria-controls="izlazniDetalji" role="tab"
                                                                        data-toggle="tab">Otprema</a></li>
                @endif

            </ul>

            <!-- TAB PANES >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> -->
            <div class="tab-content">

                        <!-- TAB PANE 1 ============== -->
                        <div role="tabpanel" class="tab-pane {{$activeUlaz}}" id="ulazniDetalji">

                            <div class="row">
                                <div class="col-sm-6">

                                    <!-- ovisno o roli piše gdje se dostavlja -->
                                    <!-- admin bi trebao moći birati između SVIH (select2) POS_ID -->


                                    <!--
                                        ako je SPP, onda nema "ZAPRIMANJE"
                                    -->
                                    <div class="panel panel-info">

                                        <!-- SPP NOVI ILI STARI NALOG-->
                                        @if($SPPuser)
                                            <div class="panel-heading"> UREĐAJ DOSTAVLJEN NA SPP
                                                <strong>{{$pickupposname}}</strong>
                                                <input type="hidden" name="pickuppos_id"
                                                       value="{{$pickuppos_id}}"/>
                                            </div>
                                                <!-- NIJE SPP -->
                                        @else
                                            <!-- STARI NALOG U SERVISU-->
                                            @if (isset($editingID))

                                                <div class="panel-heading"> UREĐAJ DOSTAVLJEN SA SPP
                                                    <strong>{{$pickupposname}}</strong>
                                                    <input type="hidden" name="pickuppos_id"
                                                           value="{{$pickuppos_id}}"/>
                                                </div>

                                            <!-- NOVI NALOG U SERVISU? -->
                                            @else
                                                <div class="panel-heading"> UREĐAJ DOSTAVLJEN NA
                                                    <strong>{{$servicepersonlocationname}}</strong>
                                                    <input type="hidden" name="pickuppos_id"
                                                           value="{{$pickuppos_id}}"/>
                                                </div>

                                            @endif

                                        @endif

                                        <div class="panel-body color1" style="padding-bottom:15px">
                                            <div class="col-sm-6 form-group">


                                                {!! Form::label('devicerecievetype_id','Vrsta dopreme') !!}
                                                @if($SPPuser)
                                                    <input title="Vrsta dopreme" type="text" name="fake"
                                                           value=""
                                                           class="form-control" disabled="disabled"/>
                                                    {!! Form::hidden('devicerecievetype_id', 0, array('id'=>'devicerecievetype_id')) !!}
                                                @else
                                                    {!! Form::select('devicerecievetype_id', $devicerecievetypes, null, array('placeholder'=>'Odaberi', 'class' => 'form-control', 'id'=>'devicerecievetype_id')) !!}
                                                @endif
                                            </div>
                                            <div class="col-sm-6 form-group">
                                                {!! Form::label('devicerecievedate','Datum dopreme') !!}
                                                <?php
                                                if (Request::old('devicerecievedate')!==null) {
                                                $td=Request::old('devicerecievedate');
                                                } else if (!isset($editingID)) {
                                                $td=date("d.m.Y");
                                                } else {
                                                $td = ($slucaj->devicerecievedate->year < 1 ) ? date("d.m.Y") : $slucaj->devicerecievedate->format("d.m.Y");
                                                }

                                                ?>
                                                @if($SPPuser)

                                                    {!! Form::text('devicerecievedate_fake', null, array('disabled'=>'disabled', 'class' => 'form-control')) !!}
                                                    {!! Form::hidden('devicerecievedate', date("d.m.Y"), array('class' => 'form-control dateinput')) !!}
                                                @else
                                                    {!! Form::text('devicerecievedate', $td, array('class' => 'form-control dateinput')) !!}
                                                @endif
                                            </div>
                                            <div class="col-sm-12 form-group" style="margin-top:5px">
                                                {!! Form::text('devicerecieveother', $devicerecieveother, array('disabled'=>'disabled', 'id'=>'devicerecieveother', 'class'=>'form-control')) !!}
                                            </div>
                                        </div>
                                    </div> <!-- panel -->

                                    <div class="panel panel-info">
                                        <div class="panel-heading"> UREĐAJ</div>
                                            <div class="panel-body color1">
                                                <div class="row">

                                                        <!-- DEVICE INFO ====================================== -->
                                                        <div class="clearfix">

                                                            <div class="col-sm-9 form-group">
                                                                {!! Form::label('devicemodel_id', 'Tip - Brand - Model') !!}
                                                                {!! Form::select('devicemodel_id', array(""=>"")+$modeli_brandovi, null, array('class' => 'tmk-sing-select2 form-control')) !!}

                                                                        <!-- t2s label start -->
                                                                @if(!empty($t2s['device_type']))<span
                                                                        class="t2sl">{{$t2s['device_type']}}</span> @endif
                                                                @if(!empty($t2s['device_brand']))<span
                                                                        class="t2sl">{{$t2s['device_brand']}}</span> @endif
                                                                @if(!empty($t2s['device_model']))<span
                                                                        class="t2sl">{{$t2s['device_model']}}</span>
                                                                @endif
                                                                        <!-- t2s label end -->

                                                            </div>

                                                            <div class="col-sm-3 form-group">
                                                                {!! Form::label('devicetcode', 'T-CODE') !!}
                                                                {!! Form::text('devicetcode', null, array('class' => 'form-control')) !!}
                                                            </div>

                                                        </div>

                                                        <div class="clearfix">
                                                            <div class="col-sm-5 form-group">
                                                                {!! Form::label('deviceincomingimei', 'IMEI 1 / SN') !!}
                                                                @if (!isset($editingID))
                                                                    <small>[<a title="upiši 15 '0'"
                                                                               id="petnajstnula"><span class="crveno">15x0</span></a>]
                                                                    </small>
                                                                    <a class="label label-warning pull-right"
                                                                       id="checkimeiubazi"><i
                                                                                class="glyphicon glyphicon-question-sign"></i>
                                                                        PROVJERI</a>
                                                                @endif
                                                                {!! Form::text('deviceincomingimei', null, array('class' => 'form-control', 'id'=>'deviceincomingimei')) !!}
                                                            </div>
                                                            <div class="col-sm-4 form-group">
                                                                {!! Form::label('devicemanufactureddate', 'Datum proizvodnje') !!}
                                                                <?php
                                                                $td = (empty($slucaj->devicemanufactureddate) ) ? null : $slucaj->devicemanufactureddate->format("m/Y");
                                                                ?>
                                                                {!! Form::text('devicemanufactureddate', $td, array('class' => 'form-control dateinputshort')) !!}
                                                            </div>
                                                            <div class="col-sm-3 form-group">
                                                                {!! Form::label('devicepostpaid', 'Pre/Post paid') !!}
                                                                {!! Form::select('devicepostpaid', array('null'=>'/', 'postpaid' => 'Postpaid', 'prepaid' => 'Prepaid'), null, array('class' => 'form-control')) !!}
                                                            </div>
                                                        </div>

                                                        <div class="clearfix">
                                                            <div class="col-sm-12 form-group">
                                                                {!! Form::label('devicefailuredescription', 'Opis neispravnosti') !!}
                                                                {!! Form::textarea('devicefailuredescription', null, array('class' => 'form-control', 'rows'=>'3')) !!}
                                                            </div>
                                                        </div>

                                                        <div class="clearfix">
                                                            <div class="col-sm-12 form-group">


                                                                {!! Form::label('ms_customersypmtoms', 'Simptomi (prijava korisnika)') !!}
                                                                {!! Form::select('customersymptoms[]',$customersymptoms,

                                                                    (isset($editingID))
                                                                        ? $slucaj->customersymptoms->lists('id')->toArray()
                                                                        : 	(
                                                                            (isset($slucaj['damage']) && is_array($slucaj['damage']))
                                                                            ? $slucaj['damage']
                                                                            : array()
                                                                            )

                                                                    , array('multiple'=>'true', 'class' => 'form-control', 'id'=>'ms_customersypmtoms')) !!}

                                                            </div>
                                                        </div>

                                                        <div class="clearfix">
                                                            <div class="col-sm-4 form-group">
                                                                {!! Form::label('deviceinvoiceno', 'Broj računa') !!}
                                                                {!! Form::text('deviceinvoiceno', null, array('class' => 'form-control')) !!}
                                                            </div>
                                                            <div class="col-sm-4 form-group">
                                                                {!! Form::label('devicebuydate', 'Datum računa') !!}
                                                                <?php
                                                                $td = (empty($slucaj->devicebuydate) ) ? null : $slucaj->devicebuydate->format("d.m.Y");
                                                                ?>
                                                                {!! Form::text('devicebuydate', $td, array('class' => 'dateinput form-control')) !!}
                                                            </div>
                                                            <div class="col-sm-4 form-group">

                                                                {!! Form::label('devicewarrantycardno', 'Broj jamstvenog lista') !!}
                                                                {!! Form::text('devicewarrantycardno', null, array('class' => 'form-control')) !!}
                                                            </div>
                                                        </div>

                                                        <div class="clearfix">
                                                            <div class="col-sm-12 form-group">
                                                                {!! Form::label('deviceotherbuyplace', 'Mjesto kupnje uređaja')!!}
                                                                <?php
                                                                $tc=true;
                                                                $tap=array('disabled'=>'disabled','class' => 'form-control', 'id'=>'deviceotherbuyplace');
                                                                // ak slučajno ima u sessionu
                                                                if (
                                                                old('deviceotherbuyplace')!==null
                                                                &&
                                                                old('checkotherbuyplace')==null
                                                                ) {
                                                                $tc=false;
                                                                $tap=array('class' => 'form-control', 'id'=>'deviceotherbuyplace');
                                                                }
                                                                // ako je edit
                                                                if ( isset($editingID) ) {

                                                                // ako nema niš kao otherbuyplace
                                                                if (
                                                                trim($slucaj->deviceotherbuyplace) ==true
                                                                || $slucaj->deviceotherbuyplace !== null
                                                                ) {

                                                                $tc=false;
                                                                $tap=array('class' => 'form-control', 'id'=>'deviceotherbuyplace');

                                                                }

                                                                }
                                                                ?>
                                                                <div class="pull-right">
                                                                    {!! Form::checkbox('checkotherbuyplace', '1', $tc,array('id'=>'checkotherbuyplace','class'=>'field'))!!}
                                                                    {!! Form::label('checkotherbuyplace', 'Kod komitenta') !!}
                                                                </div>
                                                                {!! Form::text('deviceotherbuyplace', null, $tap) !!}
                                                            </div>
                                                        </div>

                                                        <div class="clearfix">
                                                            <div class="col-xs-6 col-sm-3 form-group">
                                                                {!! Form::checkbox('deviceaccbattery', '1', (isset($slucaj->deviceaccbattery) ? $slucaj->deviceaccbattery : false),['class' => 'field']) !!}
                                                                {!! Form::label('deviceaccbattery', 'Baterija') !!}
                                                                <br/>
                                                                {!! Form::checkbox('deviceacccharger', '1', (isset($slucaj->deviceacccharger) ? $slucaj->deviceacccharger : false),['class' => 'field']) !!}
                                                                {!! Form::label('deviceacccharger', 'Punjač') !!}
                                                                <br/>
                                                                {!! Form::checkbox('deviceaccantenna', '1', (isset($slucaj->deviceaccantenna) ? $slucaj->deviceaccantenna : false),['class' => 'field']) !!}
                                                                {!! Form::label('deviceaccantenna', 'Antena') !!}
                                                                <br/>
                                                                {!! Form::checkbox('deviceaccsim', '1', (isset($slucaj->deviceaccsim) ? $slucaj->deviceaccsim : false),['class' => 'field']) !!}
                                                                {!! Form::label('deviceaccsim', 'SIM') !!}

                                                            </div>
                                                            <div class="col-xs-6 col-sm-3 form-group">
                                                                {!! Form::checkbox('deviceaccusbcable', '1', (isset($slucaj->deviceaccusbcable) ? $slucaj->deviceaccusbcable : false),['class' => 'field']) !!}
                                                                {!! Form::label('deviceaccusbcable', 'USB kabel') !!}
                                                                <br/>
                                                                {!! Form::checkbox('deviceaccmemorycard', '1', (isset($slucaj->deviceaccmemorycard) ? $slucaj->deviceaccmemorycard : false),['class' => 'field']) !!}
                                                                {!! Form::label('deviceaccmemorycard', 'Mem. kartica') !!}
                                                                <br/>
                                                                {!! Form::checkbox('deviceaccheadphones', '1', (isset($slucaj->deviceaccheadphones) ? $slucaj->deviceaccheadphones : false),['class' => 'field']) !!}
                                                                {!! Form::label('deviceaccheadphones', 'Slušalice') !!}
                                                            </div>
                                                            <div class="col-sm-6 form-group">
                                                                {!! Form::label('deviceaccrest', 'Ostali pribor') !!}
                                                                {!! Form::textarea('deviceaccrest', null, array('rows'=>3,'class' => 'form-control')) !!}
                                                            </div>
                                                        </div>

                                                    </div><!-- prva row-->
                                                </div><!-- prva panel-body -->
                                            </div><!-- prva panel -->

                                </div><!-- prva kolona -->

                                <div class="col-sm-6">

                                    <div class="panel panel-info">
                                            <div class="panel-heading"> KRAJNJI KORISNIK</div>
                                            <div class="panel-body color1">
                                                <div class="row">
                                                    <!-- KORISNIK INFO ====================================== -->
                                                    <div class="clearfix">
                                                        <div class="col-sm-6 form-group">
                                                            {!! Form::label('customername', 'Naziv (ime/tvrtka)') !!}
                                                            {!! Form::text('customername', null, array('class' => 'form-control')) !!}
                                                        </div>
                                                        <div class="col-sm-6 form-group">
                                                            {!! Form::label('customerlastname', 'Naziv2 (prezime/oib)') !!}
                                                            {!! Form::text('customerlastname', null, array('class' => 'form-control')) !!}
                                                        </div>
                                                    </div>

                                                    <div class="clearfix">
                                                        <!-- TODO: country? -->
                                                        <div class="col-sm-12 form-group">
                                                            {!! Form::label('customerstreet', 'Adresa') !!}
                                                            {!! Form::text('customerstreet', null, array('class' => 'form-control')) !!}
                                                        </div>
                                                        <div class="col-sm-12 form-group">
                                                            {!! Form::label('customerplace_id', 'Država - Grad') !!}
                                                            {!! Form::select(	'customerplace_id',
                                                                $gradovi,
                                                                null,
                                                                array('class' => 'tmk-sing-nc-select2 form-control')
                                                                )
                                                                !!}
                                                                    <!-- t2s label start -->
                                                            @if(!empty($t2s['contact_address_postcode']))<span
                                                                    class="t2sl">{{$t2s['contact_address_postcode']}} </span> @endif
                                                            @if(!empty($t2s['contact_address_place']))<span
                                                                    class="t2sl"> {{$t2s['contact_address_place']}} </span> @endif
                                                            @if(!empty($t2s['contact_address_country']))<span
                                                                    class="t2sl"> {{$t2s['contact_address_country']}} </span>
                                                            @endif
                                                                    <!-- t2s label end -->
                                                        </div>
                                                    </div>

                                                    <div class="clearfix">
                                                        <div class="col-sm-4 form-group">
                                                            {!! Form::label('customerphone1', 'Telefon 1') !!}
                                                            {!! Form::text('customerphone1', null, array('class' => 'form-control')) !!}
                                                        </div>
                                                        <div class="col-sm-4 form-group">
                                                            {!! Form::label('customerphone2', 'Telefon 2') !!}
                                                            {!! Form::text('customerphone2', null, array('class' => 'form-control')) !!}
                                                        </div>
                                                        <div class="col-sm-4 form-group">
                                                            {!! Form::label('customeremail', 'E-mail') !!}
                                                            [<a target="_blank" data-poljeadrese="customeremail"
                                                                class="sendMailLink">pošalji</a>]
                                                            {!! Form::text('customeremail', null, array('id'=>'customeremail', 'class' => 'form-control')) !!}
                                                        </div>
                                                    </div>


                                                </div><!-- druga row-->
                                            </div><!-- druga panel-body -->
                                        </div><!-- druga panel -->

                                    <div class="panel panel-info">
                                            <div class="panel-heading"> KOMITENT (Tko prijavljuje kvar?)</div>
                                            <div class="panel-body color1">
                                                <div class="row">

                                                    <!-- POS INFO ====================================== -->
                                                    <div class="clearfix">
                                                        <div class="col-sm-8 form-group">
                                                            {!! Form::label('pos_id', 'Komitent - Prodajno mjesto') !!}
                                                            @if($disabledFields['pos_id'])
                                                                {!! Form::text('pos_id_text', $posevi[$slucaj['pos_id']], array('class' => '  form-control', 'readonly')) !!}
                                                                {!! Form::hidden('pos_id', $slucaj['pos_id'], array('class' => '  form-control', 'readonly')) !!}
                                                            @else
                                                                {!! Form::select('pos_id', [""=>""]+$posevi, null, array('class' => ' tmk-sing-nc-select2 form-control')) !!}
                                                            @endif
                                                        </div>
                                                        <div class="col-sm-4 form-group">
                                                            {!! Form::label('posclaimdate', 'Datum zaprimanja') !!}
                                                            <?php
                                                            //dd($slucaj);
                                                            $td = (empty($slucaj->posclaimdate) ) ? null : $slucaj->posclaimdate->format("d.m.Y");
                                                            //$td=$slucaj['posclaimdate'];
                                                            //$td="";

                                                            ?>
                                                            {!! Form::text('posclaimdate', $td, array('class' => 'form-control dateinput')) !!}
                                                        </div>
                                                    </div>

                                                    <div class="clearfix">
                                                        <div class="col-sm-12 form-group">
                                                            {!! Form::label('posmessage', 'Napomena') !!}
                                                            {!! Form::textarea('posmessage', null, array('class' => 'form-control', 'rows'=>'2')) !!}
                                                        </div>
                                                    </div>

                                                    <hr style="margin:0 0 8px 0;"/>

                                                    <div class="clearfix">
                                                        <div class="col-sm-4 form-group">
                                                            {!! Form::label('posrepairorderno', 'POS radni nalog') !!}
                                                            @if($disabledFields['posrepairorderno'])
                                                                {!! Form::text('posrepairorderno', null, array('class' => 'form-control')+['readOnly']) !!}
                                                            @else
                                                                {!! Form::text('posrepairorderno', null, array('class' => 'form-control')) !!}
                                                            @endif
                                                        </div>
                                                        <div class="col-sm-6 form-group">
                                                            {!! Form::label('posclaimtype_id', 'Vrsta reklamacije') !!}
                                                            {!! Form::select('posclaimtype_id', $claimtypes, null, array('class' => 'form-control tmk-sing-nc-select2'),1) !!}
                                                        </div>
                                                        <div class="col-sm-2 form-group">
                                                            {!! Form::label('pospriority', 'Prioritet') !!}
                                                            {!! Form::text('pospriority', null, array('class' => 'form-control')) !!}
                                                        </div>
                                                    </div>


                                                </div><!-- druga row2-->
                                            </div><!-- druga panel2-body -->
                                        </div><!-- druga panel2 -->

                                    <div class="panel panel-info">
                                            <div class="panel-heading"> OTPREMA (Kako se uređaj otprema nakon
                                                servisa?)
                                            </div>
                                            <div class="panel-body color1">
                                                <div class="col-sm-12 form-group">

                                                    {!! Form::select('posdevicereturntype_id', $posdevicereturntypes, null, array('placeholder'=>'Odaberi', 'class' => 'form-control tmk-sing-nc-select2', 'id'=>'posdevicereturntype_id')) !!}
                                                </div>
                                                <div class="col-sm-12 form-group" style="margin-bottom:12px">
                                                    {!! Form::text('posdevicereturnother', $posdevicereturnother, array('disabled'=>'disabled', 'id'=>'posdevicereturnother', 'class'=>'form-control')) !!}
                                                </div>
                                            </div>
                                        </div>


                                </div><!-- druga kolona -->


                            </div><!-- row -->

                        </div>    <!-- TAB PANE 1 ============== -->



                        @if (isset($editingID))

                        <!-- TAB PANE 2 ============== -->
                        <div role="tabpanel" class="tab-pane {{$activeServis}}" id="servisniDetalji">


                            <!-- DETALJI SERVISA ========================================================================================== -->
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="panel panel-primary color2">
                                        <div class="panel-heading">
                                            STS <span class="pull-right">korisnik: {{$servicepersonuser}}</span>
                                        </div>
                                        <div class="panel-body color2">

                                            <!-- broj, datum i status naloga -->
                                            <div class="row">
                                                <?php /*
											<div class="col-sm-2 form-group">
												{!! Form::label('stsrepairorderno', 'Broj radnog naloga') !!}
												{!! Form::text('fake', $slucaj->stsrepairorderno, array('class' => 'form-control', 'disabled'=>'disabled')) !!}
												{!! Form::hidden('stsrepairorderno', $slucaj->stsrepairorderno, array('class' => 'form-control', )) !!}
											</div>
										*/ ?>
                                                {!! Form::hidden('stsrepairorderno', $slucaj->stsrepairorderno) !!}


                                                <div class="col-sm-2 form-group">
                                                    {!! Form::label('stsroopendate', 'Datum otvaranja') !!}
                                                    <?php
                                                    /*
                                                        ako je editing, onda treba biti disbled.
                                                        ako bu trebalo onda treba ovisno o ROLE datu da se to promijeni,
                                                        pošto je forma "model-based" onda se nesmije staviti dole explicitna vrijednost
                                                        jer je redosljed dodjeljivanja
                                                            - SESSION (OLD)
                                                            - EXPLICIT DATA
                                                            - MODEL DATA
                                                        pa bi stalno stavljao taj explicit. (treba dakle staviti neku logiku prije)
                                                    */
                                                    $tro='readonly';
                                                    $td = (empty($slucaj->stsroopendate) ) ? null : $slucaj->stsroopendate->format("d.m.Y");
                                                    ?>
                                                    {!! Form::text('stsroopendate', $td, array('class' => 'form-control', 'readonly'=>$tro)) !!}
                                                </div>
                                                <?php /*
											<div class="col-sm-2 form-group">
												{!! Form::label('stsservicelocation_id', 'Lokacija') !!}
												{!! Form::select('stsservicelocation_id', $servicelocations, null, array('class' => 'form-control tight')) !!}
											</div>
											*/?>


                                                <div class="col-sm-1 form-group">
                                                    {!! Form::label('stsservicelevel_id', 'Nivo') !!}
                                                    {!! Form::select('stsservicelevel_id', $servicelevels, null, array('class' => 'form-control tight')) !!}
                                                </div>

                                            </div>
                                            <!-- end broj, datum i status naloga -->

                                            <div class="row">

                                                <div class="col-sm-2 form-group">
                                                    {!! Form::checkbox('devicewarranty', '1', (isset($slucaj->devicewarranty) ? $slucaj->devicewarranty : false),['class' => 'field']) !!}
                                                    {!! Form::label('devicewarranty', 'Jamstvo?') !!}
                                                </div>
                                                <div class="col-sm-2 form-group">
                                                    {!! Form::checkbox('stsmbswap', '1', (isset($slucaj->stsmbswap) ? $slucaj->stsmbswap : false),['class' => 'field']) !!}
                                                    {!! Form::label('stsmbswap', 'zamjena MB?') !!}
                                                </div>
                                                <div class="col-sm-2 form-group">
                                                    {!! Form::checkbox('stsfailuredetected', '1', (isset($slucaj->stsfailuredetected) ? $slucaj->stsfailuredetected : false),['class' => 'field']) !!}
                                                    {!! Form::label('stsfailuredetected', 'Greška nađena?') !!}
                                                </div>
                                                <div class="col-sm-1 form-group">
                                                    {!! Form::checkbox('stsqc', '1', (isset($slucaj->stsqc) ? $slucaj->stsqc : false),['class' => 'field']) !!}
                                                    {!! Form::label('stsqc', 'QC?') !!}
                                                </div>
                                                <div class="col-sm-2 form-group">

                                                    <?php
                                                    $swapreadonly=null;

                                                    /* - isključeno 24.01.2016, Štef, SKYPE, tmkjs.js & validation u repairordercontrolleru + create view

                                                    $swapreadonly = 'readonly';
                                                    if (isset($slucaj) && $slucaj->stsdeviceswap==1) {
                                                        $swapreadonly=null;
                                                    }
                                                    */
                                                    ?>

                                                    {!! Form::checkbox('stsdeviceswap', '1', (isset($slucaj->stsdeviceswap) ? $slucaj->stsdeviceswap : false),['id'=>'stsdeviceswap', 'class' => 'field']) !!}
                                                    {!! Form::label('stsdeviceswap', 'Zamjena uređaja? ') !!}
                                                </div>
                                                <div class="col-sm-3 form-group">
                                                    @if (isset($slucaj->stsdoadap) && $slucaj->stsdoadap == 1)
                                                    {!! Form::checkbox('stsdoadap', '1', true, ['class' => '']) !!}
                                                    @else
                                                    {!! Form::checkbox('stsdoadap', '1', false, ['class' => '']) !!}
                                                    @endif
                                                    {!! Form::label('stsdoadap', 'DOA/DAP?') !!}
                                                            <!-- ako je tražen doadap reci -->
                                                    @if($slucaj->posclaimtype_id == $ct_DOA)
                                                        <span class="t2sl"><small>DOA tražen</small></span>
                                                    @elseif ($slucaj->posclaimtype_id == $ct_DAP)
                                                        <span class="t2sl"><small>DAP tražen</small></span>
                                                    @else <span><small>nije tražen</small></span>
                                                    @endif
                                                </div>

                                            </div>

                                            <div class="row">

                                                <div class="col-sm-2 form-group">
                                                    {!! Form::label('deviceincomingsasref', 'Dolazni SAS') !!}
                                                    {!! Form::text('deviceincomingsasref', null, array('class' => 'form-control')) !!}
                                                </div>

                                                <div class="col-sm-2 form-group">
                                                    {!! Form::label('deviceincomingswversion', 'Dolazni SW') !!}
                                                    {!! Form::text('deviceincomingswversion', null, array('class' => 'form-control')) !!}
                                                </div>

                                                <div class="col-sm-3 form-group">
                                                    {!! Form::label('deviceoutgoingimei', 'Izlazni IMEI / SN') !!}
                                                    {!! Form::text('deviceoutgoingimei', null, array('readonly'=>$swapreadonly, 'class' => 'tmk_swap form-control')) !!}
                                                </div>
                                                <div class="col-sm-3 form-group">
                                                    {!! Form::label('deviceoutgoingsasref', 'Izlazna SAS ref.') !!}
                                                    {!! Form::text('deviceoutgoingsasref', null, array('readonly'=>$swapreadonly, 'class' => 'tmk_swap form-control')) !!}
                                                </div>
                                                <div class="col-sm-2 form-group">
                                                    {!! Form::label('deviceoutgoingswversion', 'Izlazni SW') !!}
                                                    {!! Form::text('deviceoutgoingswversion', null, array('class' => 'form-control')) !!}
                                                </div>
                                            </div>

                                            <!-- tech symp, faultyelements, log, description -->
                                            <div class="row">

                                                <!-- tech symp, faultyelements -->
                                                <div class="col-sm-6 ">

                                                    <div class="form-group">
                                                        <?php

                                                        $glavni=null;
                                                        $ostali=null;
                                                        $disabled=true;

                                                        /* EDIT */

                                                        if (isset($slucaj) && !($slucaj->techniciansymptoms->isEmpty())){

                                                        foreach($slucaj->techniciansymptoms as $ts) {
                                                        // prvi je glavni (tak je sortirano u modelu)
                                                        if (is_null($glavni)) {
                                                        $glavni=$ts->id;
                                                        $disabled=null;
                                                        } else $ostali[]=$ts->id;
                                                        }
                                                        }
                                                        ?>

                                                                <!-- napomene sa zaprimanja -->
                                                        <div class="well well-sm">
                                                            <p class="notice"><strong>Prijavljen
                                                                    nedostatak:</strong> {{$slucaj->devicefailuredescription}}
                                                            </p>
                                                            <p class="notice"><strong>Napomena:</strong>
                                                                @if (stristr($slucaj->posmessage, '|| Nepostojeći POS', true)=="")
                                                                    {{$slucaj->posmessage}}
                                                                @else
                                                                    {{stristr($slucaj->posmessage, '|| Nepostojeći POS', true)}}
                                                                @endif
                                                            </p>
                                                        </div>


                                                        {!! Form::label('techniciansymptom_id', 'Simptomi (prijava servisera)') !!}
                                                        {!! Form::select('techniciansymptom_id',array('' => '')+$techniciansymptoms, $glavni, array('class' => 'tmk-sing-select2 form-control')) !!}
                                                        <hr class="short"/>
                                                        {!! Form::select('techniciansymptomothers[]',$techniciansymptoms, $ostali, array('multiple'=>'true', 'class' => 'tmk-sing-select2 form-control', 'disabled'=>$disabled, 'id'=>'techniciansymptomothers')) !!}

                                                    </div>
                                                    <div class="form-group">

                                                        {!! Form::label('ms_faultyelements', 'Neispravni dijelovi') !!}
                                                        {!! Form::select('faultyelements[]',$faultyelements, (isset($slucaj)) ? $slucaj->faultyelements->lists('id') : array(), array('multiple'=>'true', 'class' => 'form-control','id'=>'ms_faultyelements')) !!}

                                                    </div>
                                                </div>


                                                <!--VRSTA REKLAMACIJE (STS UNOS) i opis zahvata -->
                                                <div class="col-sm-6">

                                                    <div class="form-group">
                                                        <label for='stsclaimtype_id'>STS Vrsta reklamacije
                                                            <small>(SPP je tražio
                                                                "{{$claimtypes[$slucaj->posclaimtype_id]}}")
                                                            </small>
                                                        </label>
                                                        {!! Form::select('stsclaimtype_id', $claimtypes, null, array('class' => 'form-control tmk-sing-nc-select2'),1) !!}
                                                    </div>


                                                    <div class="form-group">
                                                        {!! Form::label('stsnotice', 'Finalni opis zahvata') !!}

                                                                <!--[<a href="">Generiraj iz dnevnika</a>]-->
                                                        {!! Form::textarea('stsnotice', null, array('class' => 'form-control', 'rows'=>'4', 'id'=>'stsnotice')) !!}

                                                    </div>

                                                    <div class="input-group">
                                                        {!! Form::select('disclaimers',$disclaimers,null,array('class'=>'form-control', 'id'=>'disclaimerSelect'))!!}
                                                        <span class="input-group-btn">
	 												<button class="btn" id="disclaimerButton">Dodaj izjavu</button>
													</span>
                                                    </div>

                                                </div>
                                                <!-- end tech symp, faultyelements, log, description -->
                                            </div>

                                            <!-- rezervni i usluge -->
                                            <div class="row">

                                                <!-- rezervni dijelovi -->
                                                <div class="col-sm-6">

                                                    <label for="sp_parts">Rezervni dijelovi</label>
                                                    <select id="sp_parts" class="form-control" name="sp_parts">
                                                        <option value="">Odaberi</option>
                                                        @foreach ($spareparts as $part)

                                                            @if ($part->stock->isEmpty())
                                                                <option disabled="disabled" value="{{ $part->id }}"
                                                                        data-price="{{ $part->price }}">
                                                                    {{ $part->custom_name }}
                                                                </option>
                                                            @else
                                                                @foreach ($part->stock as $wh)
                                                                    <option value="{{ $part->id }}"
                                                                            data-warehouseid="{{$wh->pivot->spwarehouse_id}}"
                                                                            data-warehouse="{{$wh->code}}"
                                                                            data-name="{{$part->custom_name}}"
                                                                            data-price="{{ $part->price }}">
                                                                        {!! $part->custom_name." - ".$wh->code." [".$wh->pivot->qty."kom]" !!}
                                                                    </option>
                                                                @endforeach
                                                            @endif

                                                        @endforeach
                                                    </select>


                                                    <div id="tmk_spareparts">
                                                        <?php
                                                        //AKO je edit i ima nekaj, onda popuni u tablice veze iz baze!
                                                        if (isset($slucaj) && !($slucaj->spareparts->isEmpty())){
                                                        // TEMPLATE (iz tmkjs.js) početak tablice
                                                        $tmk_sparepartsCount=0;
                                                        echo '<table class="table table-condensed table-bordered"><thead><tr><th>naziv</th><th class="col-md-2">cijena</th><th class="col-md-2">kom</th><th class="col-md-2">skladište</th></tr></thead><tbody>';
                                                        foreach($slucaj->spareparts as $sparepart)    {
                                                        $selid=$sparepart->id;
                                                        $seltxt=$sparepart->name;
                                                        $selprc=$sparepart->pivot->price;
                                                        $selqty=$sparepart->pivot->qty;
                                                        $selwhsid=$sparepart->pivot->spwarehouse_id;
                                                        $selwhs=\App\Models\Spwarehouse::find($selwhsid)->first()->code;

                                                        echo '<tr><td class="vert-align">
														<input id="sppt'.$tmk_sparepartsCount.'ids" type="hidden" name="sppt['.$tmk_sparepartsCount.'][ids]" class= "tmksp_id"  value="'.$selid.'" />
														<button id="sppt'.$tmk_sparepartsCount.'del" class="removespptrow btn btn-default"><i class="glyphicon glyphicon-remove"></i> </button> '.$seltxt.'
														</td><td>
														<input id="sppt'.$tmk_sparepartsCount.'prc" type="text" name="sppt['.$tmk_sparepartsCount.'][prc]" class= "tmksp_price form-control decimalInput"  value="'.$selprc.'" />
														</td><td>
														<input id="sppt'.$tmk_sparepartsCount.'qty" type="text" name="sppt['.$tmk_sparepartsCount.'][qty]" class= "tmksp_qty form-control"  value="'.$selqty.'" />
														</td><td>
														<input id="sppt'.$tmk_sparepartsCount.'whs" type="hidden" name="sppt['.$tmk_sparepartsCount.'][whs]" class= "tmksp_whs form-control"  value="'.$selwhsid.'" />'.$selwhs.'
														</td></tr>';
                                                        $tmk_sparepartsCount++;
                                                        }
                                                        // TEMPLATE (iz tmkjs.js) kraj tablice
                                                        echo '</tbody></table>';
                                                        }
                                                        ?>
                                                    </div>
                                                    <!-- end rezervni dijelovi -->
                                                </div>

                                                <!-- usluge -->
                                                <div class="col-sm-6">

                                                    <label for="stsservices">Usluge</label>
                                                    <select id="stsservices" class="form-control" name="stsservices">
                                                        <option value="">Odaberi</option>
                                                        @foreach ($stsservices as $service)
                                                            <option value="{{ $service->id }}"
                                                                    data-price="{{ $service->price }}"
                                                                    data-jm="{{ $service->jm }}"
                                                                    @if ($service->id == $otherserviceid)
                                                                    data-ro="0"
                                                                    @else
                                                                    data-ro="1"
                                                                    @endif
                                                            >{{ $service->name }}</option>
                                                        @endforeach
                                                    </select>

                                                    <div id="tmk_stsservices">
                                                        <?php
                                                        //AKO je edit i ima nekaj, onda popuni u tablice veze iz baze!
                                                        if (isset($slucaj) && !($slucaj->stsservices->isEmpty())){
                                                        // TEMPLATE (iz tmkjs.js) početak tablice
                                                        $tmk_stsservicesCount=0;
                                                        echo '<table class="table table-condensed table-bordered"><thead><tr><th></th><th>naziv</th><th class="col-md-2">jm</th><th class="col-md-2">cijena</th><th class="col-md-2">qty</th></tr></thead><tbody>';
                                                        foreach($slucaj->stsservices as $service)    {
                                                        $selid=$service->id;
                                                        $seltxt=$service->pivot->savedname;
                                                        $seljm=$service->pivot->savedjm;
                                                        $selprc=$service->pivot->price;
                                                        $selqty=$service->pivot->qty;
                                                        $readonly=($service->id == $otherserviceid) ? false : true;

                                                        echo '<tr><td class="vert-align">
														<input id="stssrv'.$tmk_stsservicesCount.'ids" type="hidden" name="stssrv['.$tmk_stsservicesCount.'][ids]" class= "tmksrv_id"  value="'.$selid.'" />
														<button id="stssrv'.$tmk_stsservicesCount.'del" class="removestssrvrow btn btn-default"><i class="glyphicon glyphicon-remove"></i> </button>
														</td><td>
														<input id="stssrv'.$tmk_stsservicesCount.'nme" type="text" name="stssrv['.$tmk_stsservicesCount.'][nme]" class= "tmksrv_name form-control" ';
                                                        if ($readonly) echo ' readonly="readonly" ';
                                                        echo ' value="'.$seltxt.'" />
														</td><td>
														<input id="stssrv'.$tmk_stsservicesCount.'jm" type="text" name="stssrv['.$tmk_stsservicesCount.'][jm]" class= "tmksrv_jm form-control" ';
                                                        if ($readonly) echo ' readonly="readonly" ';
                                                        echo ' value="'.$seljm.'" />

														'
                                                        .'</td><td>
														<input id="stssrv'.$tmk_stsservicesCount.'prc" type="text" name="stssrv['.$tmk_stsservicesCount.'][prc]" class= "tmksrv_price decimalInput form-control"  value="'.$selprc.'" />
														</td><td>
														<input id="stssrv'.$tmk_stsservicesCount.'qty" type="text" name="stssrv['.$tmk_stsservicesCount.'][qty]" class= "tmksrv_qty decimalInput form-control"  value="'.$selqty.'" />
														</td></tr>';
                                                        $tmk_stsservicesCount++;
                                                        }
                                                        // TEMPLATE (iz tmkjs.js) kraj tablice
                                                        echo '</tbody></table>';
                                                        }
                                                        ?>
                                                    </div>
                                                    <!-- end usluge -->
                                                </div>
                                                <!-- end rezervni i usluge -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- END DETALJI SERVISA ====================================================================================== -->


                        </div> <!-- TAB PANE 2 ============== -->
                        @endif



                        @if ($showOtprema)
                                <!-- TAB PANE 3 ============== -->
                <div role="tabpanel" class="tab-pane {{$activeIzlaz}}" id="izlazniDetalji">

                    <div class="panel panel-info">
                        <div class="panel-heading"> LOGISTIČKE INFORMACIJE</div>
                        <div class="panel-body color1">

                            <div class="row">
                                <div class="col-sm-4">
                                    <dl>
                                        <dt>Stranka donjela uređaj na:</dt>
                                        <dd>{{$pickupposname}}
                                            , {{($slucaj->devicerecievedate->year < 1 ) ? date("d.m.Y") : $slucaj->devicerecievedate->format("d.m.Y")}} </dd>
                                        <dt>Traženo je da se uređaj dostavi:</dt>
                                        <dd>{{(!empty($slucaj->posdevicereturnother)) ? $slucaj->posdevicereturnother : $devicereturntypes[$slucaj->posdevicereturntype_id]}}  </dd>
                                        <dt>Opis neispravnosti:</dt>
                                        <dd>{{$slucaj->devicefailuredescription}}</dd>
                                    </dl>
                                </div>
                                <div class="col-sm-4">
                                    <dl>
                                        <dt>Krajnji korisnik:</dt>
                                        <dd>{!!$slucaj->customername." ".$slucaj->customerlastname."<br />".$slucaj->customerstreet.", ".$gradovi[$slucaj->customerplace_id]."<br />".$slucaj->customerphone1!!}
                                            {!! (!empty($slucaj->customerphone2)) ? " (".$slucaj->customerphone2.")" : ""!!}
                                            {!! (!empty($slucaj->customeremail)) ? "<br />".$slucaj->customeremail : "" !!}
                                        </dd>
                                        <dt>SPP:</dt>
                                        <dd>{!!$posevi[$slucaj['pos_id']]!!} </dd>
                                    </dl>
                                </div>
                                <div class="col-sm-4">
                                    <dl>
                                        <dt>Oprema uz uređaj:</dt>
                                        <dd>
                                            <?php
                                            $tarr=array();
                                            if ($slucaj->deviceaccbattery==1) array_push($tarr,"baterija");
                                            if ($slucaj->deviceacccharger==1) array_push($tarr,"punjač");
                                            if ($slucaj->deviceaccantenna==1) array_push($tarr,"antena");
                                            if ($slucaj->deviceaccusbcable==1) array_push($tarr,"usb kabel");
                                            if ($slucaj->deviceaccmemorycard==1) array_push($tarr,"memorijska kartica");
                                            if ($slucaj->deviceaccheadphones==1) array_push($tarr,"slušalice");
                                            if ($slucaj->deviceaccsim==1) array_push($tarr,"SIM");
                                            $tarr=implode(",",$tarr).", ".$slucaj->deviceaccrest;
                                            ?>
                                            {{$tarr}}
                                        </dd>
                                        <dt>Zadnji servis:</dt>
                                        <dd> {{ $slucaj->serviser->servis_user}}
                                            , {{$slucaj->stsroclosingdate}} </dd>
                                        <dt>Finalni opis zahvata:</dt>
                                        <dd>{{$slucaj->stsnotice}}</dd>
                                    </dl>
                                </div>
                            </div>
                            <hr/>
                            <div class="row">
                                <div class="col-sm-12">
                                    {!! Form::label('devicereturnnotice', 'Napomena otpreme') !!}
                                    {!! Form::textarea('devicereturnnotice', null, array('class' => 'form-control', 'rows'=>'2', 'id'=>'devicereturnnotice')) !!}
                                </div>
                            </div>

                        </div> <!-- panel body -->
                    </div> <!-- panel -->
                </div>

                    @endif

            </div> <!-- TAB PANES ============== -->


            <!-- SERVISER, STATUS i OTPREMA (jednog dana i LOG) ============== -->
            @if(isset($editingID))
                    <div class="panel panel-color3">
                        <div class="panel-heading"> STS STATUS I VRSTA OTPREME</div>
                        <div class="panel-body color3">


                            <div class="row">
                                <div class="col-sm-3 form-group">
                                    <label for='stsserviceperson_id'>Serviser (<span data-toggle="tooltip"
                                                                                     data-placement="top"
                                                                                     title="Zadnji serviser koji je radio"
                                                                                     class="t2sl">{{($slucaj->serviser) ? $slucaj->serviser->servis_user : ""}}</span>)</label>
                                    {!! Form::select('stsserviceperson_id', $activeservicepersonscustom, $servicepersonid, array('class' => 'form-control')) !!}
                                </div>

                                <div class="col-sm-3 form-group">
                                    {!! Form::label('status', 'Status') !!}
                                    <?php
                                    // ako ima više od jednog statusa, dodaj link "Status history" - ako ima tu ROLU
                                    // otvara popup? sa popisom historya: STATUS, DATUM, USER koji je promijenio
                                    if (isset($editingID)) echo '[<a href="#" data-toggle="modal" data-target="#historyModal" >Povijest</a>]';
                                    $tempstatus=1;
                                    if (isset($editingID)) { // EDIT!
                                    if (!is_null($slucaj->repairstatuses->first())) {
                                    $tempstatus=$slucaj->repairstatuses->first()->repairstatus_id;
                                    } else {
                                    $tempstatus=null;
                                    }
                                    }
                                    ?>
                                    {!! Form::select('status', $statusi, $tempstatus, array('class' => 'form-control', 'id'=>'status')) !!}
                                </div>

                                <div class="col-sm-2 form-group">
                                    <label for='relocationspp_id'>Odredište</label>
                                    <?php
                                    if (in_array($tempstatus, \App\Http\Controllers\RepairorderController::getStatusiTrebaOdrediste())) {
                                    $td=array();
                                    $tv=$slucaj->repairstatuses->first()->relocationspp_id;
                                    } else {
                                    $td=['disabled'=>'disabled'];
                                    $tv=null;
                                    }
                                    ?>
                                    {!! Form::select('relocationspp_id', [""=>""]+$servicelocations, $tv, array('class' => 'form-control', 'id'=>'relocationspp_id')+$td) !!}
                                </div>

                                <div class="col-sm-2 form-group">
                                    <label for='devicereturntype_id'>STS Otprema</label>
                                    {!! Form::select('devicereturntype_id', $devicereturntypes, null, array('placeholder'=>'Odaberi', 'class' => 'form-control', 'id'=>'devicereturntype_id')) !!}
                                </div>
                                <div class="col-sm-2 form-group">
                                    <label for='devicereturnother'>Drugo</label>
                                    {!! Form::text('devicereturnother', $devicereturnother, array('disabled'=>'disabled', 'id'=>'devicereturnother', 'class'=>'form-control')) !!}
                                </div>


                                <!-- od panela -->
                            </div>
                        </div>
                    </div>
                    <!-- od panela kraj -->
                    <!-- END: SERVISER, STATUS i OTPREMA (jednog dana i LOG) ============== -->
                    <div style="padding-bottom:5px;">
                        <a href="{{URL::to('printPrijemniView/rn/'.$slucaj->id)}}" target="_blank"
                           class="btn btn-default">Prijemni list Print</a>
                        <a href="{{URL::to('pdfPrijemniView/rn/'.$slucaj->id)}}" target="_blank"
                           class="btn btn-default">Prijemni list - PDF</a>
                        <?php /*@if( (isset($caseClosed) && $caseClosed) || (isset($serviceOver) && $serviceOver) ) */?>
                        <a href="{{URL::to('printView/rn/'.$slucaj->id)}}" target="_blank"
                           class="btn btn-default">Radni nalog - Print</a>
                        <a href="{{URL::to('pdfView/rn/'.$slucaj->id)}}" target="_blank"
                           class="btn btn-default">Radni nalog - PDF</a>
                        <?php /*@endif */?>
                    </div>

                    @if(isset($caseClosed) && $caseClosed)

                        <div class="alert alert-success" role="alert"><i
                                    class="glyphicon glyphicon-ok-circle"></i> Radni nalog zatvoren </div>

                        @if(isset($adminUser) && $adminUser)
                            {!! Form::button('Spremi', array('disabled'=>'disabled', 'id'=>'savebutton', 'class' => 'btn btn-primary')) !!}
                            {!! Form::submit('Spremi i natrag na popis', array('class' => 'btn btn-primary')) !!}
                        @endif

                    @else
                        {!! Form::button('Spremi', array('disabled'=>'disabled', 'id'=>'savebutton', 'class' => 'btn btn-primary')) !!}
                        {!! Form::submit('Spremi i natrag na popis', array('class' => 'btn btn-primary')) !!}
                    @endif

                    <a href="{{URL::to('slucaj')}}" class="btn btn-success">Odustani</a>



                    @else
                                            <!-- samo zaprimanje -->


                    <div class="row">
                        <div class="col-sm-5">
                            <div class="input-group">
                                {!! Form::select('doafterrostore',[
                                    //'print'=>'Kreiraj radni nalog i isprintaj prijemni list',
                                    'new'=>'Kreiraj radni nalog i otvori novi',
                                    'home'=>'Kreiraj radni nalog i vrati se na popis',
                                    'edit'=>'Kreiraj radni nalog i otvori ga'
                                    ], null, array('class' => 'form-control')) !!}
                                <div class="input-group-btn">
                                    {!! Form::submit('Potvrdi', array('class' => ' btn btn-primary')) !!}
                                    <a href="{{URL::to('slucaj')}}" class="btn btn-success">Odustani</a>
                                </div>
                            </div>
                        </div>
                    </div>


                    @endif


                    {!! Form::close() !!}




                    @if (isset($editingID))

                            <!-- HISTORY Modal -->
                    <div class="modal fade" id="historyModal" tabindex="-1" role="dialog"
                         aria-labelledby="historyModalLabel">
                        <!-- ISCRTAJ HISTORY MODAL -->
                        {!!$historymodalcontent!!}
                    </div>

                    @endif






                            <!-- EXSERVISI Modal -->
                    <div class="modal fade" id="exservisiModal" tabindex="-1" role="dialog" aria-labelledby="exservisiModalLabel">

                        <!-- ISCRTAJ EXSERVISI MODAL -->
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel">NALOZI U KOJIMA SE POJAVLJUJE ISTI IMEI/SN</h4>
                                </div>
                                <div id="exservisiModalcontent">
                                    @if (isset($editingID) && count($exservisi)>1)
                                        <div class="modal-body">
                                            <table class="table ">
                                                <thead>
                                                <tr>
                                                    <th>RADNI NALOG</th>
                                                    <th>OTVOREN</th>
                                                    <th>OTPREMLJEN</th>
                                                    <th>KORISNIK</th>
                                                    <th>OPIS ZAHVATA</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($exservisi as $exservis)
                                                    <tr>
                                                        <td class="text-nowrap"><a href="{{URL::to("slucaj/$exservis->id")}}" target="_blank">{{$exservis->stsrepairorderno}}</a></td>
                                                        <td class="text-nowrap">{{$exservis->stsroopendate}}</td>
                                                        <td class="text-nowrap">{{$exservis->devicereturndate}}</td>
                                                        <td class="">{{$exservis->customername." ".$exservis->customerlastname}}</td>
                                                        <td>{{$exservis->stsnotice}}

                                                            @if($exservis->zamjena=="da")
                                                                <span style="color:#f00"> ZAMJENA </span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Zatvori</button>
                                </div>
                            </div>
                        </div>

                    </div>





                    @if(!isset($editingID))
                @push('scripts')
                <script type="text/javascript">
                    $("#checkimeiubazi").on("click", function(e){
                        var value=$("#deviceincomingimei").val();
                        $("#exservisiModalcontent").load("/drawExservisiModal/"+value, function(){
                            $("#exservisiModal").modal('toggle'); //show
                        });
                    });
                </script>
                @endpush
            @endif



                @push('scripts')
                <script type="text/javascript">
                    window.onbeforeunload = function () {
                        document.getElementById("tmkReloadTest").value = "fromcache";
                    };

                    /*
                     $(window).on('beforeunload', function(e) {
                     $('#tmkReloadTest').val("fromcache");
                     });

                     $(function() {
                     if($('#tmkReloadTest').val()=="fromcache") {
                     location.reload();
                     }
                     });
                     */
                </script>
            @endpush










            @stop