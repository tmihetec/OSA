<!DOCTYPE html>
<html>
<head>
    <title>h18Tele2Service</title>
<!--    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">-->
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">

</head>
<body>
<div class="container">




    <div class="jumbotron text-center">
		
		{!! Form::open(array('url'=>'getmwrpt')) !!}
		<div class="row">
			<div class="col-lg-4 col-lg-offset-4">
					
					{!! Form::submit('dohvati report', array('class' => 'btn btn-lg btn-primary')) !!}
			</div><!-- /.col-lg-4 -->
		</div><!-- /.row -->
		
		{!! Form::close() !!}
    </div>


	
</div>
</body>
</html>