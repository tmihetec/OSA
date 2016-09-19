@extends('layouts.shell')
@section('container')

<!-- if there are creation errors, they will show here -->
{!! HTML::ul($errors->all(),array('class'=>'list-unstyled alert alert-danger', 'role'=>'alert')) !!}

    <h1>
        Otvoreni radni nalozi
        <a href="{{URL::to('/slucaj/create')}}" title="Novi nalog" class="label label-default pull-right" style="font-size:.5em; margin-top:10px" ><i class="glyphicon glyphicon-plus-sign"></i></a>
    </h1>
    <p class="notice">Popis aktivnih naloga u bazi. Ne uključuje najave T2 naloga putem web servisa i otpremljene naloge</p>

	<hr />
	
<!-- will be used to show any messages -->
@if (Session::has('message'))
    <div class="alert alert-info">{!! Session::get('message') !!}</div>
@endif

<table id="dataTableMe" class="table table-striped table-bordered table-hover dt-responsive" cellspacing="0" width="100%">
    <thead>
        <tr class="filterrow">
            <th>STS RN</th>
           <!-- <th>LOK.</th> class="columnfilterthis" -->
            <th>DATUM</th>
            <th>UREĐAJ</th>
            <th>IMEI</th> <!--  style="display:none" -->
            <th>POS</th>
            <th>POS RN</th> <!-- hide? -->
            <th>KORISNIK</th> <!-- hide? -->
            <th>ZADNJI STATUS</th>
            <th></th>
        </tr>

    </thead>
    <tbody>


<?php /*

    @foreach($slucajevi as $key => $value)

// V2 - idemo sa DATA propertijem datatablesa
// https://datatables.net/reference/option/data

        <tr>
            <td>{{ $value->stsrepairorderno }}</td> <!--$value->stsrepairorderno-->
            <td>{{ $value->servicelocation->name }}</td>
            <td>{{ $value->stsroopendate->format("d.m.Y") }}</td>
            <td>{{ $value->model->brand->name." ".$value->model->name }}</td>
            <td style="display:none">{{ $value->deviceincomingimei }}</td>
            <td>{{ $value->pos->posname }}</td>
            <td>{{ $value->posrepairorderno }}</td>
            <td>{{ $value->customername." ".$value->customerlastname }}</td>

           	<td>{{ (!empty($value->repairstatuses->first()->name)) ? $value->repairstatuses->first()->name : "fetch status error" }}</td>

            <td>
            	<span class="btn-group btn-group-xs" style="display:flex;" role="group">
					
					<a class="btn btn-small btn-warning" title="edit" href="{{ URL::to('slucaj/' . $value->id . '/edit') }}"><i class="glyphicon glyphicon-pencil"></i></a>

                    <!-- print -->
                    <a class="btn btn-small btn-info" title="print view" href="{{ URL::to('printView/rn/' . $value->id) }}"><i class="glyphicon glyphicon-eye-open"></i></a>

                    <!-- pdf -->
                    <a class="btn btn-small btn-primary" title="pdf" href="{{ URL::to('pdfView/rn/' . $value->id) }}"><i class="glyphicon glyphicon-file"></i></a>

					<!--
                		trebalo bi napraviti default stranicu za delete, sa prikazom i ponovo delete gumbom. (ako ne radi js)
    					http://www.laravel-tricks.com/tricks/delete-links-with-confirmation-box-in-jquery
                    -->

                    @if ($adminUser)
					<a class="btn  btn-small btn-danger" data-placement="left" title="Delete?" data-delete="{{ csrf_token() }}" data-myhref="{{ URL::to('slucaj/' . $value->id) }}"><i class="glyphicon glyphicon-trash"></i></a>
                    @endif
				</span>
            </td>

        </tr>


    @endforeach
*/?>


    </tbody>
</table>

<?php 
// kreiraj js data objekt da ga se može koristiti u tmkjs - data prop za dataTableMe
echo "<script>";
echo "dtme2DArray=[";
$prvi=true;
foreach($slucajevi as $key => $value) {
    if (!$prvi) echo ",";
    echo "[";
        echo "'".e($value->stsrepairorderno)."',";
        //echo "'".e($value->servicelocation->posname)."',";
        echo "'".e($value->stsroopendate->format("d.m.Y"))."',";
        echo "'".e($value->model->brand->name." ".$value->model->name)."',";
        echo "'".e($value->deviceincomingimei)."',";
        echo ($value->pos) ? "'".e($value->pos->posname)."'," : "'<span class=\'crveno\'>NEMA</span>',";
        echo "'".e($value->posrepairorderno)."',";
        echo "'".e($value->customername." ".$value->customerlastname)."',";
        if (!empty($value->repairstatuses->first()->name)) {
            echo "'".e($value->repairstatuses->first()->name)."',";
        } else {
            echo "'-error-',";
        }

        echo "'";

            echo '<span class="btn-group btn-group-xs" style="display:flex;" role="group">';
                    
                echo    '<a class="btn btn-small btn-warning" target="_blank"  title="edit" href="'.URL::to('slucaj/'.$value->id.'/edit').'"><i class="glyphicon glyphicon-pencil"></i></a>';

                echo    '<a class="btn btn-small btn-info" target="_blank" title="Prijemni list" href="'.URL::to('printPrijemniView/rn/'.$value->id).'"><i class="glyphicon glyphicon-import"></i></a>';

                echo    '<a class="btn btn-small btn-primary" target="_blank"  title="Radni nalog" href="'.URL::to('printView/rn/'.$value->id).'"><i class="glyphicon glyphicon-export"></i></a>';

                if ($adminUser) {
                    echo '<a class="btn btn-small btn-danger" data-placement="left" title="Briši nalog?" data-delete="'.csrf_token().'" data-myhref="'.URL::to('slucaj/'.$value->id).'"><i class="glyphicon glyphicon-trash"></i></a>';
                }

            echo '</span>';

        echo "'";

    echo "]";
    $prvi=false;
}
echo "];";
echo "</script>";
?>


<?php /*
@if ( SlucajController::getPaginationCount()>0 )

	 {{ $slucajevi->appends(Request::except('page'))->links() }}
	
@endif
*/
?>

	
@stop