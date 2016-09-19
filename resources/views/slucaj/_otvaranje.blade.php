@extends('layouts.shell')
@section('container')



    @if(!empty($errors->all()))
        <ul class='list-unstyled alert alert-danger' role='alert'>
            @foreach ($errors->all() as $msg)
                {!!"<li>".$msg."</li>"!!}
            @endforeach
        </ul>
    @endif

    <!-- will be used to show any messages -->
    @if (Session::has('message'))
        <div class="alert alert-info">{!! Session::get('message') !!}</div>
    @endif


    {!! Form::model($slucaj, array('url' => 'slucaj', 'id'=>'caseForm')) !!}


    <h1 style="margin-bottom:30px;">Novi radni nalog

        @if(!is_null($t2s))
            <!-- tele2ws nalog -->
            TELE2 <input type="hidden" name="wsTele2id" value="{{$t2s['id']}}"/>
        @endif

        @if(Entrust::hasRole(['admin','logistika','spp']))
        za

            @if (Entrust::hasRole(['admin','spp']))
                <!-- ako je admin, daj select -->
                {!! Form::select('stsservicelocation_id', $servicelocations, $servicepersonlocation, array('class' => '', 'id'=>'stsservicelocation_id','style'=>'display: inline-block; color:#999; padding-left:3px; margin-left:7px; font-size:.8em')) !!}
            @else
                <!-- logistika koja nije admin - za otvaranje vidi samo svoj servis -->
                {!! Form::select('stsservicelocation_id', array($servicelocations[$servicepersonlocation]), $servicepersonlocation, array('class' => '', 'id'=>'stsservicelocation_id','style'=>'display: inline-block; color:#999; padding-left:3px; margin-left:7px; font-size:.8em')) !!}
            @endif

        @endif

    </h1>



<!-- TAB NAVIGACIJA >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> -->
<ul class="nav nav-tabs" role="tablist" style="margin-bottom:10px">
    <li role="presentation" class="active"><a href="#ulazniDetalji" aria-controls="ulazniDetalji" role="tab" data-toggle="tab">Zaprimanje</a></li>
</ul>



