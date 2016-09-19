<!DOCTYPE html>
<html lang="en">
<head>
	<meta	http-equiv="Content-Type"	content="charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>STS: radni nalog</title>


	<style type="text/css">

		.page-break {
			page-break-after: always;
		}
		html{
			background-color: #ffffff;
		}
		body{
			font-family: sans-serif;
			font-size: 12px;
			line-height: 1.1em;
			background-color: #ffffff;
		    font-family:  "dejavu sans condensed", sans-serif; /* */
		}
		.normalni{
			font-weight: normal;
			font-size: 11px;
			line-height: 16px;
		}
		th {    		
			font-size: 14px;
			line-height: 16px;
		}
	</style>

	<script type="text/javascript" >
		//window.onload = function() { window.print(); }
	</script>

</head>
<body>

	<table width="100%">
		<tr>
			<th style="text-align:left" width="30%"><img src="{{asset('inc/img/report_samologo.jpg')}}" style="width:3cm; padding-bottom:5px"/></th>
			<th style="text-align:right; font-weight:normal; font-size:11.4px; line-height:12px;" width="70%"><strong>Smartphone i tablet servis d.o.o. </strong><br />
				<span>MBS 080957903; OIB 51430141183; IBAN HR7123400091110719276<br />
					<strong>Logistički centar i servis:</strong> Braće Wolf 5, 48000 Koprivnica tel: 01/3877-213, 048/864-348<br />
					<strong>Servis:</strong> Josipa Slavenskog 1, TPC Prečko, II kat, lokal 162, 10000 Zagreb, tel: 01/3877-209<br /></span>
				</th>
			</tr>
			<tr><th colspan="2" style="border-top:1px solid #000"></th></tr>
		</table>

		<table width="100%" style="margin:20px 0px 20px 0px" cellspacing="0" cellpadding="0">
			<tr>
				<td width="48%">


					<table width="100%" cellspacing="0" cellpadding="0">
						<tr >
							<td style="text-align:left; font-size:28px; line-height:28px; padding:0; margin:0px 0px 20px 0px">
								<strong style="font-size:.8em; line-height:20px">Radni nalog:</strong><br/> {{$slucaj->stsrepairorderno}}<br />
								{!! DNS1D::getBarcodeHTML($slucaj->stsrepairorderno, "C128", 2,25) !!}
							</td>
						</tr>
						<tr>
							<td style="font-size:14px; padding-top:7px; padding-bottom:7px;">

								<strong>Datum radnog naloga: </strong>{{ (empty($slucaj->stsroopendate) ) ? null : $slucaj->stsroopendate->format("d.m.Y") }}
								<br />
								<strong>Status: </strong><span style="font-size:1.1em">{{ (!empty($slucaj->repairstatuses->first()->name)) ? $slucaj->repairstatuses->first()->name : "" }}</span> 	
							</td>
						</tr>

						@if (!$zaprimanje)
						<tr>
						<td style="border:1px solid #ccc;  border-bottom:0; padding:0px 5px 2px 5px">			<!-- ako je status samo ZAPRIMLJENO, onda se neispisuje!! -->
								<strong style="font-size:13px">{{($slucaj->stsfailuredetected==1)? "Nedostatak uočen" 
									: 
									"Nedostatak nije pronađen"}}
									@if ($slucaj->stsdeviceswap == 1)
									{{" - zamjena uređaja"}}
									@endif

									</strong>
						</td>
						</tr>
						@endif

						<tr>
							<td style="border:1px solid #ccc;  padding:2px 5px">			<!-- ako je status samo ZAPRIMLJENO, onda se neispisuje!! -->
								@if (!$zaprimanje)
								<strong style="font-size:13px">{{($slucaj->devicewarranty==1)? "Uređaj u jamstvu" : "Uređaj nije u jamstvu"}}</strong> 
								<br />
								@endif
								<div style=" line-height:1em; padding:2px 0px 4px 0px; ">
								Datum i broj računa: {{(empty($slucaj->devicebuydate) ) ? "-" : $slucaj->devicebuydate->format("d.m.Y")}} / {{(empty($slucaj->deviceinvoiceno) ) ? "-" : $slucaj->deviceinvoiceno}}<br />
								Mjesto kupnje uređaja: {{$buyplace}}<br />
								Broj jamstvenog lista: {{(empty($slucaj->devicewarrantycardno) ) ? "-" : $slucaj->devicewarrantycardno}}
								</div>
							</td>

						</tr>
					</table>


				</td>
				<td width="10%"></td>

				<td style="border:1px solid #ccc; padding:0px 10px 10px 10px; font-size:14px" width="42%" >
					<h2 style="font-size:16px; font-weight:bold; padding:0px; line-height:14px; margin:0px 0px 5px 0px">Korisnik</h2>
					<div style="padding-bottom:10px;">
						<strong>{{ $slucaj->customername }}<br />
							{{ $slucaj->customerlastname }}<br />
						</strong>
						Adresa: {{$slucaj->customerstreet.", ".$slucaj->grad->postalcode." ".$slucaj->grad->name}}<br />
						Tel:  {{ $slucaj->customerphone1 }}<br />
						Email: {{ $slucaj->customeremail }}
					</div>
					<h2 style="font-size:16px; font-weight:bold; padding:8px 0px 0px 0px; line-height:14px; margin:0px 0px 5px 0px; border-top:1px solid #ccc">Mjesto predaje uređaja</h2>
					{{$slucaj->pos->custom_name}}<br />
					Datum zaprimanja:{{(empty($slucaj->posclaimdate) ) ? null : $slucaj->posclaimdate->format("d.m.Y")}}
				</td>
			</tr>
		</table>


		<table width="100%" style="margin:20px 0px 20px 0px" cellpadding="0" cellspacing="0" >
			<tr >
				<td width="48%" valign="top" style="border:1px solid #ccc; padding:5px 10px; border-right:0px">
					<h2 style="margin-top:0px;  margin-bottom:5px; font-size:14px; line-height:16px;">{{$slucaj->model->brandmodel}}</h2>
					<div style="border-top:1px solid #ccc">
					Ulazni IMEI/SN: {{$slucaj->deviceincomingimei}}<br />
					{!! DNS1D::getBarcodeHTML($slucaj->deviceincomingimei, "C128", 2,25) !!}<br />
					</div>
					<div style="border-top:1px solid #ccc">
					@if ($zaprimanje)
					Izlazni IMEI/SN: /<br />
					@else 
					Izlazni IMEI/SN: {{$slucaj->deviceoutgoingimei}}<br />
					{!!  (isset($slucaj->deviceoutgoingimei) && !empty($slucaj->deviceoutgoingimei) && trim($slucaj->deviceoutgoingimei) !== "") ? DNS1D::getBarcodeSVG($slucaj->deviceoutgoingimei, "C128", 2,25) :"" !!}
					@endif
					</div>
				</td>
				<td width="24%" valign="top" style="border:1px solid #ccc; padding:5px 10px; border-right:0px;">
					<h2 style="margin-top:0px; margin-bottom:5px; font-size:14px; line-height:16px;">Pribor</h2>
					{{$pribor}}
				</td>
				<td width="28%" valign="top" style="border:1px solid #ccc; padding:5px 10px">
					<h2 style="margin-top:0px; margin-bottom:5px;  font-size:14px; line-height:16px;">Opis neispravnosti</h2>
					{{$slucaj->devicefailuredescription}}
				</td>
			</tr>
		</table>


		<table width="100%" style="margin:10px 0px 15px 0px" cellpadding="0" cellspacing="0" >
			<tr >
				<td valign="top" style="text-align:left; border:1px solid #ccc;height:110px; padding:5px 10px 0px 10px; overflow:hidden;">
					<h2 style=" font-size:14px; margin:0px 0px 5px 0px; padding:0px">Izvješće servisera:</h2>

					{{$slucaj->stsnotice}}
				</td>
				@if($dijeloviusluge)
				<td valign="top" width="50%" style="border:1px solid #ccc; border-left:0; padding:5px; ">
					<h2 style="margin:0px 0px 5px 0px; font-size:14px; padding:0px">Dijelovi i usluge:</h2>
					<table width="100%" style="border:1px solid #999; border-bottom:0; border-left:0; border-right:0" cellpadding="0" cellspacing="0">
						<tr>
							<th width="5%" style="text-align:left; border-bottom:1px solid #ccc; font-size:11px; padding-left:3px">br</th>
							<th width="45%" style="text-align:left; font-size:11px; border-bottom:1px solid #ccc">naziv</th>
							<th width="15%" style="text-align:left; font-size:11px; border-bottom:1px solid #ccc">jm</th>
							<th width="15%" style="text-align:left; font-size:11px; border-bottom:1px solid #ccc">kol</th>
							<th width="20%" style="text-align:left; font-size:11px; border-bottom:1px solid #ccc">iznos</th>
						</tr>
						@foreach ($dijeloviusluge as $key => $value)
						<tr>
							<td style="border-top:1px solid #ccc; padding-left:3px">{{($key+1)."."}}</td>
							<td style="border-top:1px solid #ccc">{{$value['naziv']}}</td>
							<td style="border-top:1px solid #ccc">{{$value['jm']}}</td>
							<td style="border-top:1px solid #ccc">{{number_format($value['qty'],2,",",".")}}</td>
							<td style="border-top:1px solid #ccc">{{number_format($value['prc'],2,",",".")}}</td>
						</tr>
						@endforeach
					</table>
				</td>
				@endif
			</tr>
			<tr>
				<td style="border:1px solid #ccc; font-size:12px; border-top:0; padding:2px 5px 0px 5px;">
					@if(!is_null($datumzavrsetka))
					<div style="padding-bottom:2px">
						<strong>Završetak:</strong> {{$datumzavrsetka->format("d.m.Y")}}  | <strong>Serviser:</strong>  
						{{$slucaj->serviser->first_name." ".substr($slucaj->serviser->last_name,0,1)}} </div>
						@endif
					</td>

					@if($dijeloviusluge)
					<td style="border:1px solid #ccc; border-top:0; border-left:0; font-size:13px; font-weight:normal; padding:5px 5px 5px 5px;">Ukupna cijena servisa: <span style="font-weight:bold">{{number_format($ukupnacijena,2,",",".")}}kn</span> (PDV uključen)</td>
					@endif
				</tr>
			</table>


			<table width="100%" style="margin-top:10px; margin-bottom:15px">
				<tr><td width="50%" valign="top">
					Dokument izradio: <br />
					<div style="text-align:left; border-bottom:1px solid #000; width:50%; height:50px"></div>

					{{$izradio}}, {{ date("d.m.Y")}}<br />

					</td>
					<td valign="top">
						Otprema: 	@if($slucaj->devicereturntype_id == 4)
								{{ $slucaj->adresa_otpreme}}
							@else
								{{ $slucaj->otprema->name}}
							@endif
					</td>
				</tr>
			</table>

	<div style="border:1px solid #000; padding:10px;">
		@if(false && $popravakgotov)
		Uređaj je testiran. Svi sustavi su ispravni i rade u okviru specifikacija proizvođača.
		@endif
		Dokument je izrađen elektronskim putem i pravovaljan je bez pečata.
	</div>

	</body>
	</html>