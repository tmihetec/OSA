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
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Reset Password</div>

                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/password/reset') }}">
                        {!! csrf_field() !!}

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input type="email" class="form-control" name="email" value="{{ $email or old('email') }}">

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input type="password" class="form-control" name="password">

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Confirm Password</label>
                            <div class="col-md-6">
                                <input type="password" class="form-control" name="password_confirmation">

                                @if ($errors->has('password_confirmation'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-refresh"></i>Reset Password
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>








            
  </body>
</html>
