<!DOCTYPE html>
<html>
<head>
    <title>STS / IMSTAT</title>
<!--    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">-->
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">

</head>
<body>
<div class="container">



    <div class="jumbotron text-center">
		<img class="profile-img" style="margin-bottom:10px;"src="{{asset('inc/img/logoW.png')}}" alt="STS OnlineServiceApplication" />
		<h1 class="hidden">STS</h1>
		<p><small>status radnog naloga</small></p>
		
		{!! Form::open(array('url'=>'imstat', 'class'=>'form-inline')) !!}

				<div class="form-group">
					<input type="text" name="caseid" id="caseid" value="" placeholder="RADNI NALOG" class="form-control input-lg" />
					</div>
				<div class="form-group">
					<input type="text" name="imei" id="imei" value="" placeholder="IMEI" class="form-control input-lg" />
					</div>
				<div class="form-group">
					{!! Form::submit('provjeri', array('class' => 'btn btn-lg btn-primary')) !!}
				</div><!-- /input-group -->
		
		{!! Form::close() !!}
    </div>

	@if (isset($imeidata))
	
	<div class="alert alert-info text-center" role="alert">

			<small>
			  <strong>Radni nalog:</strong> {{$nalog}}, <strong>IMEI:</strong> {{$imei}}</small>			
			  <hr />
			<h4>{{ $imeidata->repairstatuses->first()->name }}</h4>
			<hr />
			<div id="imstat_detalji">
			<small>
			  uređaj je predan na prodajno mjesto:{{ date("d.m.Y",strtotime($imeidata->posclaimdate)) }}, 
			  zadnja izmjena statusa: {{ date("d.m.Y",strtotime($imeidata->repairstatuses->first()->pivot->updated_at))}}
			  @if ($imeidata->posclaimtype_id == '2' | $imeidata->posclaimtype_id == '3')
				, DOA/DAP: 
				@if ($imeidata->stsdoadap == '1')
				 DA
				@else 
				 NE
				@endif
			  @endif
			</small>
			</div>
    </div>

	@endif
	
	<!-- if there are creation errors, they will show here -->
    {!! HTML::ul($errors->all(),array('class'=>'list-unstyled text-center alert alert-danger', 'role'=>'alert')) !!}
	
</div>
</body>
</html>