@extends('layouts.shell')

@section('container')


<!-- if there are creation errors, they will show here -->
{!! HTML::ul($errors->all(),array('class'=>'list-unstyled alert alert-danger', 'role'=>'alert')) !!}



    <h1>
        Arhiva radnih naloga
        <a href="{{URL::to('slucaj/create')}}" title="Add new application" class="label label-default pull-right" style="font-size:.5em; margin-top:10px" ><i class="glyphicon glyphicon-plus-sign"></i></a>
    </h1>
    <p class="notice">Popis svih naloga u bazi, pretraga malo sporija. Prikazuje i izbrisane naloge (one di je brisanje onemogućeno)</p>


	<hr />
	
<!-- will be used to show any messages -->
@if (Session::has('message'))
    <div class="alert alert-info">{!! Session::get('message') !!}</div>
@endif

<table id="dataTableMeSSP" class="table table-striped table-bordered table-hover dt-responsive" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>STS RN</th>
            <th>DATUM</th>
            <th>UREĐAJ</th>
            <th>IMEI</th> 
            <th>POS</th>
            <th>POS RN</th> 
            <th>KORISNIK</th> 
            <th>ZADNJI STATUS</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <!-- prvih 25 naloga, UX -->
        @foreach($slucajevi as $slucaj)
        <tr>
            <td>{!!$slucaj->stsrepairorderno!!}</td>
            <td>{!!$slucaj->stsroopendate!!}</td>
            <td>{!!$slucaj->uredjaj!!}</td>
            <td>{!!$slucaj->deviceincomingimei!!}</td>
            <td>{!!$slucaj->posname!!}</td>
            <td>{!!$slucaj->posrepairorderno!!}</td>
            <td>{!!$slucaj->korisnik!!}</td>
            <td>{!!$slucaj->zadnjistatus!!}</td>
            <td>{!!$slucaj->alati!!}</td>
        </tr>
        @endforeach            
    </tbody>
</table>






@push('scripts')

<script >
$(function() {
  $('#dataTableMeSSP').DataTable({
        processing: true,
        pageLength: 25,
        fixedHeader: true,
        serverSide: true,
        deferRender: true,
        deferLoading: {{$total}}, //https://datatables.net/examples/server_side/defer_loading.html
        //searchDelay: 100,
        ajax: {
            url: '/dohvatiArhivaNaloge',
            type: 'POST',
            data: {
                "_token": "{!!csrf_token()!!}"
            },
            //dataSrc:function (json) {
            //    alert("Done!");
            //    return json.data;
            //}
            
        },
        columns: [
            { name: 'stsrepairorderno', data:'stsrepairorderno'},
            { name: 'stsroopendate', data:'stsroopendate'},
            { name: 'uredjaj', data:'uredjaj'},
            { name: 'deviceincomingimei', data: 'deviceincomingimei'},
            { name: 'posname', data: 'posname'},
            { name: 'posrepairorderno', data: 'posrepairorderno'},
            { name: 'korisnik', data: 'korisnik'},
            { name: 'zadnjistatus', data:'zadnjistatus'},
            { name: 'alati', data:'alati', 'searchable': false}
        ],


        "order": [[ 1, "desc" ]],
        "initComplete": function (settings, json) {

            //delay kod unosa teksta u filter - smanjiti ajax upite
            //https://www.datatables.net/forums/discussion/23970/1-10-3-searchdelay-not-working-properly

            var api = this.api();

            $('#dataTableMeSSP_filter input').off('keyup.DT search.DT input.DT paste.DT cut.DT');
            var searchDelay = null;             

            $('#dataTableMeSSP_filter input').on('input', function(event) {

                var search = $('#dataTableMeSSP_filter input').val();

                clearTimeout(searchDelay);         

                searchDelay = setTimeout(function() {
                        api
                        .search(search)
                        //.columns().search( '' )
                        .draw();
                }, 300);

            });

        },
        "drawCallback": function (response) {

            //console.log(response.json);
            //alert("Done!");
            
            $('[data-delete]').confirmation({
                'popout':true 
                ,'singleton':true
                ,'btnOkLabel':'DA'
                ,'btnCancelLabel':'NE'

                ,'onConfirm':function(){

                                // Get the route URL
                                //var url = $(this).prop('href');
                                var url = $(this).data('myhref');
                                // Get the token
                                var token = $(this).data('delete');
                                // Create a form element
                                var $form = $('<form/>', {action: url, method: 'post'});
                                // Add the DELETE hidden input method
                                var $inputMethod = $('<input/>', {type: 'hidden', name: '_method', value: 'delete'});
                                // Add the token hidden input
                                var $inputToken = $('<input/>', {type: 'hidden', name: '_token', value: token});
                                // Append the inputs to the form, hide the form, append the form to the <body>, SUBMIT !
                                
                                $form.append($inputMethod, $inputToken).hide().appendTo('body').submit();                   
                                return false;
                            }

                        });

        }



    });
});
</script>
@endpush

@stop