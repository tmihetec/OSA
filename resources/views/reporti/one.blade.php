@extends('layouts.shell')

@section('container')


<!-- if there are creation errors, they will show here -->
{!! HTML::ul($errors->all(),array('class'=>'list-unstyled alert alert-danger', 'role'=>'alert')) !!}


<h1>Kompletni izvještaj</h1>
	<hr />
	
<!-- will be used to show any messages -->
@if (Session::has('message'))
    <div class="alert alert-info">{{ Session::get('message') }}</div>
@endif

{{-- <div class="alert alert-danger">NAČIN ISPISA REZULTATA! - KAO ARHIVA?</div> --}}

<form method="POST" action="{{URL::to('reportOne')}}" id="rptOneForm" role="form" class="form">
    {!! csrf_field() !!}

    <div class="row">
    	<div class="col-sm-2">
			<div class="form-group">
		        <label for="dateFrom">Datum od:</label>
			    <input type="text" name="dateFrom" value="{{$postedData->dateFrom}}" class="form-control dateinput"/>
	        </div>
        </div>
    	<div class="col-sm-2">
			<div class="form-group">
			    <label for="dateTo">Datum do:</label>
			    <input type="text" name="dateTo" value="{{$postedData->dateTo}}" class="form-control dateinput" />
	        </div>
        </div>
    	<div class="col-sm-3">
			<div class="form-group">
			    <label for="brand">Brand:</label>
			    <select data-placeholder="Svi" multiple="multiple" name="brand[]" class="form-control tmk-sing-select2">
			    @foreach ($brandovi as $key => $value)
		    		<option value="{{$key}}" 
			    		@if(is_array($postedData->brandovi) && in_array($key,$postedData->brandovi))
			    			{{"selected = 'selected' "}}
			    		@endif
		    		>{{$value}}</option>
		    	@endforeach
				</select>
	        </div>
        </div>
    	<div class="col-sm-2">
			<div class="form-group">
			    <label for="model">Tip: </label>
			    <select data-placeholder="Svi" multiple="multiple" name="tip[]" class="form-control tmk-sing-select2"> @foreach ($tipovi as $key => $value)
		    		<option value="{{$key}}" 
			    		@if(is_array($postedData->tipovi) && in_array($key,$postedData->tipovi))
			    			{{"selected = 'selected' "}}
			    		@endif
		    		>{{$value}}</option>
		    	@endforeach
				</select>
	        </div>
        </div>

    	<div class="col-sm-3">
			<div class="form-group">
			    <label for="model">Model:</label>
			    <select data-placeholder="Svi" multiple="multiple" name="model[]" class="form-control tmk-sing-select2"> @foreach ($modeli as $key => $value)
		    		<option value="{{$key}}" 
			    		@if(is_array($postedData->modeli) && in_array($key,$postedData->modeli))
			    			{{"selected = 'selected' "}}
			    		@endif
		    		>{{$value}}</option>
		    	@endforeach
				</select>
	        </div>
        </div>

    </div>


    <div class="row">
        <div class="col-sm-2">
			    <label for="spp">SPP:</label>
			    <select name="spp[]" class="form-control tmk-sing-select2" data-placeholder="Svi" multiple="multiple" >
			    	@foreach ($spp as $key => $value)
			    		<option value="{{$key}}"
			    		@if(is_array($postedData->spp) && in_array($key,$postedData->spp))
			    			{{"selected = 'selected' "}}
			    		@endif
			    		>{{$value}}</option>
			    	@endforeach
			    </select>
        </div>
    	<div class="col-sm-2">
			<div class="form-group">
			    <label for="komitenti">Komitent:</label>
			    <select name="komitenti[]" class="form-control tmk-sing-select2" data-placeholder="Svi" multiple="multiple" >
			    	@foreach ($komitenti as $key => $value)
			    		<option value="{{$key}}"
			    		@if(is_array($postedData->komitenti) && in_array($key,$postedData->komitenti))
			    			{{"selected = 'selected' "}}
			    		@endif
			    		>{{$value}}</option>
			    	@endforeach
			    </select>
	        </div>
        </div>
        <div class="col-sm-3">
			    <label for="spp">Prodajno mjesto:</label>
			    <select name="spp[]" class="form-control tmk-sing-select2" data-placeholder="Svi" multiple="multiple" >
			    	@foreach ($spp as $key => $value)
			    		<option value="{{$key}}"
			    		@if(is_array($postedData->spp) && in_array($key,$postedData->spp))
			    			{{"selected = 'selected' "}}
			    		@endif
			    		>{{$value}}</option>
			    	@endforeach
			    </select>
        </div>
        <div class="col-sm-2">
		    <label for="serviceLocation">Lokacija:</label>

		    <select name="serviceLocation[]" class="form-control tmk-sing-select2" data-placeholder="Sve" multiple="multiple" >
		    	@foreach ($lokacije as $key => $value)
		    		<option value="{{$key}}" 
			    		@if(is_array($postedData->lokacije) && in_array($key,$postedData->lokacije))
			    			{{"selected = 'selected' "}}
			    		@endif
			    		>{{$value}}</option>
		    	@endforeach
		    </select>
        </div>
    	<div class="col-sm-3">
    		<div class="form-group">
			    <label for="servicePerson">Serviser:</label>

			    <select name="servicePerson[]" class="form-control tmk-sing-select2" data-placeholder="Svi" multiple="multiple" >
			    	@foreach ($serviseri as $key => $value)
			    		<option value="{{$key}}" 
				    		@if(is_array($postedData->serviseri) && in_array($key,$postedData->serviseri))
				    			{{"selected = 'selected' "}}
				    		@endif
			    		>{{$value}}</option>
			    	@endforeach
			    </select>
		    </div>
		</div>
    </div>

	<div class="row">
		<div class="col-sm-4">
    		<div class="form-group">
			    <label for="status">Zadnji status je neki od:</label>
			    <select name="status[]" class="form-control tmk-sing-select2" data-placeholder="Svi" multiple="multiple" >
			    	@foreach ($statusi as $key => $value)
			    		<option value="{{$key}}"
			    		@if(is_array($postedData->statusi) && in_array($key,$postedData->statusi))
			    			{{"selected = 'selected' "}}
			    		@endif
			    		>{{$value}}</option>
			    	@endforeach
			    </select>
		    </div>
		</div>
		<div class="col-sm-4">
    		<div class="form-group">
			    <label for="imanekestatuse">Ima neki od statusa:</label>
			    <select name="imanekestatuse[]" class="form-control tmk-sing-select2" data-placeholder="Svi" multiple="multiple" >
			    	@foreach ($statusi as $key => $value)
			    		<option value="{{$key}}"
			    		@if(is_array($postedData->imanekestatuse) && in_array($key,$postedData->imanekestatuse))
			    			{{"selected = 'selected' "}}
			    		@endif
			    		>{{$value}}</option>
			    	@endforeach
			    </select>
		    </div>
		</div>
		<div class="col-sm-4">
    		<div class="form-group">
			    <label for="imastatuse">Ima sve ove statuse:</label>
			    <select name="imastatuse[]" class="form-control tmk-sing-select2" data-placeholder="Svi" multiple="multiple" >
			    	@foreach ($statusi as $key => $value)
			    		<option value="{{$key}}"
			    		@if(is_array($postedData->imastatuse) && in_array($key,$postedData->imastatuse))
			    			{{"selected = 'selected' "}}
			    		@endif
			    		>{{$value}}</option>
			    	@endforeach
			    </select>
		    </div>
		</div>
	</div>




	<div class="row">
		<div class="col-sm-3">
			<input type="submit" value="Generiraj" class="btn btn-block btn-primary" />
		</div>
		<div class="col-sm-3">
			<a href="#" class="disabled btn btn-block  btn-default">Print</a>
		</div>
		<div class="col-sm-3">
			<a href="#" class="disabled btn btn-block  btn-default">PDF</a>
		</div>
		<div class="col-sm-3">
			<a href="#" class="disabled btn btn-block  btn-default">Excel</a>
		</div>
	</div>

