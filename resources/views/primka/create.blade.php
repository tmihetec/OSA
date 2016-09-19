@extends('layouts.shell')

@section('container')


<!-- if there are creation errors, they will show here -->
{!! HTML::ul($errors->all(),array('class'=>'list-unstyled alert alert-danger', 'role'=>'alert')) !!}


<h1>
@if (isset($editingID))
	Izmjena primke
@else
	Nova primka
@endif
	<a href="{{URL::to('primke/')}}" title="Popis primki" class="label label-default pull-right" style="font-size:.5em; margin-top:10px" ><i class="glyphicon glyphicon-list"></i></a>
</h1>
<hr />

<!-- will be used to show any messages -->
@if (Session::has('message'))
<div class="alert alert-info">{{ Session::get('message') }}</div>
@endif



@if (isset($editingID))
<!-- edit -->
<?php /* {!! Form::model($primka, ['route' => array('primke.update', $primka->id), 'method' => 'put']) !!} */ ?>
{!! Form::model($primka, ['url' => '{{URL::to("primke"/.$primka->id)}}', 'method' => 'put']) !!}
@else 
<!-- create -->
{!! Form::model($primka, ['url' => '{{URL::to("primke"/)}}', 'method' => 'post']) !!}
@endif

<div class="row">
	<div class="col-sm-6">
		{!! Form::label('receipt_no', 'Broj primke') !!}
		{!! Form::text('fake', empty($primka->receipt_no)?null : $primka->receipt_no, array('class' => 'form-control', 'disabled'=>'disabled')) !!}
		{!! Form::hidden('receipt_no', null, array('class' => 'form-control', )) !!}

		{!! Form::label('receipt_datetime', 'Datum primke') !!}
		{!! Form::text('receipt_datetime', empty($primka->receipt_datetime) ? date("d.m.Y") : $primka->receipt_datetime->format("m/Y"), array('class' => 'form-control dateinput')) !!} 

		{!! Form::label('warehouse_id', 'Prijemno skladište') !!}
		{!! Form::select('warehouse_id', array(""=>"")+$warehouses, null, array('class' => 'form-control')) !!}

		{!! Form::label('supplier_id', 'Dobavljač') !!}
		{!! Form::select('supplier_id', array(""=>"")+$suppliers, null, array('class' => 'form-control')) !!}

		{!! Form::label('document_no', 'Broj ulaznog dokumenta') !!}
		{!! Form::text('document_no', null, array('class' => 'form-control')) !!} 

		{!! Form::label('document_date', 'Datum ulaznog dokumenta') !!}
		{!! Form::text('document_date', empty($primka->document_date) ? null : $primka->document_date->format("m/Y"), array('class' => 'form-control dateinput')) !!} 

	</div>
	<div class="col-sm-6">


	<!-- STAVKE PRIMKE -->
		{!! Form::label('sppartsreceipt', 'Stavke primke') !!}
		{!! Form::select('sppartsreceipt', array(""=>"")+$spareparts, null, array('class' => 'form-control')) !!}
		<hr />
	<!-- STAVKE PRIMKE -->

					<div id="tmk_spareparts">
					<?php
					//AKO je edit i ima nekaj, onda popuni u tablice veze iz baze!
					if (isset($primka) && !($primka->spareparts->isEmpty())){
						// TEMPLATE (iz tmkjs.js) početak tablice 
						$tmk_sparepartsCount=0;
						echo '<table class="table table-condensed table-bordered"><thead><tr><th>naziv</th><th class="col-md-2">kom</th><th class="col-md-2">cijena</th></tr></thead><tbody>';
						foreach($primka->spareparts as $sparepart)	{
							$selid=$sparepart->id;
							$seltxt=$sparepart->name;
							$selprc=$sparepart->pivot->price;
							$selqty=$sparepart->pivot->qty;

							echo '<tr><td class="vert-align">
							<input id="sppt'.$tmk_sparepartsCount.'ids" type="hidden" name="sppt['.$tmk_sparepartsCount.'][ids]" class= "tmksp_id"  value="'.$selid.'" />
							<button id="sppt'.$tmk_sparepartsCount.'del" class="removespptrow btn btn-default"><i class="glyphicon glyphicon-remove"></i> </button> '.$seltxt.'
							</td><td>
							<input id="sppt'.$tmk_sparepartsCount.'qty" type="text" name="sppt['.$tmk_sparepartsCount.'][qty]" class= "tmksp_qty form-control"  value="'.$selqty.'" /> 
							</td><td><td>
							<input id="sppt'.$tmk_sparepartsCount.'prc" type="text" name="sppt['.$tmk_sparepartsCount.'][prc]" class= "tmksp_price currency form-control"  value="'.$selprc.'" /> 
							</td>';
							$tmk_sparepartsCount++;
						}
						// TEMPLATE (iz tmkjs.js) kraj tablice 
						echo '</tbody></table>';
					}
					?>
				</div>

	</div>
</div>

<hr />

<!-- spremi samo ako je nova ili otvorena primka -->
@if($adminUser || (!empty($primka->closed) && $primka->closed == 0)) 
<div class="row">
<div class="col-sm-5">

	@if((!empty($primka->closed) && $primka->closed > 0) )
		<!-- samo admin --> 
		<label>primka zatvorena: </label>
	@endif
		<div class="input-group">
		<span class="input-group-btn">
			{!! Form::submit('Spremi', array('class' => 'btn btn-primary')) !!}
		</span>
		{!! Form::select('closed', array("0"=>"i ostavi primku otvorenom", "1"=>"i zatvori primku"), null, array('class' => 'form-control')) !!}
	<span class="input-group-btn">
		<a href="{{URL::to('primke')}}" class='btn btn-success'>Odustani</a>
	</span>

</div>
</div>
</div>
@endif



{!! Form::close() !!}


@stop