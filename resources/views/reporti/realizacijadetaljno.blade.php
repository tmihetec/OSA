@extends('layouts.shell')

@section('container')




<!-- if there are creation errors, they will show here -->
{!! HTML::ul($errors->all(),array('class'=>'list-unstyled alert alert-danger', 'role'=>'alert')) !!}


<h1>Realizacija detaljno</h1>
<p class="notice">Broj zatvorenih naloga servisera u danom intervalu</p>
	<hr />
	
<!-- will be used to show any messages -->
@if (Session::has('message'))
    <div class="alert alert-info">{{ Session::get('message') }}</div>
@endif


			<!-- filter -->
			<form method="POST" action="{{URL::to('reportRealizacijaDetaljno')}}" class="form-inline">
						    {!! csrf_field() !!}
						    <div class="row">

							  <div class="form-group col-sm-3">
							    <label  for="serviser_id">Serviser</label><br />
								<select name="serviser_id" id="serviser_id" class="form-control tmk-select2" data-placeholder="odaberi" >
										<option value=""></option>
								    	@foreach ($serviseri as $key => $value)
								    		<option value="{{$key}}" 
									    		@if($postedData->serviser_id == $key)
									    			{!!"selected = 'selected' "!!}
									    		@endif
									    		>{{$value}}</option>
								    	@endforeach
							    </select>	
							 </div>

							  <div class="form-group">
							    <label  for="serviceLocation">Datum od</label><br />
							    <input type="text" name="datumOd" id="datumOd" value="{{($postedData->datumOdObj) ? $postedData->datumOdObj->format("d.m.Y"): ''}}" class="form-control dateinput" />
							 </div>

							  <div class="form-group ">
							    <label  for="serviceLocation">Datum do</label><br />
							    <input type="text" name="datumDo" id="datumDo" value="{{($postedData->datumDoObj) ? $postedData->datumDoObj->format("d.m.Y") : ''}}"class="form-control dateinput"/>

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
			@if(!is_null($nalozi))
				<!-- ispis tablice -->

				<table id="rptrealizacijadetaljno" class="tmkdt table table-striped table-bordered table-hover nowrap" cellspacing="0" width="100%">
				    <thead>
				        <tr>
				            <th>RADNI NALOG</th>
				            <th class="nemojprintat"></th>
				            <th>DATUM ZAVRŠETKA</th>
				            <th>LOKACIJA</th>
				            <th>SWAP</th>
				            <th>MBSWAP</th>
				            <th>IMAKVAR</th>
				            <th>IMA JAMSTVO</th>
				            <th>NAPLATIVO kn</th>
				        </tr>

				    </thead>
				    <tbody>

				    @foreach($nalozi as $key => $value)
				        <tr>
				            <td>
				            	

				            	<a href="{{URL::to('slucaj/'.$value->id)}}">{{ $value->nalog }}</a> 


				            </td>
				            <td>
					            <a style="padding:0px 4px; margin:0px; line-height:1.2em; font-size:11px;" class="btn btn-default btn-xs hidden-print"  title="prikaži statuse" data-toggle="modal" data-target="#myModal" data-nalog="{{$value->id}}" data-brojnaloga="{{$value->nalog}}">statusi</a>
				            </td>
				            <td>{{ date("d.m.Y", strtotime($value->stsroclosingdate)) }}</td>
				            <td>{{ $value->posname }}</td>
				            <td>{{ $value->swap }}</td>
				            <td>{{ $value->mbswap }}</td>
				            <td>{{ $value->imakvar }}</td>
				            <td>{{ $value->jamstvo }}</td>
				            <td class="text-right">{{ number_format($value->iznos,0,",","") }}</td>

				        </tr>   
				    @endforeach

				    </tbody>
				    <tfoot>
				        <tr>
				            <th colspan="4">UKUPNO</th>
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
				<div class="alert alert-warning" role="alert">Žitra ljeda!</div>
			@endif
			<!-- rezltat end -->



@include('layouts.modal')
@yield('mymodal')


@push('scripts')
<script>
$('#myModal').on('show.bs.modal', function (event) {
  var atag = $(event.relatedTarget) // Button that triggered the modal
  var nalog = atag.data('nalog') // Extract info from data-* attributes
  var brojnaloga = atag.data('brojnaloga')
  var modal = $(this)
  modal.find('.modal-title').text('Statusi za nalog '+brojnaloga);
  modal.find('.modal-body').load("/dajStatuseNaloga"
  	, { nalog : nalog, 
  		_token:"{{csrf_token()}}"
  	}
	, function(responseTxt, statusTxt, xhr){
	        if(statusTxt == "success"){
	            //alert("External content loaded successfully!");
		        }
	        if(statusTxt == "error"){
	            //alert("Error: " + xhr.status + ": " + xhr.statusText);
	        	}
	    }
	);
});
</script>
@endpush





@stop