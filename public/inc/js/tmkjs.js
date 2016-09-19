$(document).ready( function () {

	//alert("JS running!");

// AJAX LOADING INDICATOR
$(document).ajaxSend(function(event, request, settings) {
	$('#loading-indicator').show();
});

$(document).ajaxComplete(function(event, request, settings) {
	$('#loading-indicator').hide();
});




    var status_needrelocation = 10;
    var status_moved = 11;
    var status_movedfix = 13;


    // ODREDIŠTE KOD RELOKACIJE
    // onload
    if ( [status_needrelocation,status_moved,status_movedfix].indexOf(parseInt($("#status").val())) > -1 ) {
        $("#relocationspp_id").prop("disabled", false);
    } else {
        $("#relocationspp_id").val("");
        $("#relocationspp_id").prop("disabled", true);
    }
    // promjena
    $("#status").on("change", function () {
        if ( [status_needrelocation,status_moved,status_movedfix].indexOf(parseInt($("#status").val())) >-1 ) {
            $("#relocationspp_id").prop("disabled", false);
        } else {
            $("#relocationspp_id").val("");
            $("#relocationspp_id").prop("disabled", true);
        }
    });
    // samo kod ZAPRIMANJA treba pratiti i da li ima promjene u #relocationspp_id, ako ima, promijeni za koga je nalog?


	// ako je logistika ili admin, i ako je selected: stsservicelocation_id != hidden:servicepersonlocation_id
	// onda napiši negdje "RELOKACIJA"
	// onload
	if ($("#stsservicelocation_id option:selected").val() !== $("#servicepersonlocation_id").val()) {
		$("#relocationsign").show();
	} else {
		$("#relocationsign").hide();
	}
	// promjena
	$("#stsservicelocation_id").on("change", function () {
		if ($("#stsservicelocation_id option:selected").val() !== $("#servicepersonlocation_id").val()) {
			$("#relocationsign").show();
		} else {
			$("#relocationsign").hide();
		}
	});




$("#petnajstnula").on("click",function(){
	if($("#deviceincomingimei").val()=="") {
		$("#deviceincomingimei").val("000000000000000");
	} else if(confirm('unjeti 15x "0" kao imei (recimo, nemože se iščitati serijski ili IMEI)?')) {
		$("#deviceincomingimei").val("000000000000000");
	}
});


$.mask.definitions['D'] = "[0-3]";
$.mask.definitions['M'] = "[0-1]";
$.mask.definitions['G'] = "[2]";
$("input.dateinput").mask("D9.M9.GM99",{
	placeholder:"DD.MM.GGGG"
});
$("input.dateinputshort").mask("M9/GM99",{
	placeholder:"MM/GGGG"
});


//pošalji email - AKO IMA UNEŠENE ADRESE!
$("a.sendMailLink").on("click",function(e){
	if ($.trim($("#"+$(this).data("poljeadrese")).val()) !==""){
		$(this).attr("href","mailto:"+$("#"+$(this).data("poljeadrese")).val());
	} else 
	e.preventDefault();
})


$(function () {
	$('[data-toggle="tooltip"]').tooltip()
})






// onload
if ($("#devicereturntype_id").val()==4){ // other ------------------------------->>>>>>>>
	$("#devicereturnother").attr("disabled",false);
} else {
	$("#devicereturnother").attr("disabled",true);
}
// promjena
$("#devicereturntype_id").on("change", function(){
	if ($(this).val()==4){ // other
		$("#devicereturnother").attr("disabled",false);
		if($(this).data('tempval')!=="") {
			$("#devicereturnother").val($(this).data('tempval'));
		}
	} else {
		if($("#devicereturnother").val()!=="") {
			$(this).data('tempval',$("#devicereturnother").val());
			$("#devicereturnother").val("");
		}
		$("#devicereturnother").attr("disabled",true);
	}
});


// onload
if ($("#posdevicereturntype_id").val()==4){ // other ------------------------------->>>>>>>>
	$("#posdevicereturnother").attr("disabled",false);
} else {
	$("#posdevicereturnother").attr("disabled",true);
}
// promjena
$("#posdevicereturntype_id").on("change", function(){
	if ($(this).val()==4){ // other
		$("#posdevicereturnother").attr("disabled",false);
		if($(this).data('tempval')!=="") {
			$("#posdevicereturnother").val($(this).data('tempval'));
		}
	} else {
		if($("#posdevicereturnother").val()!=="") {
			$(this).data('tempval',$("#posdevicereturnother").val());
			$("#posdevicereturnother").val("");
		}
		$("#posdevicereturnother").attr("disabled",true);
	}
});


// mjesto kupnje -------------------------------------------------- >>>>>>>>>>>>>>
$("#checkotherbuyplace").on("change", function(){
	if (this.checked) {
		$("#deviceotherbuyplace").prop('disabled',true);
	} else {
		$("#deviceotherbuyplace").prop('disabled',false);
	}
});

// onload
if ($("#devicerecievetype_id").val()==2){ // other ------------------------------->>>>>>>>
	$("#devicerecieveother").attr("disabled",false);
} else {
	$("#devicerecieveother").attr("disabled",true);
}
// promjena
$("#devicerecievetype_id").on("change", function(){
	if ($(this).val()==2){ // other
		$("#devicerecieveother").attr("disabled",false);
		if($(this).data('tempval')!=="") {
			$("#devicerecieveother").val($(this).data('tempval'));
		}
	} else {
		if($("#devicerecieveother").val()!=="") {
			$(this).data('tempval',$("#devicerecieveother").val());
			$("#devicerecieveother").val("");
		}
		$("#devicerecieveother").attr("disabled",true);
	}
});




function tmkSelect2MatchAnyKw(params, data) {
				  	// If there are no search terms, return all of the data
				  	if ($.trim(params.term) === '') {
				  		return data;
				  	}
				  	// `params.term` should be the term that is used for searching
				  	// split by " " to get keywords
				  	keywords=(params.term).split(" ");
				  	// `data.text` is the text that is displayed for the data object
					// check if data.text contains all of keywords, if some is missing, return null
					var i;
					for (i = 0; i < keywords.length; i++) {

						if (((data.text).toUpperCase()).indexOf((keywords[i]).toUpperCase()) == -1) 
						  // Return `null` if the term should not be displayed
						return null;

					}
				  	// If here, data.text contains all keywords, so return it.
				  	return data;
				  }



/* - isključeno 24.01.2016, Štef, SKYPE, tmkjs.js & validation u repairordercontrolleru + create view
	// SWAP ARTIKLA --------------------------------------------------
	if ($('#stsdeviceswap').is(":checked")) $("input.tmk_swap").prop("readonly", false);
	$("input[name='deviceincomingimei']").on('change', function(){
		if(!$('#stsdeviceswap').is(":checked")) $("input[name='deviceoutgoingimei']").val($(this).val());
	});
	$("input[name='deviceincomingsasref']").on('change', function(){
		if(!$('#stsdeviceswap').is(":checked")) $("input[name='deviceoutgoingsasref']").val($(this).val());
	});
	$('#stsdeviceswap').on('click',function(){
		// ako je uključen, izvadi placeholder podatke ili ne, i enable
		if ($(this).is(":checked")){
			$("input.tmk_swap").each(function(){
				//$(this).removeAttr("disabled").attr("placeholder", $(this).val()).val(null);
				data= (typeof $(this).attr("placeholder") !== typeof undefined && $(this).attr("placeholder") !== false) ? $(this).attr("placeholder") : ""
				$(this).prop("readonly",false).val(data);
			});
		} else { 
		// ako je sada isključen swap, sačuvaj trenutne podatke ovisno o parametru i disable + iskopiraj iz incoming
		$("input[name='deviceoutgoingimei']").attr("readonly","readonly").attr("placeholder", $("input[name='deviceoutgoingimei']").val()).val($("input[name='deviceincomingimei']").val());
		$("input[name='deviceoutgoingsasref']").attr("readonly","readonly").attr("placeholder", $("input[name='deviceoutgoingsasref']").val()).val($("input[name='deviceincomingsasref']").val());
		//$("input[name='deviceoutgoingswversion']").attr("readonly","readonly").attr("placeholder", $("input[name='deviceoutgoingswversion']").val()).val($("input[name='deviceincomingswversion']").val());
		}
	});
*/

	// ------------------------------------------------------------------------------------------------------------------ 
	// SELECT2 JQ plug-ins //////////////////////////////////////////////////////////////////////////////////////////////
	// ------------------------------------------------------------------------------------------------------------------ 
	//	- https://select2.github.io/
	//
	// http://stackoverflow.com/questions/28518158/jquery-select2-dropdown-disabled-when-cloning-a-table-row
	//
	//select2 i datatables
	//https://github.com/vedmack/yadcf
	//
	//$.fn.select2.defaults.set("allowClear", true);


	$(".tmk-select2").select2({
		"allowClear": true,
		"placeholder": "Odaberi",
		matcher: tmkSelect2MatchAnyKw,
	});

	$(".tmk-sing-select2").select2({
		"allowClear": true,
		"placeholder": "Odaberi",
		"width" : "100%",
		matcher: tmkSelect2MatchAnyKw,
	});


//$.fn.select2.amd.require(['select2/compat/matcher'], function (oldMatcher) {
	$(".tmk-sing-nc-select2").select2({
		"width" : "100%",
		"placeholder": "Odaberi",
		matcher: tmkSelect2MatchAnyKw,
	});


	// TECHNICIAN SYMPTOMS ---------------------------------------------------------------------------------------------- 
	$("#techniciansymptom_id").select2({
		"allowClear": true,
		"placeholder": "Odaberi glavni simptom",
		"width" : "100%",
		matcher: tmkSelect2MatchAnyKw,
	});


	$("#techniciansymptomothers").select2({
			//"allowClear": true,
			"placeholder": "Ostali simptomi",
			"width" : "100%",
			matcher: tmkSelect2MatchAnyKw,
		});



	function init_techniciansymptomothers(obj){

				// targetSelect
				targetSelect=$("#techniciansymptomothers");

				// ako je novi disableani već odabran, makni ga iz odabranih
				if ( $.isArray(targetSelect.val()) && ($.inArray($(obj).val(), targetSelect.val()) > -1) ) {
					newArray=[];
					for(i=0; i<(targetSelect.val()).length; i++){
							//console.log(entry);
							if ((targetSelect.val())[i]!==$(obj).val()) newArray.push((targetSelect.val())[i]);
						}
						//console.debug(newArray);
						targetSelect.val(newArray).trigger("change");
					}

				// sačuvaj opcije
				var tempOptions = targetSelect.data('select2').options.options;

				// console.debug($(this).val());
				// console.debug(targetSelect.val());

				// zgasi s2
				targetSelect.select2("destroy");

				// promijeni disabled option (hoću da je taj option disabled u "others")
				targetSelect.find("option").removeAttr("disabled");
				targetSelect.find(" option[value='"+$(obj).val()+"']").attr("disabled", true);


				// reinicijaliziraj
				targetSelect.select2(tempOptions);

				// ako je odabran neki glavni, upali "others"

				if ($(obj).val() !== "") {
					// upali others
					targetSelect.prop("disabled", false);
				}

			}	

		// kod učitavanja stranice (EDIT)
		$("#techniciansymptom_id").each(function(){init_techniciansymptomothers(this);});
		// za kasnije, others su disableani
		$("#techniciansymptom_id").on("select2:select", function (e){
			init_techniciansymptomothers(this);
		});


		$(document).on("select2:unselect", "#techniciansymptom_id", function (e){
		// postavi sve u others na početak
		$("#techniciansymptomothers").prop("disabled", true).val(null).trigger("change");
	});








	// CUSTOMER SYMPTOMS --------------------------------------------------------------------------------------------------------- 
	$('#ms_customersypmtoms').select2({
		"allowClear": true,
		"placeholder": "Odaberi",
		"width" : "100%",
		matcher: tmkSelect2MatchAnyKw,
	});


	// faulty elements -----------------------------------------------------------------------------------------------------------
	$('#ms_faultyelements').select2({
		"allowClear": true,
		"placeholder": "Odaberi",
		"width" : "100%",
		matcher: tmkSelect2MatchAnyKw,
	});


	// SPARE PARTS ---------------------------------------------------------------------------------------------------------------
	$('#sp_parts, #sppartsreceipt').select2({
		"allowClear": true,
		"placeholder": "Odaberi",
		"width" : "100%",
		matcher: tmkSelect2MatchAnyKw,		
	});
	var tmk_sparepartsCount = $("#tmk_spareparts tbody tr").length;

	$(document).on("select2:select", "#sp_parts", function (e) { 

			//alert($(this).select2('data')[0].id);
			$selid=$(this).val();
			//$seltxt=$(this).select2('data')[0].text;
			$seltxt=$(this).find(':selected').attr('data-name'); //attr('data-pricekn');
			$selprc=$(this).find(':selected').attr('data-price'); //attr('data-pricekn');
			$selwhs=$(this).find(':selected').attr('data-warehouse'); //attr('data-pricekn');
			$selwhsid=$(this).find(':selected').attr('data-warehouseid'); //attr('data-pricekn');

			var newTableRow = $('<tr>\
				<td  class="vert-align"> \
				<input id="sppt'+tmk_sparepartsCount+'ids" type="hidden" name="sppt['+tmk_sparepartsCount+'][ids]" class= "tmksp_id"  value="'+$selid+'" /> \
				<button id="sppt'+tmk_sparepartsCount+'del" class="removespptrow btn btn-default"><i class="glyphicon glyphicon-remove"></i> </button> \
				'+$seltxt+' \
				</td> \
				<td>\
				<input id="sppt'+tmk_sparepartsCount+'prc" type="text" name="sppt['+tmk_sparepartsCount+'][prc]" class= "tmksp_price form-control"  value="'+$selprc+'" /> \
				</td> \
				<td>\
				<input id="sppt'+tmk_sparepartsCount+'qty" type="text" name="sppt['+tmk_sparepartsCount+'][qty]" class= "tmksp_qty form-control"  value="1.00" /> \
				</td> \
				<td>\
				<input id="sppt'+tmk_sparepartsCount+'whs" type="hidden" name="sppt['+tmk_sparepartsCount+'][whs]" class= "tmksp_whs"  value="'+$selwhsid+'" /> \
				'+$selwhs+'\
				</td> \
				\
				</tr>');

			// otvori tablicu
			if (tmk_sparepartsCount==0)
				$("#tmk_spareparts").prepend('<table class="table table-condensed table-bordered"><thead><tr><th>naziv</th><th class="col-md-2">cijena</th><th class="col-md-2">kom</th><th class="col-md-2">skladiste</th></tr></thead><tbody></tbody></table>');

			// dodaj row
			$("#tmk_spareparts tbody").append(newTableRow);

			// očisti select
			$(this).val(null).trigger("change");

			// stavi fokus na  QTY
			$("#sppt"+tmk_sparepartsCount+"qty").focus().select();

			// povečaj broj redova
			tmk_sparepartsCount++;


		});


$(document).on("select2:select", "#sppartsreceipt", function (e) { 

			//alert($(this).select2('data')[0].id);
			$selid=$(this).val();
			//$seltxt=$(this).select2('data')[0].text;
			$seltxt=$(this).find(':selected').text(); //attr('data-pricekn');
			//$seltxt=$(this).find(':selected').attr('data-name'); //attr('data-pricekn');
			$selprc=0; //attr('data-pricekn');
			//$selprc=$(this).find(':selected').attr('data-price'); //attr('data-pricekn');

			var newTableRow = $('<tr>\
				<td  class="vert-align"> \
				<input id="sppt'+tmk_sparepartsCount+'ids" type="hidden" name="sppt['+tmk_sparepartsCount+'][ids]" class= "tmksp_id"  value="'+$selid+'" /> \
				<button id="sppt'+tmk_sparepartsCount+'del" class="removespptrow btn btn-default"><i class="glyphicon glyphicon-remove"></i> </button> \
				'+$seltxt+' \
				</td> \
				<td>\
				<input id="sppt'+tmk_sparepartsCount+'qty" type="text" name="sppt['+tmk_sparepartsCount+'][qty]" class= "tmksp_qty form-control"  value="1" /> \
				</td> \
				<td>\
				<input id="sppt'+tmk_sparepartsCount+'prc" type="text" name="sppt['+tmk_sparepartsCount+'][prc]" class= "tmksp_price form-control"  value="'+$selprc+'" /> \
				</td> \
				\
				</tr>');

			// otvori tablicu
			if (tmk_sparepartsCount==0)
				$("#tmk_spareparts").prepend('<table class="table table-condensed table-bordered"><thead><tr><th>naziv</th><th class="col-md-2">kom</th><th class="col-md-2">cijena</th></tr></thead><tbody></tbody></table>');

			// dodaj row
			$("#tmk_spareparts tbody").prepend(newTableRow);

			// očisti select
			$(this).val(null).trigger("change");

			// stavi fokus na  QTY
			$("#sppt"+tmk_sparepartsCount+"qty").focus().select();

			// povečaj broj redova
			tmk_sparepartsCount++;


		});




$('form').on('keyup keypress', 'input, button', function(e) {
	var code = e.keyCode || e.which;
// dozvoliti ENTER samo na SELECT i TEXTAREA

if (code == 13) { 
	e.preventDefault();
	return false;
}
});



$(document).on('click','.removespptrow', function (e){
	e.preventDefault();
	this.blur();
			// smanji broj redova
			tmk_sparepartsCount--;
			// makni table header
			if (tmk_sparepartsCount==0) {
				$("#tmk_spareparts table").remove();
			} else {
				// makni samo red
				$(this).parents("tr").first().remove();
			}
			// omogući save gumb
			enableSaveGumb();
			//return false;
		});


$('#tmk_spareparts .s2').on("select2:unselect", function (e) { 
			// alert("brisan"); 
			// SVI SE MOGU BRISATI OSIM PRVOG CHILDA!
		});


	// STS BROJ NALOGA ----------------------------------------------------------------------------------------------------------- 
	/*
	if ($.trim($('#stsrepairorderno').val())==='') $('#stsrepairorderno').val( $('#stsrepairorderno').data('tmkempty'));

	$('#stsrepairorderno').on('click',function(e){
		if ($(this).val()==$(this).data('tmkempty')) $(this).val("");
	});
	$('#stsrepairorderno').on('blur',function(e){
		if ($.trim($(this).val())==='') $(this).val($(this).data('tmkempty'));
	});
*/

	// STS SERVICES ----------------------------------------------------------------------------------------------------------- 
	$('#stsservices').select2({
		"allowClear": true,
		"placeholder": "Odaberi",
		"width" : "100%",
		matcher: tmkSelect2MatchAnyKw,		
	});
	var tmk_stsservicesCount = $("#tmk_stsservices tbody tr").length;
	$(document).on("select2:select", "#stsservices", function (e) { 

			//alert($(this).select2('data')[0].id);
			$selid=$(this).val();
			$seltext=$(this).select2('data')[0].text;
			$seljm=$(this).find(':selected').attr('data-jm');
			$selprc=$(this).find(':selected').attr('data-price'); //attr('data-pricekn');
			$readonly=$(this).find(':selected').attr('data-ro'); //attr('data-pricekn');
			var newRowString='<tr>\
			<td  class="vert-align"> \
			<input id="stssrv'+tmk_stsservicesCount+'ids" type="hidden" name="stssrv['+tmk_stsservicesCount+'][ids]" class= "tmksrv_id"  value="'+$selid+'" /> \
			<button id="stssrv'+tmk_stsservicesCount+'del" class="removestssrvrow btn btn-default"><i class="glyphicon glyphicon-remove"></i> </button> \
			</td>\
			<td><input id="stssrv'+tmk_stsservicesCount+'nme" type="text" name="stssrv['+tmk_stsservicesCount+'][nme]" class= "tmksrv_name form-control" '; 
			if ($readonly == '1') {
				newRowString+=' readonly="readonly" ';
			}
			newRowString+=' value="'+$seltext+'" /></td>\
			<td><input id="stssrv'+tmk_stsservicesCount+'jm"  type="text" name="stssrv['+tmk_stsservicesCount+'][jm]"  class= "tmksrv_jm form-control" ';
			if ($readonly == '1') {
				newRowString+=' readonly="readonly" ';
			}
			newRowString+=' value="'+$seljm+'" /></td> \
			<td>\
			<input id="stssrv'+tmk_stsservicesCount+'prc" type="text" name="stssrv['+tmk_stsservicesCount+'][prc]" class= "tmksrv_price form-control"  value="'+$selprc+'" /> \
			</td> \
			<td>\
			<input id="stssrv'+tmk_stsservicesCount+'qty" type="text" name="stssrv['+tmk_stsservicesCount+'][qty]" class= "tmksrv_qty form-control"  value="1.00" /> \
			</td> \
			\
			</tr>';

			var newTableRow = $(newRowString);

			// otvori tablicu
			if (tmk_stsservicesCount==0)
				$("#tmk_stsservices").prepend('<table class="table table-condensed table-bordered"><thead><tr><th></th><th>naziv</th><th class="col-md-2">jm</th><th class="col-md-2">cijena</th><th class="col-md-2">qty</th></tr></thead><tbody></tbody></table>');

			// dodaj row
			$("#tmk_stsservices tbody").append(newTableRow);

			// očisti select
			$(this).val(null).trigger("change");

			// stavi fokus na cijenu ako je "0" ako nije onda na QTY
			if($("#stssrv"+tmk_stsservicesCount+"prc").val()>0) $("#stssrv"+tmk_stsservicesCount+"qty").focus().select();
			else $("#stssrv"+tmk_stsservicesCount+"prc").focus().select();

			// povečaj broj redova
			tmk_stsservicesCount++;

		});


$(document).on('click','.removestssrvrow', function (e){
	e.preventDefault();
	this.blur();
			// smanji broj redova
			tmk_stsservicesCount--;
			// makni table header
			if (tmk_stsservicesCount==0) {
				$("#tmk_stsservices table").remove();
			} else {
				// makni samo red
				$(this).parents("tr").first().remove();
			}
			// omogući save gumb
			enableSaveGumb();
			//return false;
		});


// S2's ======================================================================================================================
// --------------------------------------------------------------------------------------------------------------------------- 







$.extend( true, $.fn.dataTable.defaults, {
	responsive: true,
	stateSave: false,	
	deferRender: true,
	paging: true,
	"pagingType": "full_numbers",
		//"pageLength": 3,
		"pageLength": 25,

	lengthMenu: [
				[25, 50, 100, -1], 
				[25, 50, 100, "All"]
				],
		//lengthChange: false,

		"language": {
//			"url": "http://cdn.datatables.net/plug-ins/1.10.9/i18n/Croatian.json"
"url": "/inc/js/datatable_Croatian.json"
},

ordering: true,
"orderClasses": false,
	    "order": [[0,"desc"]], // "prijavljen (datum)"

	    columnDefs: [
	    { 	orderable: false, 
	    	targets: -1,
	    }
	    ]

	} );



$.fn.dataTable.moment( 'DD.MM.YYYY' );
$.fn.dataTable.moment( 'DD.MM.YY' );

/*

	$('#dataTableDash').DataTable({

		responsive: true,
		stateSave: true,	

		paging: true,
		"pagingType": "full_numbers",
		//"pageLength": 3,


		"language": {
			//"url": "http://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Croatian.json"
			"url": "/inc/js/datatable_Croatian.json"
		},

		ordering: true,
		"orderClasses": false,
	    "order": [[1,"desc"]], // "prijavljen (datum)"

	    columnDefs: [
	    { orderable: false, targets: -1 }, 
	    ]
	});

*/	



$('.tmkdt.tmkdt-t2').DataTable({
    "drawCallback": function () {
        //initComplete: function () {
        $('[data-delete]').confirmation({
			'popout':true 
			,'singleton':true
			,'btnOkLabel':'DA'
			,'btnCancelLabel':'NE'
			,'onConfirm':function(){
				var url = $(this).data('myhref');
				var token = $(this).data('delete');
				var dest = $(this).data('dest');
				var $form = $('<form/>', {action: url, method: 'post'});
				var $inputMethod = $('<input/>', {type: 'hidden', name: '_method', value: 'delete'});
				var $inputToken = $('<input/>', {type: 'hidden', name: '_token', value: token});
				var $destination =$('<input/>', {type: 'hidden', name: 'dest', value: dest});
				$form.append($inputMethod, $inputToken, $destination).hide().appendTo('body').submit();					
				return false;
			}

		});
	},

});
$('.tmkdt.tmkdt-primke').DataTable();
$('.tmkdt.tmkdt-parts').DataTable();
$('.tmkdt.tmkdt-simple').DataTable();
$('.tmkdt.tmkdt-komitenti').DataTable();
$('.tmkdt.tmkdt-employee').DataTable({
    "drawCallback": function () {
        //initComplete: function () {
		$('[data-delete]').confirmation({
			'popout':true 
			,'singleton':true
			,'btnOkLabel':'DA'
			,'btnCancelLabel':'NE'
			,'onConfirm':function(){
				var url = $(this).data('myhref');
				var token = $(this).data('delete');
				var $form = $('<form/>', {action: url, method: 'post'});
				var $inputMethod = $('<input/>', {type: 'hidden', name: '_method', value: 'delete'});
				var $inputToken = $('<input/>', {type: 'hidden', name: '_token', value: token});
				$form.append($inputMethod, $inputToken).hide().appendTo('body').submit();					
				return false;
			}
		});
	},
});

$('.tmkdt.tmkdt-pos').DataTable({
		"order": [[1,"desc"]], // id
    "drawCallback": function () {
        //initComplete: function () {
			$('[data-delete]').confirmation({
				'popout':true 
				,'singleton':true
				,'btnOkLabel':'DA'
				,'btnCancelLabel':'NE'
				,'onConfirm':function(){
					var url = $(this).data('myhref');
					var token = $(this).data('delete');
					var $form = $('<form/>', {action: url, method: 'post'});
					var $inputMethod = $('<input/>', {type: 'hidden', name: '_method', value: 'delete'});
					var $inputToken = $('<input/>', {type: 'hidden', name: '_token', value: token});
					$form.append($inputMethod, $inputToken).hide().appendTo('body').submit();					
					return false;
				}

			});
		},
	});

$('.tmkdt.tmkdt-services').DataTable({
		"order": [[0,"desc"]], // id
		stateSave:false,		
	});

$('.tmkdt.tmkdt-modeli').DataTable({
		"order": [[1,"desc"]], // tip
        "drawCallback": function () {
			$('[data-delete]').confirmation({
				'popout':true
				,'singleton':true
				,'btnOkLabel':'DA'
				,'btnCancelLabel':'NE'
				,'onConfirm':function(){
					var url = $(this).data('myhref');
					var token = $(this).data('delete');
					var $form = $('<form/>', {action: url, method: 'post'});
					var $inputMethod = $('<input/>', {type: 'hidden', name: '_method', value: 'delete'});
					var $inputToken = $('<input/>', {type: 'hidden', name: '_token', value: token});
					$form.append($inputMethod, $inputToken).hide().appendTo('body').submit();
					return false;
				}
			});
		},

});

/*

	$('.tmkdt.tmkdt-rpt1').DataTable({
		"order": [[2,"desc"],[0,"desc"]], // datum pa broj naloga
		"pagingType": "full",
		"pageLength": 25,
		"stateSave": false,	
        processing: true,
		"deferRender": true,
	});


 */



	// OTPREMA - TABLE =======================================
	$('#rptrealizacija').DataTable({
		dom: 
		"<'row'<'col-sm-6'l><'col-sm-6'f>>" +
		"<'row'<'col-sm-12'tr>>" +
		"<'row'<'col-sm-5'i><'col-sm-7'p>>"+
		"<'row'<'col-sm-12 pull-right'B>>",
       	"order": [[3,"desc"],[1,"desc"]], // datum pa broj naloga
       	paging:false,
       	stateSave: false,	
       	buttons: [
       	{
       		extend: 'print',
       		text: "Print",
       		exportOptions: {
       			columns: ':visible'
       		}
       	},
       	{
       		extend: 'colvis',
       		text: 'Vidljive kolone',
       	}
       	],

	    //https://datatables.net/examples/advanced_init/footer_callback.html
	    "footerCallback": function ( row, data, start, end, display ) {
	    	var api = this.api(), data;

            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
            	return typeof i === 'string' ?
            	i.replace(/[\$,]/g, '')*1 :
            	typeof i === 'number' ?
            	i : 0;
            };

			// Total over all pages
			total = api.column( 2 ).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
            // Update footer
            $( api.column( 2 ).footer() ).html('<strong>'+total+'</strong>');

            total = api.column( 3 ).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
            $( api.column( 3 ).footer() ).html('<strong>'+total+'</strong>');
            total = api.column( 4 ).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
            $( api.column( 4 ).footer() ).html('<strong>'+total+'</strong>');
            total = api.column( 5 ).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
            $( api.column( 5 ).footer() ).html('<strong>'+total+'</strong>');
            total = api.column( 6 ).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
            $( api.column( 6 ).footer() ).html('<strong>'+total+'</strong>');
            total = api.column( 7 ).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
            $( api.column( 7 ).footer() ).html('<strong>'+total+'</strong>');


        }
    } );



$('#rptrealizacijadetaljno').DataTable({
	dom: 
	"<'row'<'col-sm-6'l><'col-sm-6'f>>" +
	"<'row'<'col-sm-12'tr>>" +
	"<'row'<'col-sm-5'i><'col-sm-7'p>>"+
	"<'row'<'col-sm-12 pull-right'B>>",
       	"order": [[2,"desc"],[0,"desc"]], // datum pa broj naloga
       	"columnDefs": [ { "targets": 1, "orderable": false, "searchable": false } ],
       	paging:false,
       	stateSave: false,	
       	buttons: [
       	{
       		extend: 'print',
       		text: "Print",
       		title: "STS REALIZACIJA",
       		message: "SERVISER: "+$("#serviser_id option:selected" ).text()+" <br/>PERIOD:"+$("#datumOd").val()+"-"+$("#datumDo").val(), 
       		autoPrint: false,
       		exportOptions: {
       			columns: ':not(.nemojprintat)'
       		}
       	},
/*	        {
	           extend: 'colvis',
	           text: 'Vidljive kolone',
			}
			*/
			],

	    //https://datatables.net/examples/advanced_init/footer_callback.html
	    "footerCallback": function ( row, data, start, end, display ) {
	    	var api = this.api(), data;

            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
            	return typeof i === 'string' ?
            	i.replace(/[\$,]/g, '')*1 :
            	typeof i === 'number' ?
            	i : 0;
            };



			// Total over all pages
			total = api.column( 4 ).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
            // Update footer
            $( api.column( 4 ).footer() ).html('<strong>'+total+'</strong>');
            total = api.column( 5 ).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
            $( api.column( 5 ).footer() ).html('<strong>'+total+'</strong>');
            total = api.column( 6 ).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
            $( api.column( 6 ).footer() ).html('<strong>'+total+'</strong>');
            total = api.column( 7 ).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
            $( api.column( 7 ).footer() ).html('<strong>'+total+'</strong>');
            total = api.column( 8 ).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
            $( api.column( 8 ).footer() ).html('<strong>'+total+'</strong>');
        }
    } );


	// OTPREMA - TABLE =======================================
	var d = new Date();
	var n = d.toLocaleDateString();
	var t = d.toLocaleTimeString();
	if ($("#datumzavrsetka").val()+"."==n) {
		var tempDatum = ", NA "+n+" DO "+t;
	}else{
		var tempDatum = ($("#datumzavrsetka").val()=="") ? ", DO "+n+" U "+t: ", NA DATUM: "+$("#datumzavrsetka").val();
	}
	var tempServiser = ($("#serviseri").val()==0) ? "SVI" : $("#serviseri option:selected" ).text();
	var tempLokacija = ($("#serviceLocation").val()==0) ? "SVE" : $("#serviceLocation option:selected" ).text();

	var tab_otprema= $('#rptotprema').DataTable({
		dom: 
		"<'row'<'col-sm-6'l><'col-sm-6'f>>" +
		"<'row'<'col-sm-12'tr>>" +
		"<'row'<'col-sm-5'i><'col-sm-7'p>>"+
		"<'row'<'col-sm-12 pull-right'B>>",
       	"order": [[3,"desc"],[1,"desc"]], // datum pa broj naloga
       	"pagingType": "full",
       	"pageLength": 25,
       	stateSave: false,	
       	buttons: [
       	{
       		extend: 'print',
       		text: "Print",
       		title: "STS - UREĐAJI ZA OTPREMU",
       		message: "LOKACIJA: "+tempLokacija+", SERVISER: "+tempServiser+tempDatum, 
       		autoPrint: true,
       		exportOptions: {
       			columns: ':not(.nemojprintat)'
       			//columns: ':visible'
       		}
       	},
       	{
       		extend: 'colvis',
       		text: 'Vidljive kolone',
       	}
       	],
       });

