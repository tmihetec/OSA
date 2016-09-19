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
			line-height: 18px;
			background-color: #ffffff;
			font-family:  sans-serif; /* */
		}
		.normalni{
			font-weight: normal;
			font-size: 11px;
			line-height: 16px;
		}
		th {    		
			font-size: 14px;
			line-height: 18px;
		}
	</style>

	<script type="text/javascript" >
		//window.onload = function() { window.print(); }
	</script>

</head>
<body >

		<table width="100%">
			<tr>
				<th style="text-align:left" width="30%"><img src="{{asset('inc/img/report_samologo.jpg')}}" style="width:3cm; padding-bottom:5px"/></th>
				<th style="text-align:right" width="70%"><strong>Smartphone i tablet servis d.o.o. </strong><br />
					<span class="normalni">MBS 080957903; OIB 51430141183; IBAN HR7123400091110719276<br />
					<strong>Logistički centar i servis:</strong> Braće Wolf 5, 48000 Koprivnica tel: 01/3877-213, 048/864-348<br />
					<strong>Servis:</strong> Josipa Slavenskog 1, TPC Prečko, II kat, lokal 162, 10000 Zagreb, tel: 01/3877-209<br /></span>
				</th>
			</tr>
			<tr>
				<th colspan="2" style="border-top:1px solid #000"></th>
			</tr>
		</table>

		<div style="height:20px"></div>

		<table width="100%" cellspacing="0" cellpadding="0">
			<tr >
				<td width="58%" >
					<div style="text-align:left; font-size:32px; line-height:30px; padding:0;">
						<div style="font-weight:bold; font-size:1.3em; line-height:1.3em">Prijemni list</div>
						<div style="font-size:.55em">za radni nalog {{$slucaj->stsrepairorderno}}</div>
						<div style="line-height:1em; ">{!! DNS1D::getBarcodeSVG($slucaj->stsrepairorderno, "C128", 2,25) !!}</div>
						<div style="font-size:16px; line-height:.9em;padding-top:10px;">
							<strong>Datum zaprimanja: </strong>{{ (empty($slucaj->stsroopendate) ) ? null : $slucaj->stsroopendate->format("d.m.Y") }}
						</div>
						<div style="font-size:16px; padding:0px; margin:0">
							<strong>Za servisno mjesto: </strong>
							{{$slucaj->servicelocation->posname}}
						</div>
					</td>

					<td width="42%" >
						<h2 style="margin-top:0px; padding:0px; margin-bottom:10px; font-weight:normal; font-size:12px; line-height:14px;">Krajnji korisnik</h2>

						<div style="font-size:14px; padding-bottom:10px;">
							<strong>{{ $slucaj->customername }} {{ $slucaj->customerlastname }}</strong><br />
							Adresa: {{$slucaj->customerstreet.", ".$slucaj->grad->postalcode." ".$slucaj->grad->name}}<br />
							Tel:  {{ $slucaj->customerphone1 }}<br />
							Email: {{ $slucaj->customeremail }}
						</div>
						<h2 style="font-size:12px; font-weight:normal; padding:10px 0px 0px 0px; line-height:14px; margin:0px 0px 10px 0px; border-top:1px solid #ccc">Mjesto predaje uređaja</h2>
						<div style="font-size:14px; ">
							<strong>{{$slucaj->pos->custom_name}}</strong><br />
							Datum:{{($slucaj->posclaimdate !==null ) ? null : $slucaj->posclaimdate->format("d.m.Y")}}
						</div>
					</td>
				</tr>
			</table>

			<div style="height:30px"></div>

			<table width="100%" style="border:1px solid #000;" cellpadding="0" cellspacing="0" >
				<tr >
					<td width="55%" valign="top" style="border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:5px 10px; ">
						<h2 style="margin-top:0px;  margin-bottom:5px; font-size:17px; line-height:16px;">{{$slucaj->model->brandmodel}}</h2>
						<div >
							IMEI/SN: {{$slucaj->deviceincomingimei}}<br />
							{!! DNS1D::getBarcodeSVG($slucaj->deviceincomingimei, "C128", 2,25) !!}
						</div>
					</td>
					<td width="45%" valign="top" style="border-bottom:1px solid #ccc; padding:5px 10px">
						<div>
							Mjesto kupnje uređaja: {{$buyplace}}
						</div>
						<div>
							Datum računa: {{(empty($slucaj->devicebuydate) ) ? "-" : $slucaj->devicebuydate->format("d.m.Y")}} 
						</div>
						<div>
							Broj računa: {{(empty($slucaj->deviceinvoiceno) ) ? "-" : $slucaj->deviceinvoiceno}}
						</div>
						<div>
							Broj jamstvenog lista: {{empty($slucaj->devicewarrantycardno) ? "-" : $slucaj->devicewarrantycardno}}
						</div>

					</td>
				</tr>
				<tr>
					<td style="border-bottom:1px solid #ccc; padding:5px 10px; " valign="top" colspan="2" >
						<div>
							<span style="margin-top:0px; margin-bottom:5px; font-weight:bold; font-size:12px; line-height:16px;">Pribor:</span>
							@if (trim($pribor)=="") 
							ništa
							@else
							{{$pribor}}
							@endif
						</div>
					</td>
				</tr>
				<tr>
					<td valign="top" colspan="2" style=" padding:5px 10px; ">
						<h2 style="margin-top:0px; margin-bottom:5px;  font-size:12px; line-height:16px;">Opis neispravnosti</h2>
						{{$slucaj->devicefailuredescription}}
					</td>
				</tr>
			</table>


			<div style="padding-top:10px; height:100px">
				<h2 style="margin-top:0px; margin-bottom:5px; font-size:12px; line-height:16px; text-decoration:underline;">Napomene</h2>
				<strong>Otprema uređaja: </strong>
				@if($slucaj->devicereturntype_id == 4)
				{{ $slucaj->adresa_otpreme}}
				@else
				{{ $slucaj->otprema->name}}
				@endif
				<br />

			</div>



			<div id="dno" style="position:absolute; top:20cm; bottom:0; left:0; height:100px">
				<table width="100%" style="margin-top:10px;">
					<tr>
						<td width="30%" valign="top">
							Za STS: <br />
							<div style="text-align:left; border-bottom:1px solid #000; height:50px"></div>
							{{$izradio}}, {{ date("d.m.Y")}}<br />
						</td>
						<td width="35%" valign="middle" style="text-align:center; font-size:.8em">
							MP
						</td>
						<td width="35%" valign="top">
							Potpis korisnika: <br />
							<div style="text-align:left; border-bottom:1px solid #000;  height:50px"></div>
							Suglasan sam s navedenim podacima<br />
						</td>
					</tr>
				</table>


				<div style="border:1px solid #000; padding:10px; margin-top:25px">
					Status radnog naloga možete provjeriti na internet adresi: <strong>http:\\servis.sts.hr</strong> unosom broja radnog naloga <strong>{{$slucaj->stsrepairorderno}}</strong> i IMEI odnosno serijskog broja <strong>{{$slucaj->deviceincomingimei}}</strong>.<br />
					Dokument je izrađen elektronskim putem i pravovaljan je bez pečata.
				</div>
			</div>

		</body>
		</html>