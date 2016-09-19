$(document).ready( function () {



	$("#historyModal").on("click",".detailsModalBtn", function(){

		var token =  $(this).data('token');
		var order = $(this).data('order');
		var action = $(this).data('action');
		var status = $(this).data('status');
		var pivotid = $(this).data('pivotid');
		var poruka = false;

		switch(action){
			
			case("setReturnedData"): 
				poruka="Upiši 'SET' za postavljanje novih podataka";
				potvrda="SET";
				break;
			case("deleteReturnData"): 
				poruka="Upiši 'DELETE' za brisanje podataka o dostavi";
				potvrda="DELETE";
				break;
			case("deleteClosingDate"): 
				poruka="Upiši 'DELETE' za brisanje podataka o zatvaranju";
				potvrda="DELETE";
				break;
			case("deleteStatus"): 
				poruka="Upiši 'DELETE' za brisanje statusa";
				potvrda="DELETE";
				break;
			default:
				break;
		}


		if (poruka && prompt(poruka)==potvrda){
			// 

	       	$.ajax({
		       	url:'/detailsModalBtn',
		       	type: 'post',
		       	data: { _token :token, order:order, action:action, pivotid:pivotid, status:status},
		       	success:function(returneddata){


		       		if (returneddata.postaction == 'reload') {

		       			// napuni modal iz početka
		       			$("#historyModal").load("/drawHistoryModal/"+returneddata.postdata['id']);

		       		}

			        if ( ! returneddata.success) {
						//	foreach (data.errors)
					}

					//alert(returneddata.msg);

		       	}
	       	});

		} else {
			// niš

		}

	});





})