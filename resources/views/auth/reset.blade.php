<!-- resources/views/auth/reset.blade.php -->




<!-- resources/views/auth/password.blade.php -->






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
        .form-signin input[type="email"]{margin-bottom:-1px;border-bottom-left-radius:0;border-bottom-right-radius:0;}
        .form-signin input.mid{margin-bottom:-1px;border-radius:0;}
        .form-signin input.mb{margin-bottom:10px;border-top-left-radius:0;border-top-right-radius:0;}

        .account-wall{margin:20px 0px 0px 0px;background-color:#f7f7f7;-moz-box-shadow:0 2px 2px rgba(0,0,0,0.3);-webkit-box-shadow:0 2px 2px rgba(0,0,0,0.3);box-shadow:0 1px 6px rgba(0,0,0,0.3);padding:40px 0 5px 0px;}
        .profile-img{width:170px;height:105px;display:block;
                /*-moz-border-radius:50%;-webkit-border-radius:50%;border-radius:50%;*/
                margin:0 auto 0px;}
        .account-wall h2{text-align:center; border-bottom:1px dotted #999; margin-bottom: 20px; }
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
<div class="col-sm-6 col-md-4 col-md-offset-4">
                        

                    
<div class="account-wall">

                            <img class="profile-img" src="{{asset('inc/img/logoW.png')}}" alt="STS OnlineServiceApplication" />


<form method="POST" action="/password/reset"  role="form" class="form-signin">
                                {!! csrf_field() !!}
    <input type="hidden" name="token" value="{{ $token }}">
                                <h2>Zaboravljena lozinka</h2>
@if(session('status'))
    {{session('status')}}
    
@else


        <input type="email" placeholder="email adresa" class="form-control"  name="email" value="{{ old('email') }}">

        <input type="password" placeholder="lozinka" class="form-control mid"  name="password">
        <input type="password" placeholder="ponovi lozinku" class="form-control mb"  name="password_confirmation">


            <input type="submit" class="btn btn-lg btn-primary btn-block" value="Izmjeni lozinku"/>
            
                <hr />
                <div class="clearfix">
                       <a href="{{URL::to('auth/login')}}" class="pull-right">Login forma</a>
                </div>

@endif

                            </form>

</div>



    
                        <div class='msg_container'>
                        <!-- if there are creation errors, they will show here -->
                        {!! HTML::ul($errors->all(),array('class'=>'list-unstyled alert alert-danger', 'role'=>'alert')) !!}
                        </div>
                        

                        

</div>        
</div>
</div>
    

            
            
  </body>
</html>