/*
// gumb je: #otpreminaloge

	// --- SELEKTIRANJE REDOVA
    $('#rptotprema tbody').on( 'click', 'tr', function () {
        $(this).toggleClass('selected');
        if ($('#rptotprema tbody tr.selected').length) {
        	$("#otpreminaloge").prop("disabled",false);
        } else {
        	$("#otpreminaloge").prop("disabled",true);
        }
    } );
 
    // --- NAĐI SELEKTIRANE 
    $('#otpreminaloge').click( function () {
        //alert( tab_otprema.rows('.selected').data().length +' row(s) selected' );
         redovi = tab_otprema.rows('.selected').data();
         nalozi = [];
         for(i=0; i<redovi.length; i++){
         	// prvi CELL
         	//alert($(redovi[i][0]).text());
         	nalozi[i]=$(redovi[i][0]).text(); // uf... triper, ali radi
         }
         console.log(nalozi);

         // imam IDjeve

    } );

	// OTPREMA - TABLE END ===================================

	*/







//    tab.buttons().container().appendTo('#alati'); //#rptotprema_wrapper .col-sm-6:eq(0)
/*
		language: {
			url: "http://cdn.datatables.net/plug-ins/1.10.9/i18n/English.json", 
			buttons: {
	            print: "Click to copy",
	            copy: "Click to copy",
	            pdf: "Click to copy",
	            excel: "Click to copy",
	            csv: "Click to copy"
       		}
		},
		*/


		$('#dataTableMe').DataTable({

			responsive: true,
			stateSave: false,	
			deferRender: true,
			fixedHeader: true,

			pageLength: 25,

			paging: true,
			pagingType: "full_numbers",
		//"pageLength": 3,

		data: dtme2DArray,

		"language": {
			//"url": "http://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Croatian.json"
			"url": "/inc/js/datatable_Croatian.json"
		},

		ordering: true,
		"orderClasses": false,
	    "order": [[0, "desc"]], // [1,"desc"],  "prijavljen (datum)"

	    columnDefs: [
	    { orderable: false, targets: -1 }, 
	    ],
/*		"tableTools": {
            "sSwfPath": "/swf/copy_csv_xls_pdf.swf"
        },

        "tableTools": {
            "aButtons": [
                //"copy",
				"xls",
                "print"
				
				//,{
                //    "sExtends":    "collection",
                //    "sButtonText": "Save",
                //    "aButtons":    [ "csv", "pdf" ]
                //}
				
            ]
        },

		"dom": '<"row" <"col-sm-6"l><"col-sm-6"f>><"row" <"col-sm-12"t>><"row" <"col-sm-6"iT><"col-sm-6"p>>',
		*/


            "drawCallback": function () {
		//initComplete: function () {

			// delete gumb nakon inicijalizacije:
			// Jquery function which listens for click events on elements which have a data-delete attribute
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





/*
            this.api().columns('.columnfilterthis').every( function () {
                var column = this;
                var select = $('<select class="form-control"><option value=""></option></select>')
                    .appendTo( $(column.header() ) )
                    .on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                           			 $(this).val()
                        			);
                        column
                            .search( val ? '^'+val+'$' : '', true, false )
                            .draw();
                    	} );
 
                	column.data().unique().sort().each( function ( d, j ) {
                  	  select.append( '<option value="'+d+'">'+d+'</option>' )
                	} );
           		
           		 } );


			$("#dataTableMe_paginate").after('<a href="#" class="pull-right btn btn-small btn-default" style="padding:0px 3px" id="reset_table"><small>reset state</small></a>');
			*/

		}
		
		
	});



