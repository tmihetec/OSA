<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>STS OSA</title>

    <!-- Bootstrap -->
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
   <style>
		.form-signin{max-width:330px;margin:0 auto;padding:15px;}
		.form-signin .form-signin-heading,.form-signin .checkbox{margin-bottom:10px;}
		.form-signin .checkbox{font-weight:normal;}
		.form-signin .form-control{position:relative;font-size:16px;height:auto;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;padding:10px;}
		.form-signin .form-control:focus{z-index:2;}
		.form-signin input[type="text"]{margin-bottom:-1px;border-bottom-left-radius:0;border-bottom-right-radius:0;}
		.form-signin input[type="password"]{margin-bottom:10px;border-top-left-radius:0;border-top-right-radius:0;}
		.account-wall{margin:20px 0px 0px 0px;background-color:#f7f7f7;-moz-box-shadow:0 2px 2px rgba(0,0,0,0.3);-webkit-box-shadow:0 2px 2px rgba(0,0,0,0.3);box-shadow:0 1px 6px rgba(0,0,0,0.3);padding:40px 0 5px 0px;}
		.profile-img{width:170px;height:105px;display:block;
				/*-moz-border-radius:50%;-webkit-border-radius:50%;border-radius:50%;*/
				margin:0 auto 10px;}
		.account-wall h1{text-align:center; border-bottom:1px dotted #999; }
		.logged{background-color:#fff0f0; max-width:330px;margin:0 auto;padding:15px; margin-top:20px;}
		.msg_container{padding:10px 0 20px;}
	</style>
	

	

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
	
 	
  </head>
  <body>
			 
    <div class="container">

<?php	// echo $password = Hash::make('tomek'); ?>


<div class="row">
        <div class="col-sm-6 col-md-4 col-md-offset-4">
						
					
					@if (Auth::check()) 
					
						<div class="account-wall logged">
							<h1>{!! link_to_route('slucaj.index', 'STS OSA') !!}</h1>
							<div class="well well-sm"><span>Prijavljeni korisnik: </span><strong> {{ Auth::user()->user }}</strong></div>
							<div>{!! link_to_route('logout', 'Odjava ', null, array('class'=>'btn btn-lg btn-danger btn-block')) !!}</div>
						</div>
						
					@else 
					
						<div class="account-wall">
							<img class="profile-img" src="{{asset('inc/img/logoW.png')}}" alt="STS OnlineServiceApplication" />

									{!! Form::open(array('url' => 'auth/login', 'role'=>'form', 'class'=>'form-signin') ) !!}
										<h1>STS OSA</h1>
										{!! Form::text('user', null, array('placeholder' => 'korisnik', 'class'=>'form-control ')) !!}
										{!! Form::password('password' , array('placeholder' => 'lozinka', 'class'=>'form-control')) !!}
										{!! Form::submit('Prijava', array('class' => 'btn btn-lg btn-primary btn-block')) !!}
										
										<hr />
										<div class="clearfix">
												<input type="checkbox" name="remember" value="remember-me">
												Zapamti me
					
											<a class="pull-right" style="padding-left:17px;" href="{{ URL::to('password/reset') }}">
												Zaboravljena lozinka?
											</a>
										</div>
									{!! Form::close() !!}
									
						<div>
<?php
/*
								{{ $errors }}
*/
?>								
							</div>

							
							
							
						</div>
						<div class='msg_container'>
							@if ($errors->any()) 
								<div class="alert alert-danger alert-dismissible" role="alert">
									<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Zatvori</span></button>
									<!-- validation errors -->						
									{!! (trim($errors->first('user'))!=="") ? $errors->first('user') . '<br />' : '' !!}
									{!! $errors->first('password') !!}
								</div>
							@endif

							@if (Session::has('message-error'))
								<div class="alert alert-danger alert-dismissible" role="alert">
									<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Zatvori</span></button>
									<!-- login error message -->
									{!! Session::get('message-error') !!}
									</div>
							@endif						
						</div>
						
						
					@endif

        </div>
		
    </div>
    </div>
	

			
			
  </body>
</html>