</form>



@if(!is_null($nalozi))

<hr />

<table id="rptOneTable" class="tmkdt tmkdt-rpt1 table table-striped table-bordered table-hover nowrap" cellspacing="0" width="100%">
    <thead>
        <tr class="filterrow">
            <th>STS RN</th>
            <th>UREĐAJ</th>
            <th class="none">LOKACIJA</th> <!-- style="display:none" -->
            <th>ZADNJI STATUS</th>
            <th>DATUM</th>
            <th>SERVISER</th>
            <th>KOMITENT</th>
            <th>SPP</th>
            <th>OTPREMA</th>
            <th>ADRESA OTPREME</th>
            <th class="none">IMEI</th> <!--style="display:none"-->
            <th width="51"></th>
        </tr>

    </thead>


    <tbody>

    @foreach($nalozi as $key => $value)
        <tr>
            <td>{{ $value->stsrepairorderno }}</td> 
            <td>{{ $value->uredjaj }}</td>
            <td >{{ $value->servisname }}</td>
            <td>{{ $value->zadnjistatus }}</td>
           	<td>{{ date("d.m.Y", strtotime($value->datumstatusa))}}</td>
            <td><span {!! (!is_null($value->zbrisankorisnik)) ? "style='color:#900; text-decoration:line-through;'" :"" 
            !!}>{{ $value->serviser }}</span></td>
            <td>{{ $value->komitent }}</td>
            <td>{{ $value->posname }}</td>
            <td>{{ $value->otprema }}</td>
            <td>{!! implode("<br>",array_filter(array($value->adresaotpreme1,$value->adresaotpreme2))) !!}</td>
            <td >{{ $value->deviceincomingimei }}</td>
            <td width="51">
            	<div class="btn-group btn-group-xs" role="group">
					<a class="btn btn-small btn-warning" title="edit" href="{{ URL::to('slucaj/' . $value->id . '/edit') }}"><i class="glyphicon glyphicon-pencil"></i></a>
                    <a class="btn btn-small btn-info" target="_blank" title="print view" href="{{ URL::to('printView/rn/' . $value->id) }}"><i class="glyphicon glyphicon-eye-open"></i></a>
                </div>
            </td>
        </tr>   
    @endforeach


    </tbody>