/*

    if ( this.value == "" )
    {
        this.className = "search_init";
        this.value = this.title;
       
    }



  	da samo ostanu moguće opcije u selectu
	https://datatables.net/forums/discussion/27541/update-select-filters
$('#dataTableMe').DataTable({

 table.on('draw', function () {
    table.columns().indexes().each( function ( idx ) {
      var select = $(table.column( idx ).footer()).find('select');
      
      if ( select.val() === '' ) {
        select
          .empty()
          .append('<option value=""/>');

        table.column(idx, {search:'applied'}).data().unique().sort().each( function ( d, j ) {
          select.append( '<option value="'+d+'">'+d+'</option>' );
        } );
      }
    } );
  } );


});

*/







	// reset datatable STATE
	$('body').on('click', '#reset_table', function(e){
		e.preventDefault();
		var confirmation = confirm('Do you want to reset this table?');
		if(confirmation) { 
			$('#dataTableMe').DataTable().state.clear();
			window.location.reload();
		}
	});


    // mini jQuery plugin that formats to two decimal places
    (function($) {
    	$.fn.decimalInput = function() {
    		this.each( function( i ) {
    			$(this).blur( function( e ){
                	//ako ima , zamjeni za 0
                	this.value=(this.value).replace(",",".");
                	if( isNaN( parseFloat( this.value ) ) ) {
                		this.value="0.00";
                	} else {
                		this.value = parseFloat(this.value).toFixed(2);
                	}
                });
    		});
            return this; //for chaining
        }
    })( jQuery );


    // apply the currencyFormat behaviour to elements with 'currency' as their class
    $( function() {
    	$('.decimalInput').decimalInput();
    });




    // SAVE GUMB ============================================================================================
    // staviti ću da ne provjerava svaki put da li treba enable, disable save - dakle nakon prve promjene će 
    // enableati, a ak se vrijednost i vrati na staro ostaje enablean.

    // ------------------------V
    // eventualno dodati da kod ajax submita (i pravog submita) provjeri (kao kak je ovaj dole) da li je bilo 
    // promjena, (znači ima formA i formB serijaliziran) i ak nije, da ne sumbita / sprema...
    // ------------------------A

    // enable gumb
    var enableSaveGumb = function(){
    	if ($("#savebutton").prop("disabled") == false) return;
    	$("#savebutton").prop("disabled",false);
    }
    $("#caseForm").change(enableSaveGumb);
    $("#caseForm").keyup(enableSaveGumb);

    // ajax call
    $("#savebutton").on("click", function(e){

			//    	e.preventDefault();

			$.ajax({
				type 	: 	'PUT',
				url 	: 	'/slucaj/'+$("#caseForm #caseID").val(),
				data 	: 	$("#caseForm").serialize(),
				dataType: 	'json',
				error: function(jqXhr){
					if( jqXhr.status === 422 ) {
							        //process validation errors here.
							        errors = jqXhr.responseJSON; //this will get the errors response data.

							        //show them somewhere in the markup
							        errorsHtml = '<ul class="myAjaxAlert list-unstyled alert alert-danger" role="alert">';
							        $.each( errors, function( key, value ) {
							            errorsHtml += '<li>' + value[0] + '</li>'; //showing only the first error.
							        });
							        errorsHtml += '</ul>';

							        // makni sve prethodne    
							        $('.myAjaxAlert').remove();


							        // ubaci ispred H1
							        $('h1').first().before( errorsHtml ); //appending to a <div id="form-errors"></div> inside form

							        // disable savebutton
							        $("#savebutton").prop("disabled",true).blur();

							        // skroll gore da se vide greške
							        //window.scrollTo(0,0);
							        jQuery('html,body').animate({scrollTop:0},500);

							    }
							},
				success: function(data) {
				    	 	// log data to the console so we can see
				    	 	//console.log(data);

					        //poruka
					        poruka = '<div class="myAjaxAlert alert alert-'+data.status+'">'+data.msg+'</div>';

					        // makni sve prethodne    
					        $('.myAjaxAlert').remove();
					        $('#topmsgs').remove();

					        // ubaci ispred H1
					        $('h1').first().before( poruka ); //appending to a <div id="form-errors"></div> inside form

					        if ( ! data.success) {
								//	foreach (data.errors)
							}
					        // disable savebutton
					        $("#savebutton").prop("disabled",true).blur();

					        //jQuery('html,body').animate({scrollTop:0},500);

					        // AKO SE PROMJENIO STATUS: 
							        // ====================================================================
							        if (data.newstatuslist) {
							       	 	// -- UPDATE SELECT SA STATUSIMA!
							        	//console.log(data.newstatuslist);
							        	// obriši
							        	$('#status').empty();
							        	// napuni s novim
							        	$.each(data.newstatuslist, function(i, value) {
							        		//console.log("i= "+i, "value= " + value);
							        		if (i==data.newstatusid) {
								        		$('#status').append($('<option>').text(value).attr('value',i).attr('selected','selected'));
							        		} else {
							        			$('#status').append($('<option>').text(value).attr('value',i))
							        		}
							        	});
								        // -- UPDATE POVIJEST
								        if (data.historymodalcontent) {
									        $('#historyModal').html(data.historymodalcontent);
									    }
							        }
					    }

					});

	/*
		      // using the fail promise callback
	    	 .fail(function(data) {

		        // show any errors
		        // best to remove for production
		        // console.log(data);
		     });
	*/


	});


    //još neki
    //http://rubentd.com/dirrty/
    
		/* neki save gumb...
		http://stackoverflow.com/questions/194101/what-is-the-best-way-to-track-changes-in-a-form-via-javascript

		// ovaj provjerava svaki keyup.. možda overload

		function formUnloadPrompt(formSelector) {
		    var formA = $(formSelector).serialize(), formB, formSubmit = false;

		    // Detect Form Submit
		    $(formSelector).submit( function(){
		        formSubmit = true;
		    });

		    // Handle Form Unload    
		    window.onbeforeunload = function(){
		        if (formSubmit) return;
		        formB = $(formSelector).serialize();
		        if (formA != formB) return "Your changes have not been saved.";
		    };

		    // Enable & Disable Submit Button
		    var formToggleSubmit = function(){
		        formB = $(formSelector).serialize();
		        $(formSelector+' [type="submit"]').attr( "disabled", formA == formB);
		    };

		    formToggleSubmit();
		    $(formSelector).change(formToggleSubmit);
		    $(formSelector).keyup(formToggleSubmit);
		}



		// Call function on DOM Ready:

		$(function(){
		    formUnloadPrompt('form');
		});

*/
    // SAVE GUMB END ==================================================================================


    // DISCLAIMERS ===============
    $("#disclaimerButton").on("click",function(e){
    	e.preventDefault;
    	tx=$("#stsnotice").val();
    	$("#stsnotice").val(tx+$("#disclaimerSelect option:selected" ).text());
    	return false;
    });
    // DISCLAIMERS END ===========


    // OTPREMI
    $(document).on("click",".otpremiNalog",function(){
    	//e.preventDefault();
    	ovo = $(this);
    	var url = ovo.data('myhref');
    	var token = ovo.data('token');
    	var nalog = ovo.data('nalog');
    	
    	bootbox.confirm({
    		buttons: {
    			confirm: {
    				label: 'Da'
					//,className: "btn-success"
				},
				cancel: {
					label: 'Ne'
				}

			},
    	 	size: null, // large, null
    	 	title: "OTPREMI NALOG "+nalog+"?",
    	 	message: "Nalog se otprema (status: otpremljeno ili otpremljeno nepopravljano) i miče sa liste za otpremu?",
    	 	callback: function(result) {
    	 		if (result) {

						    	// AJAX Call
						    	// - ovisno o trenutnom statusu (završen ili odustanak) postavi status (otpremljeno ili otpreljeno nepopravljeno)
						    	// - to bi trebala biti neka SS metoda "OTPREMI", koja će osim kaj postavi novi status, poslije i zapisivati u log...
						    	// - po success, makni ovaj row! 

						    	$.ajax({
						    		type 	: 	'POST',
						    		url 	: 	url,
						    		data 	: 	{'_token' : token},
						    		dataType: 	'json',
						    		error: function(jqXhr){
						    			if( jqXhr.status === 422 ) {
										        //process validation errors here.
										        errors = jqXhr.responseJSON; //this will get the errors response data.

										        //show them somewhere in the markup
										        errorsHtml = '<ul class="list-unstyled alert alert-danger" role="alert">';
										        $.each( errors, function( key, value ) {
										            errorsHtml += '<li>' + value[0] + '</li>'; //showing only the first error.
										        });
										        errorsHtml += '</ul>';

										        // makni sve prethodne    
										        $('.myAjaxAlert').remove();
										        // ubaci ispred H1
										        $('h1').first().before( errorsHtml ); //appending to a <div id="form-errors"></div> inside form
										    }
										    if( jqXhr.status === 404 ) {
										    	alert("URL nije pronađen!");
										    }

										    return false;
										},
										success: function(data) {
							    	 	// log data to the console so we can see
							    	 	console.log(data);

								        //poruka
								        poruka = '<div class="myAjaxAlert alert alert-success">Nalog otpremljen</div>';

								        // makni sve prethodne    
								        $('.myAjaxAlert').remove();
								        // ubaci ispred H1
								        $('h1').first().before( poruka ); //appending to a <div id="form-errors"></div> inside form
								        if ( ! data.success) {
											//	foreach (data.errors)
										}
								        // IZBRIŠI RED
								        // ako je ovom closest TR-u klasa "child" - prvo maknu nju, onda zgasi red.



								        if(ovo.closest('tr').is('.child')){
								        	// zbriši taj red i red prije (ak je radi responsive dodan...)
								        	red=ovo.closest('tr').prevAll("tr:first");
								        	ovo.closest('tr').remove();								        	
								        	red.fadeOut(300, function() { $(this).remove(); });
								        } else {
								        	ovo.closest('tr').fadeOut(300, function() { $(this).remove(); });
								        }


								        return true;
								    }

								});



}		

},
});







});




    // PREBACI NALOG
    $(document).on("click",".prebaciNalog",function(){
    	//e.preventDefault();
    	ovo = $(this);
    	var url = ovo.data('myhref');
    	var token = ovo.data('token');
    	var nalog = ovo.data('nalog');
    	
    	bootbox.confirm({
    		buttons: {
    			confirm: {
    				label: 'Da'
					//,className: "btn-success"
				},
				cancel: {
					label: 'Ne'
				}

			},
    	 	size: null, // large, null
    	 	title: "PREBACI NALOG "+nalog+"?",
    	 	message: "Nalog se prebacuje u drugi servis, dobiti će status RELOKACIJA i biti će u otvorenim nalozima. Miče se sa liste za otpremu?",
    	 	callback: function(result) {
    	 		if (result) {

						    	// AJAX Call
						    	// - ovisno o trenutnom statusu (završen ili odustanak) postavi status (otpremljeno ili otpreljeno nepopravljeno)
						    	// - to bi trebala biti neka SS metoda "OTPREMI", koja će osim kaj postavi novi status, poslije i zapisivati u log...
						    	// - po success, makni ovaj row! 

						    	$.ajax({
						    		type 	: 	'POST',
						    		url 	: 	url,
						    		data 	: 	{'_token' : token},
						    		dataType: 	'json',
						    		error: function(jqXhr){
						    			if( jqXhr.status === 422 ) {
										        //process validation errors here.
										        errors = jqXhr.responseJSON; //this will get the errors response data.

										        //show them somewhere in the markup
										        errorsHtml = '<ul class="list-unstyled alert alert-danger" role="alert">';
										        $.each( errors, function( key, value ) {
										            errorsHtml += '<li>' + value[0] + '</li>'; //showing only the first error.
										        });
										        errorsHtml += '</ul>';

										        // makni sve prethodne    
										        $('.myAjaxAlert').remove();
										        // ubaci ispred H1
										        $('h1').first().before( errorsHtml ); //appending to a <div id="form-errors"></div> inside form
										    }
										    if( jqXhr.status === 404 ) {
										    	alert("URL nije pronađen!");
										    }

										    return false;
										},
										success: function(data) {
							    	 	// log data to the console so we can see
							    	 	console.log(data);

								        //poruka
								        poruka = '<div class="myAjaxAlert alert alert-success">Nalog otpremljen</div>';

								        // makni sve prethodne    
								        $('.myAjaxAlert').remove();
								        // ubaci ispred H1
								        $('h1').first().before( poruka ); //appending to a <div id="form-errors"></div> inside form
								        if ( ! data.success) {
											//	foreach (data.errors)
										}
								        // IZBRIŠI RED
								        // ako je ovom closest TR-u klasa "child" - prvo maknu nju, onda zgasi red.



								        if(ovo.closest('tr').is('.child')){
								        	// zbriši taj red i red prije (ak je radi responsive dodan...)
								        	red=ovo.closest('tr').prevAll("tr:first");
								        	ovo.closest('tr').remove();								        	
								        	red.fadeOut(300, function() { $(this).remove(); });
								        } else {
								        	ovo.closest('tr').fadeOut(300, function() { $(this).remove(); });
								        }


								        return true;
								    }

								});



}		

},
});







});



    // CONFIRM T2 REJECT - SWEET ALERT ===========
    // http://t4t5.github.io/sweetalert/
    // http://github.hubspot.com/vex/api/themes/
    // http://bootboxjs.com/examples.html
    $(".confirmReject").on("click", function (e) {
    	e.preventDefault();    	
    	var obj = $(this);
    	
    	bootbox.addLocale('tm', {
    		OK : 'Da jesam!',
    		CANCEL : 'Ne nisam!',
    		CONFIRM : 'Da jesam!'
    	}).setLocale('tm');
    	bootbox.confirm({
    	 	size: 'small', // large, null
    	 	title: "ODBACI NALOG: Jesi siguran?",
    	 	message: "u Tele2 će se poslati odbijenica i nalog će se maknuti sa liste?",
    	 	callback: function(result) {
    	 		if (result) {
    	 			var url = obj.data('myhref');
    	 			var token = obj.data('delete');
    	 			var $form = $('<form/>', {action: url, method: 'post'});
    	 			var $inputMethod = $('<input/>', {type: 'hidden', name: '_method', value: 'post'});
    	 			var $inputToken = $('<input/>', {type: 'hidden', name: '_token', value: token});
    	 			$form.append($inputMethod, $inputToken).hide().appendTo('body').submit();					
    	 			return false;
    	 		}		

    	 	},
    	 });


    });




    /**

    	 bootbox.confirm("Jesi siguran? u Tele2 će se poslati odbijenica i nalog će se maknuti sa liste?", function(result) {
			if (result) window.location = destination;
		}); 


	 * This tiny script just helps us demonstrate
	 * what the various example callbacks are doing
	 */
    /*
	var Example = (function() {
	    "use strict";

	    var elem,
	        hideHandler,
	        that = {};

	    that.init = function(options) {
	        elem = $(options.selector);
	    };

	    that.show = function(text) {
	        clearTimeout(hideHandler);

	        elem.find("span").html(text);
	        elem.delay(200).fadeIn().delay(4000).fadeOut();
	    };

	    return that;
	}());
*/

    // CONFIRM T2 REJECT - SWEET ALERT END =======







} );