<!-- TAB PANES >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> -->
<div class="tab-content">

    <!-- TAB PANE 1 ============== -->
    <div role="tabpanel" class="tab-pane active" id="ulazniDetalji">

        <div class="row">

            <div class="col-sm-6">

                        <!-- ===== PANEL: ZAPRIMANJE =============================================================== -->
                        <!-- ako je SPP, onda nema "ZAPRIMANJE" panel -->
                        <input type="hidden" name="pickuppos_id" value="{{$pickuppos_id}}"/>
                        @if($SPPuser)
                            {!! Form::hidden('devicerecievetype_id', 0, array('id'=>'devicerecievetype_id')) !!}
                            {!! Form::hidden('devicerecievedate', date("d.m.Y"), array('class' => 'form-control dateinput')) !!}
                        @else
                            <div class="panel panel-primary">
                                <div class="panel-heading">PRVO ZAPRIMANJE UREĐAJA U STS SERVISU</div>
                                <div class="panel-body color1" style="padding-bottom:15px">
                                    <div class="col-sm-6 form-group">
                                        {!! Form::label('devicerecievetype_id','Vrsta dopreme') !!}
                                        {!! Form::select('devicerecievetype_id', $devicerecievetypes, null, array('placeholder'=>'Odaberi', 'class' => 'form-control', 'id'=>'devicerecievetype_id')) !!}
                                    </div>
                                    <div class="col-sm-6 form-group">
                                        {!! Form::label('devicerecievedate','Datum dopreme') !!}
                                        {!! Form::text('devicerecievedate', date("d.m.Y"), array('class' => 'form-control dateinput')) !!}
                                    </div>
                                    <div class="col-sm-12 form-group" style="margin-top:5px">
                                        {!! Form::text('devicerecieveother', $devicerecieveother, array('disabled'=>'disabled', 'id'=>'devicerecieveother', 'class'=>'form-control')) !!}
                                    </div>
                                </div>
                            </div>
                        @endif
                        <!-- ===== PANEL: ZAPRIMANJE END =========================================================== -->




                                <div class="panel panel-primary">
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
                                                    {!! Form::text('deviceincomingimei', null, array('class' => 'form-control')) !!}
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
                                                    {!! Form::select('devicepostpaid', array('null'=>'/', 'postpaid' => 'Postpaid', 'prepaid' => 'Prepaid'), Input::old('devicepostpaid'), array('class' => 'form-control')) !!}
                                                </div>
                                            </div>

                                            <div class="clearfix">
                                                <div class="col-sm-12 form-group">
                                                    {!! Form::label('devicefailuredescription', 'Opis neispravnosti') !!}
                                                    {!! Form::textarea('devicefailuredescription', Input::old('devicefailuredescription'), array('class' => 'form-control', 'rows'=>'3')) !!}
                                                </div>
                                            </div>

                                            <div class="clearfix">
                                                <div class="col-sm-12 form-group">
                                                    {!! Form::label('ms_customersypmtoms', 'Simptomi (prijava korisnika)') !!}
                                                    {!! Form::select('customersymptoms[]',$customersymptoms,
                                                        (isset($slucaj['damage']) && is_array($slucaj['damage']))
                                                        ? $slucaj['damage']
                                                        : array()
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
                                                    {!! Form::checkbox('deviceaccbattery', '1', false,['class' => 'field']) !!}
                                                    {!! Form::label('deviceaccbattery', 'Baterija') !!}
                                                    <br/>
                                                    {!! Form::checkbox('deviceacccharger', '1', false,['class' => 'field']) !!}
                                                    {!! Form::label('deviceacccharger', 'Punjač') !!}
                                                    <br/>
                                                    {!! Form::checkbox('deviceaccantenna', '1', false,['class' => 'field']) !!}
                                                    {!! Form::label('deviceaccantenna', 'Antena') !!}
                                                    <br/>
                                                    {!! Form::checkbox('deviceaccsim', '1', false,['class' => 'field']) !!}
                                                    {!! Form::label('deviceaccsim', 'SIM') !!}

                                                </div>
                                                <div class="col-xs-6 col-sm-3 form-group">
                                                    {!! Form::checkbox('deviceaccusbcable', '1', false,['class' => 'field']) !!}
                                                    {!! Form::label('deviceaccusbcable', 'USB kabel') !!}
                                                    <br/>
                                                    {!! Form::checkbox('deviceaccmemorycard', '1', false,['class' => 'field']) !!}
                                                    {!! Form::label('deviceaccmemorycard', 'Mem. kartica') !!}
                                                    <br/>
                                                    {!! Form::checkbox('deviceaccheadphones', '1', false,['class' => 'field']) !!}
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
                            <div class="panel panel-primary">
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
                                                {!! Form::text('customeremail', Input::old('customeremail'), array('id'=>'customeremail', 'class' => 'form-control')) !!}
                                            </div>
                                        </div>


                                    </div><!-- druga row-->
                                </div><!-- druga panel-body -->
                            </div><!-- druga panel -->

                            <div class="panel panel-primary">
                                <div class="panel-heading"> KOMITENT (Tko prijavljuje kvar?)</div>
                                <div class="panel-body color1">
                                    <div class="row">

                                        <!-- POS INFO ====================================== -->
                                        <div class="clearfix">
                                            <div class="col-sm-8 form-group">
                                                {!! Form::label('pos_id', 'Komitent - POS') !!}
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
                                                $td = (empty($slucaj->posclaimdate) ) ? null : $slucaj->posclaimdate->format("d.m.Y");
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


                                        <hr style="margin:0px 0px 8px 0px;"/>


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

                            <div class="panel panel-primary">
                                <div class="panel-heading"> OTPREMA (Kako se uređaj otprema nakon servisa?)
                                </div>
                                <div class="panel-body color1">
                                    <div class="col-sm-12 form-group">

                                        {!! Form::select('devicereturntype_id', $devicereturntypes, null, array('placeholder'=>'Odaberi', 'class' => 'form-control tmk-sing-nc-select2', 'id'=>'devicereturntype_id')) !!}
                                    </div>
                                    <div class="col-sm-12 form-group" style="margin-bottom:12px">
                                        {!! Form::text('devicereturnother', $devicereturnother, array('disabled'=>'disabled', 'id'=>'devicereturnother', 'class'=>'form-control')) !!}
                                    </div>
                                </div>
                            </div>


                        </div><!-- druga kolona -->


                </div><!-- row -->

            </div>    <!-- TAB PANE 1 ============== -->

        </div> <!-- TAB PANES ============== -->




<!-- ======= STATUS I DNEVNIK ======================================== -->
        <div class="panel panel-primary">
            <div class="panel-heading"> STATUS NALOGA I DNEVNIK RADA</div>
            <div class="panel-body color3">

                <div class="row">

                    <div class="col-sm-4 form-group">
                        <label for='stsserviceperson_id'>Korisnik</label>

                        @if($adminUser)
                            {!! Form::select('stsserviceperson_id', $activeservicepersonscustom, $servicepersonid, array('class' => 'form-control')) !!}
                        @else
                            <input type="text" readonly="readonly" class="form-control"
                                   value="{{$activeservicepersonscustom[$servicepersonid]}}"/>
                            {!! Form::hidden('stsserviceperson_id', $servicepersonid) !!}
                        @endif

                    </div>

                    <div class="col-sm-6 form-group">
                        {!! Form::label('status', 'Status') !!}
                        <?php
                        // ako ima više od jednog statusa, dodaj link "Status history" - ako ima tu ROLU
                        // otvara popup? sa popisom historya: STATUS, DATUM, USER koji je promijenio
                        $tempstatus = 1;
                        ?>
                        {!! Form::select('status', $statusi, $tempstatus, array('class' => 'form-control', 'id'=>'status')) !!}
                    </div>
                    <div class="col-sm-2 form-group">
                        <label for='relocationspp_id'>Odredište</label>
                        <?php
                        if (isset($editingID) && in_array($tempstatus,\App\Http\Controllers\RepairorderController::getStatusiTrebaOdrediste())) {
                            $tv = $slucaj->repairstatuses->first()->relocationspp_id;
                        } else {
                            $tv = null;
                        }
                        ?>
                        <select class="form-control" name="relocationspp_id" id="relocationspp_id"
                        @if(!is_null($tv))
                            disabled="disabled"
                        @endif
                        >
                            @foreach($servicelocations as $slk=>$slv)
                                <option value="{{$slk}}"
                                @if ($slk==$tv)
                                    selected = "selected"
                                @endif
                                >{{$slv}}</option>
                            @endforeach
                        </select>
                    </div>
                </div> <!-- row -->

                <div class="form-group">
                    {!! Form::label('loginput', 'Dnevnik rada') !!}
                    [<a href="">Pregledaj</a>]
                    <div class="input-group">
                        <div class="input-group-addon">{{date("d.m.Y")}} : {{$servicepersonuser}}</div> <!-- d.m.Y H:i:s -->
                        {!! Form::text('loginput', null, array('class' => 'form-control')) !!}
                    </div>
                </div>

            </div>
        </div>




        <!-- ================== STATUS I DNEVNIK END ====================== -->

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


    {!! Form::close() !!}



















        @push('scripts')
        <script type="text/javascript">
            window.onbeforeunload = function (ev) {
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

















