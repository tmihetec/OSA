@extends('layouts.shell')

@section('container')




<!-- if there are creation errors, they will show here -->
{!! HTML::ul($errors->all(),array('class'=>'list-unstyled alert alert-danger', 'role'=>'alert')) !!}


<h1>Realizacija sumarno</h1>
<p class="notice">Broj zatvorenih naloga po serviserima u danom intervalu - nalozi koji imaju status: <strong>SERVIS ZAVRŠEN</strong> ili <strong>ODUSTANAK OD SERVISA</strong> </p>
	<hr />
	
<!-- will be used to show any messages -->
@if (Session::has('message'))
    <div class="alert alert-info">{{ Session::get('message') }}</div>
@endif


			<!-- filter -->
			<form method="POST" action="{{URL::to('reportRealizacija')}}" class="form-inline">
						    {!! csrf_field() !!}
						    <div class="row">

							  <div class="form-group col-sm-2">
							    <label  for="serviceLocation">Lokacija</label><br />
								<select name="serviceLocation[]" class="form-control tmk-sing-select2" data-placeholder="Sve lokacije" multiple="multiple" >
								    	@foreach ($lokacije as $key => $value)
								    		<option value="{{$key}}" 
									    		@if(is_array($postedData->lokacije) && in_array($key,$postedData->lokacije))
									    			{!!"selected = 'selected' "!!}
									    		@endif
									    		>{{$value}}</option>
								    	@endforeach
							    </select>	
							 </div>

							  <div class="form-group">
							    <label  for="serviceLocation">Datum od</label><br />
							    <input type="text" name="datumOd" value="{{($postedData->datumOdObj) ? $postedData->datumOdObj->format("d.m.Y"): ''}}" class="form-control dateinput" id="datumOd" />
							 </div>

							  <div class="form-group ">
							    <label  for="serviceLocation">Datum do</label><br />
							    <input type="text" name="datumDo"  value="{{($postedData->datumDoObj) ? $postedData->datumDoObj->format("d.m.Y") : ''}}"class="form-control dateinput" id="datumDo"/>

								<input type="submit" value="Generiraj" class="btn btn-primary" />
								<!--
								<a href="#" class="disabled btn btn-default">Print</a>
								<a href="#" class="disabled btn btn-default">PDF</a>
								-->
							 </div>
							</div>
			</form>
			<!-- filter end -->


			<!-- rezultat -->
			<hr />
			@if(!is_null($realiziraniNalozi))
				<!-- ispis tablice -->

				<table id="rptrealizacija" class="tmkdt table table-striped table-bordered table-hover nowrap" cellspacing="0" width="100%">
				    <thead>
				        <tr>
				            <th>IME I PREZIME</th>
				            <th>LOKACIJA</th>
				            <th>REALIZACIJA</th>
				            <th># SWAP</th>
				            <th># MBSWAP</th>
				            <th># IMAKVAR</th>
				            <th># IMA JAMSTVO</th>
				            <th>NAPLATIVO kn</th>
				        </tr>

				    </thead>
				    <tbody>

				    @foreach($realiziraniNalozi as $key => $value)
				        <tr>
				            <td><a class="realizacijadetaljilink" href="{{URL::to('reportRealizacijaDetaljno/'.$value->stsserviceperson_id)}}">{{ $value->serviser }}</a></td>
				            <td>{{ $value->posname }}</td>
				            <td>{{ $value->realizacija }}</td>
				            <td>{{ $value->swap }}</td>
				            <td>{{ $value->mbswap }}</td>
				            <td>{{ $value->imakvar }}</td>
				            <td>{{ $value->jamstvo }}</td>
				            <td class="text-right">{{ number_format($value->iznos,0,",","")}}</td>

				        </tr>   
				    @endforeach

				    </tbody>
				    <tfoot>
				        <tr>
				            <th colspan="2">UKUPNO</th>
				            <th></th>
				            <th></th>
				            <th></th>
				            <th></th>
				            <th></th>
				            <th class="text-right"></th>
				        </tr>

				    </tfoot>
				</table>

				<!-- za odabir redova
				<hr />
				<button class="btn btn-warning" id="otpreminaloge" disabled="disabled">Otpremi označeno</button>
				-->

				<!-- ispis tablice end -->
			@else 
				<div class="alert alert-warning" role="alert">Nema realiziranih naloga</div>
			@endif
			<!-- rezltat end -->


@push('scripts')
<script>
	$("a.realizacijadetaljilink").on("click", function(e){
		e.preventDefault();
		var odd = ($("#datumOd").val()=="") ? "-" : $("#datumOd").val();
		var dod = ($("#datumDo").val()=="") ? "-" : $("#datumDo").val();
		window.location=$(this).attr("href")+"/"+odd+"/"+dod;
	});
</script>
@endpush



@stop