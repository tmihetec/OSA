@inject('tmkservisi', 'App\Services\tmkservisi')

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <title>STS</title>

	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
	<link rel="icon" href="/favicon.ico" type="image/x-icon">


    <!-- LIBS css -->
    <link rel="stylesheet" type="text/css" href="{{asset('inc/libs/Bootstrap-3.3.7-cust/css/bootstrap.min.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{asset('inc/libs/DataTables-1.10.9/css/dataTables.bootstrap.min.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{asset('inc/libs/FixedHeader-3.1.1/css/fixedHeader.bootstrap.min.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{asset('inc/libs/Buttons-1.0.3/css/buttons.bootstrap.min.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{asset('inc/libs/Responsive-1.0.7/css/responsive.bootstrap.min.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{asset('inc/libs/Scroller-1.3.0/css/scroller.bootstrap.min.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{asset('inc/libs/Select-1.0.1/css/select.bootstrap.min.css')}}"/>
    


		<!-- SELECT2 js plugin -->
		<link href="/inc/css/select2.min.css" rel="stylesheet" />
		<!-- SELECT2 bootstrap integration  

		<link href="/inc/css/select2-bootstrap.css" rel="stylesheet" />
    -->


		<!-- custom css -->
		<link href="/inc/css/custom.css" rel="stylesheet" />

	


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
	

    <!-- moram inicijalizirati varijable za velike tablice -->
    <script>
    var dtme2DArray = new Array();
    </script>


    
 	
  </head>
  
  <body>


    <!--  force reload start! ======================================================================= --> 

    <input type="hidden" id="tmkReloadTest" value="reloaded" />
    <script>
      if(document.getElementById('tmkReloadTest').value!=="reloaded") {
        document.body.className = 'hidden';
        location.reload();
      }
    /* na stranicu koju treba nasiliti na reload treba staviti: */
    //window.onbeforeunload = function (ev) { document.getElementById("tmkReloadTest").value = "fromcache"; }
    </script>


    <!-- force reload end =========================================================================== -->


			 
		<div class="container">

      <!-- Static navbar -->
      <nav class="navbar navbar-inverse"  role="navigation" >
        <div class="container-fluid">

          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{ URL::to('dashboard') }}"><img src="{{asset('inc/img/logomali.png')}}" style="margin-top:-5px; display:inline-block; margin-right:5px"  alt="STS OSA" />STS OSA</a>
          </div>

          <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
            <!--
              <li class="active"><a href="#">Home</a></li>
              <li><a href="#">About</a></li>
              <li><a href="#">Contact</a></li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
                <ul class="dropdown-menu">
                  <li><a href="#">Action</a></li>
                  <li><a href="#">Another action</a></li>
                  <li><a href="#">Something else here</a></li>
                  <li role="separator" class="divider"></li>
                  <li class="dropdown-header">Nav header</li>
                  <li><a href="#">Separated link</a></li>
                  <li><a href="#">One more separated link</a></li>
                </ul>
              </li>
            -->
            </ul>

            <ul class="nav navbar-nav navbar-right">
	            @if(!$tmkservisi->restrictedSppRole())
              <li class="dropdown">
	            	<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="glyphicon glyphicon-stats"></i> Izvještaji <span class="caret"></span></a>
		            <ul class="dropdown-menu">
        	     	 	<li ><a href="{{ URL::to('reportOne') }}">Kompletni</a></li>
                  <li ><a href="{{ URL::to('reportOtprema') }}">Otprema</a></li>
                  <li class="disabled"><a href="#">Prijem</a></li>
                  <li><a href="{{ URL::to('reportRealizacija') }}">Realizacija</a></li>
                  <li><a href="{{ URL::to('reportRealizacijaDetaljno') }}">Realizacija detaljno</a></li>
        	     	</ul>
              </li>
            	<li class="dropdown">
	            	<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="glyphicon glyphicon-tag"></i> Subjekti <span class="caret"></span></a>
		            <ul class="dropdown-menu">
      		         	<li ><!--class="active"--><a href="{{URL::to('komitenti')}}">Komitenti <!--<span class="sr-only">(current)</span>--></a></li>
        	     	   	<li ><a href="{{ URL::to('prodajnamjesta') }}">Prodajna mjesta / SPP</a></li> <!-- class="disabled" -->
        	     	 	  <li ><a href="{{ URL::to('brandovi') }}">Brandovi</a></li>
        	     	 	  <li ><a href="{{ URL::to('modeli') }}">Modeli uređaja</a></li>
        	     	 	  <li ><a href="{{ URL::to('tipovi') }}">Tipovi uređaja</a></li>
                    <li ><a href="{{ URL::to('rezervnidijelovi') }}">Rezervni dijelovi</a></li>
                    <li ><a href="{{ URL::to('usluge') }}">Usluge</a></li>
                    <li class="disabled"><a href="{{ URL::to('skladista') }}">Skladišta</a></li>
                    <li ><a href="{{ URL::to('zaposlenici') }}">Zaposlenici</a></li>
      	     	 	    <li ><a href="{{ URL::to('gradovi') }}">Gradovi</a></li>
        	     	</ul>
            	</li>
            	<li class="dropdown">
	                	<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="glyphicon glyphicon-plus-sign"></i> Robno <span class="caret"></span></a>
		                <ul class="dropdown-menu">
    		         	    <li ><!--class="active"--><a href="{{URL::to('primke')}}">Primka <!--<span class="sr-only">(current)</span>--></a></li>
        	     	 	     <li class="disabled"><a href="{{ URL::to('prijenos') }}">Međuskladišnica</a></li>
            	    	</ul>
          	  </li>
              @endif
	             <li class="dropdown">
	            	<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="glyphicon glyphicon-th-list"></i> Radni nalozi <span class="caret"></span></a>
		            <ul class="dropdown-menu">
    		         	<li ><!--class="active"--><a href="{{URL::to('slucaj')}}">Otvoreni radni nalozi <!--<span class="sr-only">(current)</span>--></a></li>
                  <li ><!--class="active"--><a href="{{URL::to('slucaj/arhiva')}}">Arhiva naloga <!--<span class="sr-only">(current)</span>--></a></li>
        	     	 	<li><a href="{{ URL::to('slucaj/create') }}">Novi radni nalog</a></li>
                  @if(!$tmkservisi->restrictedSppRole())
                    <li ><a href="{{URL::to('t2nalozi')}}">T2 nalozi na čekanju <!--<span class="sr-only">(current)</span>--></a></li>
    		         	  <li ><a href="{{URL::to('SPPnalozi')}}">SPP nalozi na čekanju <!--<span class="sr-only">(current)</span>--></a></li>
                  @endif
        	     	</ul>
              	</li>
              	
              	<li class="dropdown" >
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="glyphicon glyphicon-user"></i> {{Auth::user()->user}} <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li class="disabled"><a href="#">User Profile</a>
                        </li>
                        <li class="disabled"><a href="#">Settings</a>
                        </li>
                        <li class="hidden-xs divider"></li>
                        <li><a href="{{ URL::to('logout')}}">Logout</a>
                        </li>
                    </ul>
                </li>


                @if(!$tmkservisi->restrictedSppRole())
              	<li class="hidden-xs" style="background-color:#333">
                    <a class="dropdown-toggle"  data-toggle="tooltip" data-placement="bottom" title="T2 Nalozi" data-toggle="dropdown" href="{{ URL::to('t2nalozi')}}">
                        <span class="label label-info" style="background-color:#0066ff;">{{$tmkservisi->t2ordercount()}}</span> 
                    </a>
                </li>
                <li class="hidden-xs" style="background-color:#333">
                    <a class="dropdown-toggle"  data-toggle="tooltip" data-placement="bottom" title="SPP Nalozi" data-toggle="dropdown" href="{{ URL::to('SPPnalozi')}}" >
                        <span class="label label-primary" style="background:#ff6600;">{{$tmkservisi->SPPordercount()}}</span> 
                    </a>
                </li>
                @endif


            </ul>

          </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
      </nav>

		


				@yield('container')
		
						

		<footer>
			<hr />
			<small>IMSTAT // 2015 // 33pt // V1.5.1370</small>
			<?php
			/*
				1000 - initial release
				1001 - na zahtejv, IMEI polje je tekst polje (nije numeric) - nije im radio "right click + paste"
				1002 - dodan pagination i search po IMEI i H18 nalogu
				1003 - doadatrazen - greška u spellingu. nije prikazivao dobro da li je servis unutar 8D. treba biti "doada-P-trazen"
				1004 - prebačeno na laravel 5
				1005 - CRUD slučaja radi... prva upotrebljiva verzija
				(...)
				1025 - prebačeno na laravel 5.1
				1026 - hrpa izmjena - statusi, history, nema STS dijela na CREATE formi 
				(...)
				1100 - hrpa izmjena - dashboard beta, t2 nalozi tablica, create: tip uređaja, komitent...
				1200 - hrpa izmjena - responsive DT, reportOne
        (...)
        1315 - u configu - app -> timezone na CET (umjesto UTC), dodan print i colvis gumb, svi library i pluginovi u LIBS
        1347 -  1) broj naloga počinje s "SPP_id" sppa gdje se nalog otvorio (HR1, HR2, SI1...)
                2) PDF prijemni list
                3) Dodana slovenija kao servicelocation
                4) Nakon kreiranja novog naloga čovjeku se osim broja naloga daje mogućnost printa i edita. (bolja varijanta nego direktan print - to ima problem s BACK gumbom)
                5) Izvještaj->otprema, dorađen... između ostalog, rješen bug sa ne brisanjem reda u responsive prikazu
                6) dodan tooltip kod Ex. servisera na nalogu
                7) OTVORENI RADNI NALOZI (svi koji nisu otpremljeni)
                8) ZATVORENI RADNI NALOZI (svi na bilo koji način otpremljeni nalozi)
                9) na popisu ima sada printview NALOGA i PRIJEMNOG LISTA
        1359 - SPP user - vidi samo naloge koji su otvoreni na njegovom SPP-u, 
             - permissioni prvi put
        1368 - zaposlenici admin
        1369 - bug sa dvostrukim snimanjem "DELETED" statusa - i u kontroleru, i u AppServiceProvider-u
        1370 - dodano brisanje "DELETED" statusa za superadmina

			*/
			?>
			
			
		</footer> 
						
		</div>		

    <!-- ajax loading indicator -->
    <img id="loading-indicator" style="display:none" src="{{asset('inc/img/gears.svg')}}"  />
		
    <!-- LIBS js -->
    <script type="text/javascript" src="{{asset('inc/libs/jquery-3.1.0.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('inc/libs/Bootstrap-3.3.7-cust/js/bootstrap.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('inc/libs/JSZip-2.5.0/jszip.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('inc/libs/pdfmake-0.1.18/build/pdfmake.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('inc/libs/pdfmake-0.1.18/build/vfs_fonts.js')}}"></script>
    <script type="text/javascript" src="{{asset('inc/libs/DataTables-1.10.9/js/jquery.dataTables.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('inc/libs/DataTables-1.10.9/js/dataTables.bootstrap.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('inc/libs/Buttons-1.0.3/js/dataTables.buttons.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('inc/libs/Buttons-1.0.3/js/buttons.bootstrap.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('inc/libs/Buttons-1.0.3/js/buttons.colVis.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('inc/libs/Buttons-1.0.3/js/buttons.flash.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('inc/libs/Buttons-1.0.3/js/buttons.html5.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('inc/libs/Buttons-1.0.3/js/buttons.print.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('inc/libs/Responsive-1.0.7/js/dataTables.responsive.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('inc/libs/Scroller-1.3.0/js/dataTables.scroller.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('inc/libs/Select-1.0.1/js/dataTables.select.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('inc/libs/FixedHeader-3.1.1/js/dataTables.fixedHeader.min.js')}}"></script>



			<!-- DT moment 
			<script type="text/javascript" charset="utf8" src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script> -->
			<script type="text/javascript" charset="utf8" src="{{asset('inc/js/moment.min.js')}}"></script>
			<!-- DT moment plugin 
			<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/plug-ins/1.10.8/sorting/datetime-moment.js"></script> -->
			<script type="text/javascript" charset="utf8" src="{{asset('inc/js/datetime-moment.js')}}"></script>



		<!-- bootstrapConfirmation -->
		<script type="text/javascript" charset="utf8" src="{{asset('inc/js/bootstrap-confirmation/bootstrap-confirmation.min.js')}}"></script>
		
		<!-- SELECT2 js plugin -->
		<script src="{{asset('inc/js/select2.full.min.js')}}"></script>

		<!-- MASKED INPUT js plugin -->
		<script src="{{asset('inc/js/jquery.maskedinput.min.js')}}"></script>
		
    <!-- Sweet alert js plugin -->
    <script type="text/javascript" src="{{asset('inc/js/bootbox.min.js')}}"></script>

		<!-- Custom -->
    <script type="text/javascript" src="{{asset('inc/js/tmkjs.js')}}"></script>
		<script type="text/javascript" src="{{asset('inc/js/tmkjs-modalAjaxButtons.js')}}"></script>



    <!-- App scripts -->
    @stack('scripts')			

<?php /*
<script>
$(window).on('pageshow', function (event) { if(event.originalEvent.persisted) { location.reload(); }});

</script>
*/?>

  </body>
</html>




		