</table>

@endif



@push('scripts')


<script>
$(function() {

	$('.tmkdt.tmkdt-rpt1').DataTable({
		"order": [[2,"desc"],[0,"desc"]], // datum pa broj naloga
		"pagingType": "full",
		"pageLength": 25,
		"stateSave": false,	
        processing: true,
		"deferRender": true,
	});



{{-- 
  $('#rptOneTable').DataTable({
        processing: true,
        pageLength: 25,
        fixedHeader: true,
        serverSide: true,
        deferRender: true,
        deferLoading: {{$total}}, //https://datatables.net/examples/server_side/defer_loading.html
        //searchDelay: 100,
        ajax: {
            url: '/dohvatiNalogeZaOne',
            type: 'POST',
            data: {
                "_token": "{!!csrf_token()!!}"
            },
            //dataSrc:function (json) {
            //    alert("Done!");
            //    return json.data;
            //}
            
        },


           
        columns: [
            { name: 'stsrepairorderno', data:'stsrepairorderno'},
            { name: 'uredjaj', data:'uredjaj'},
            { name: 'servisname', data:'servisname'},
            { name: 'zadnjistatus', data:'zadnjistatus'},
            { name: 'datumstatusa', data:'datumstatusa'},
            { name: 'zbrisankorisnik', data:'zbrisankorisnik'},
            { name: 'komitent', data:'komitent'},
            { name: 'posname', data:'posname'},
            { name: 'otprema', data:'otprema'},
            { name: 'adresaotpreme', data:'adresaotpreme'},
            { name: 'deviceincomingimei', data: 'deviceincomingimei'},
            { name: 'alati', data:'alati', 'searchable': false}
        ],


        "order": [[2,"desc"],[0,"desc"]],
        "initComplete": function (settings, json) {

            //delay kod unosa teksta u filter - smanjiti ajax upite
            //https://www.datatables.net/forums/discussion/23970/1-10-3-searchdelay-not-working-properly
            var api = this.api();
            $('#rptOneTable_filter input').off('keyup.DT search.DT input.DT paste.DT cut.DT');
            var searchDelay = null;             
            $('#rptOneTable_filter input').on('keyup', function(event) {
            
                var c= String.fromCharCode(event.keyCode);
                var isWordCharacter = c.match(/\w/);
                var isBackspaceOrDelete = (event.keyCode == 8 || event.keyCode == 46);
                
                if (isWordCharacter || isBackspaceOrDelete) {
                    var search = $('#rptOneTable_filter input').val();
             
                    clearTimeout(searchDelay);
             
                    searchDelay = setTimeout(function() {
                        if ($.trim(search) != null && $.trim(search).length>1) {
                            api.search(search).draw();
                        }
                    }, 300);
                }

            });


        },
        "drawCallback": function (response) {
            //console.log(response.json);
            //alert("Done!");
        }



    });

--}}

});

</script>
@endpush


@stop