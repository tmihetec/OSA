@extends('layouts.shell')

@section('container')




<!-- if there are creation errors, they will show here -->
{!! HTML::ul($errors->all(),array('class'=>'list-unstyled alert alert-danger', 'role'=>'alert')) !!}


<h1>Uređaji za otpremu ili relokaciju</h1>
<p class="notice">Nalozi kojima je zadnji status za gotov nalog: <strong>SERVIS ZAVRŠEN</strong> ili <strong>ODUSTANAK OD SERVISA</strong>, odnosno status: <strong>POTREBNO PREBACITI U DRUGI SERVIS NA DORADU</strong></p>
<p> Ako je servis gotov, onda provjerava da li je uređaj stigao <em>relokacijom</em> pa uz otpremu nudi povratak na tu lokaciju</p>
	<hr />
	
<!-- will be used to show any messages -->
@if (Session::has('message'))
    <div class="alert alert-info">{{ Session::get('message') }}</div>
@endif


			<!-- filter -->
			<form method="POST" action="{{URL::to('reportOtprema')}}" class="form-inline">
						    {!! csrf_field() !!}
						    <div class="row">
						    <div class="col-sm-12">
							  <div class="form-group">
							    <label class="sr-only" for="serviceLocation">Lokacija</label>
								    <select name="serviceLocation" id="serviceLocation" class="form-control" data-placeholder="Sve lokacije"   >	
								    	<option value="0">Sve lokacije</option>
								    	@foreach ($lokacije as $key => $value)
								    		<option value="{{$key}}"
								    		<?php /* 
									    		@if(is_array($postedData->lokacije) && in_array($key,$postedData->lokacije))
									    			{!!"selected = 'selected' "!!}
									    		@endif
									    	*/ ?>
									    		@if($postedData->lokacije == $key)
									    			{!!"selected = 'selected' "!!}
									    		@endif
									    		>{{$value}}</option>
								    	@endforeach
								    </select>	
							</div>
							<div class="form-group">
							    <label class="sr-only" for="serviseri">Serviser</label>
								    <select name="serviseri" id="serviseri" class="form-control" data-placeholder="Svi serviseri"  >
								    	<option value="0">Svi serviseri</option>
								    	@foreach ($serviseri as $key => $value)
								    		<option value="{{$key}}" 
									    		@if($postedData->serviseri == $key)
									    			{!!"selected = 'selected' "!!}
									    		@endif
									    		>{{$value}}</option>
								    	@endforeach
								    </select>	
							</div>
							<div class="form-group">
							    <label class="sr-only" for="datumzavrsetka">Datum</label>
							    	<input type="text" class="dateinput form-control" name="datumzavrsetka" id="datumzavrsetka" value="
										@if (isset($postedData->datumzavrsetka))
								            {{ $postedData->datumzavrsetka }}
								        @endif
							    	" placeholder="Svi datumi ili samo datum" />	

							</div>
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
			@if(!is_null($naloziZaOtpremu))
				<!-- ispis tablice -->

				<table id="rptotprema" class="tmkdt tmkdt-rptotprema table table-striped table-bordered table-hover nowrap" cellspacing="0" width="100%">
				    <thead>
				        <tr class="filterrow">
				            <th>STS RN</th>
				            <th>UREĐAJ</th>
				            <th class="nemojprintat" style="display:none">LOK.</th> <!-- class="columnfilterthis" --> <!-- display:none -->
				            <th>STATUS</th>
				            <th>DATUM</th>
				            <th class="nemojprintat" style="display:none">SERVISER</th>
				            <th>KOMITENT</th>
				            <th>SPP (POS)</th>
				            <th class="nemojprintat" style="display:none">(POSID)</th>
				            <th>OTPREMA</th>
				            <th>ADRESA OTPREME</th>
				            <th><span title="relokacija iz">REL</span></th>
				            <!--<th style="">IMEI</th>-->
				            <th class="nemojprintat"></th>
				        </tr>

				    </thead>
				    <tbody>

				    @foreach($naloziZaOtpremu as $key => $value)
					
				        <tr>
				            <td><span class="rowid" style="display:none">{{$value->id}}</span>{{ $value->stsrepairorderno }}</td> <!--$value->stsrepairorderno-->
				            <td>{{ $value->model->brand->name." ".$value->model->name }}</td>
				            <td style="display:none">{{ $value->servicelocation->name }}</td>
				            <td> 
				            <?php
				            // prebaci je true ako je nalog ili za needrelocation ILI ako je završen/odustanak, a imao je relokaciju prije

				            	$prebaci=false;
				            	$vrati=false;
				            	$prebacititle="";
				            	$stigloiz="";

			            		$st=(count($value->lateststatus()->first())) ? $value->lateststatus()->first()->name : "";

				            	if ($value->lateststatus()->first()->repairstatus_id ==$statusPrebacivanja) {
				            		// treba ići na doradu
				            		$prebaci=true;
				            		$st="PREBACI U ".$lokacije[$value->lateststatus()->first()->relocationspp_id];
				            		$prebacititle=$st;
				            	} elseif (count($value->relocationBack())){
				            		// ili postoji zadnja relokacija (isporuka ili servis) 
									$vrati=true;
									$stigloiz=$lokacije[$value->relocationBack()->locationspp_id];
				            		$prebacititle="VRATI U ".$stigloiz;
				            	} 
				            ?>
				            {{ $st }}</td>
				           	<td>{{ $value->lateststatus()->first()->pivot->created_at->format("d.m.Y") }}</td>
				            <td style="display:none">
    				           	@if (null!==$value->serviser) 
									{{ $value->serviser->first_name." ".$value->serviser->last_name }}
								@endif
							</td>
				            <td>{{ $value->pos->principal->naziv }}</td> 
				            <td>{{ $value->pos->posname }}</td>
				            <td style="display:none">{{ $value->pos->posid }}</td>
				            <td>{!! $value->otprema->name !!}
				            <td>{!! $value->adresa_otpreme !!}
				            </td>
				            <td>{!! $stigloiz !!}
				            </td>
				            <!--<td style="">{{ $value->deviceincomingimei }}</td>-->
				            <td width="51">
				            	<div class="btn-group btn-group-xs" role="group">
				            	
									<!-- edit this nerd (uses the edit method found at GET /nerds/{id}/edit -->
									<a class="btn btn-small btn-warning" title="edit" href="{{ URL::to('slucaj/' . $value->id . '/edit') }}"><i class="glyphicon glyphicon-pencil"></i></a>

				                    <!-- print -->
				                    <a class="btn btn-small btn-info" target="_blank" title="print view" href="{{ URL::to('printView/rn/' . $value->id) }}"><i class="glyphicon glyphicon-eye-open"></i></a>

				                    <!-- RELOCIRAJ -->
				                    @if($prebaci || $vrati)
				                    	<a class="btn btn-small btn-primary prebaciNalog" title="{{$prebacititle}}" data-nalog="{{$value->stsrepairorderno}}" data-token="{{csrf_token()}}" data-myhref="{{ URL::to('slucaj/relociraj/' . $value->id) }}"><i class="glyphicon glyphicon-retweet"></i></a>
				                    @else
				                    	<a class="btn btn-small btn-primary prebaciNalog disabled" disabled="disabled"><i class="glyphicon glyphicon-retweet"></i></a>
				                    @endif
				                    <!-- OTPREMI -->
				                    @if(!$prebaci)
				                    	<a class="btn btn-small btn-success otpremiNalog" title="OTPREMI NALOG" data-nalog="{{$value->stsrepairorderno}}" data-token="{{csrf_token()}}" data-myhref="{{ URL::to('slucaj/otpremi/' . $value->id) }}"><i class="glyphicon glyphicon-plane"></i></a>
				                    @else
				                    	<a class="btn btn-small btn-success otpremiNalog disabled" disabled="disabled"><i class="glyphicon glyphicon-plane"></i></a>
				                    @endif

				                </div>
				            </td>

				        </tr>   
				    @endforeach

				    </tbody>
				</table>

				<!-- za odabir redova
				<hr />
				<button class="btn btn-warning" id="otpreminaloge" disabled="disabled">Otpremi označeno</button>
				-->

				<!-- ispis tablice end -->
			@else 
				<div class="alert alert-warning" role="alert">Trenutno nema naloga za otpremu za traženu lokaciju/e</div>
			@endif
			<!-- rezltat end -->


@stop