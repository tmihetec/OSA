@section('searchForm')

		<!-- TRAŽENJE UREĐAJA -->
		<h3>Traži uređaj</h3>
		
		{{ Form::open(array('method' => 'GET', 'class'=>'form-inline', 'role'=>'form', 'route' => array('imeis.search')  )) }} 
		  <div class="form-group">
					{{ Form::text('search_imei', null, array('class'=>'form-control', 'placeholder'=>'Unesi IMEI') ) }}
		  </div>
		  <div class="form-group">
			<div class="input-group">
			  <div class="input-group-addon">@</div>
				{{ Form::email('search_email', null, array('class'=>'form-control', 'placeholder'=>'Unesi eMail korisnika') ) }}
			</div>
		  </div>
<!-- 		  
		  <div class="form-group">
			  <div class="checkbox">
					{{ Form::label('search_xtrafields', 'SAMO_SA filterima') }}					
					{{ Form::checkbox('search_xtrafields', '1', false ) }}			
			  </div>	  
			  <div class="checkbox">
					
					{{ Form::label('search_potvrdjenaRegistracija', 'Registracija potvrđena?') }}
					{{ Form::checkbox('search_potvrdjenaRegistracija', '1', array() ) }}
			  </div>
			  <div class="checkbox">
					hidden za checkbox fix 
					
					{{ Form::label('search_prihvacenoJamstvo', 'Prihvaćeno 1+1?') }}
					{{ Form::checkbox('search_prihvacenoJamstvo', '1', array() ) }}
			  </div>
		  </div>
-->
		{{ Form::submit('Traži', array('class' => 'btn btn-primary')) }}
		{{ Form::close() }}

		<hr />
		
		
		
		
		

@stop