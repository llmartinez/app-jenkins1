
$(document).ready(function() {
	$( "#w_idpartner" ).focus();

	function codeNumeric(){
		if ($( "#w_id").val() != '' && $( "#w_idpartner" ).val() != '' && (!$.isNumeric($( "#w_id" ).val()) || !$.isNumeric($( "#w_idpartner" ).val()))) {
			event.preventDefault();

            var txt    = $( "#code_numeric").val();
            alert(txt);
		}
	}

	$( "#btn_check" ).click(function() {

	  	if (($( "#w_id"    ).val() == "" || $( "#w_idpartner" ).val() == "")
	  	  && $( "#w_name"  ).val() == '' && $( "#w_cif" ).val() == ''
	  	  && $( "#w_email" ).val() == '' && $( "#w_tel" ).val() == ''
	  	  && $( "#w_region").val() == ''
	  	  ) {
	  		event.preventDefault();

            var txt    = $( "#code_partner").val();

			$( "#w_idpartner" ).focus();
			$( "#lbl_code" ).html(txt);
	  	}else{
	  		codeNumeric();
	  	}
	});

	$( "#w_id" ).on( "keydown", function( event ) {

	  	if (event.which == 13) {
	  		if ($( "#w_idpartner" ).val() == "") {
		  		event.preventDefault();

	           var txt    = $( "#code_partner").val();

				$( "#w_idpartner" ).focus();
				$( "#lbl_code" ).html(txt);
			}else{
	  			codeNumeric();
	  		}
	  	}
	});

	$( "#w_idpartner" ).on( "keydown", function( event ) {

		if (event.which == 13) {
		  	event.preventDefault();
		  	$( "#w_id" ).focus();
		}
	});

    // $('#MainContent').find('.glyphicon-trash').click(function() {
    //     var workshop_id = $(this).data('id');
    //     confirm_delete_workshop_modal(workshop_id);
    // });
});
