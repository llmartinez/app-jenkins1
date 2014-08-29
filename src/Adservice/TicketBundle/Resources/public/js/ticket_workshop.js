
$(document).ready(function() {
	$( "#w_idpartner" ).focus();

	function codeNumeric(){
		if (($( "#w_id" ).val() != '' && !$.isNumeric($( "#w_id" ).val())) || ($( "#w_idpartner" ).val() != '' && !$.isNumeric($( "#w_idpartner" ).val()))) {
			event.preventDefault();

            var select = document.querySelector('#form_workshop_info');
            var data   = select.dataset;
            var txt    = data.numeric;
            alert(txt);
		}
	}

	$( "#btn_check" ).click(function() {

	  	if (($( "#w_id" ).val() == "" || $( "#w_idpartner" ).val() == "") && $( "#w_email" ).val() == '' && $( "#w_tel" ).val() == '' ) {
	  		event.preventDefault();

            var select = document.querySelector('#form_workshop_info');
            var data   = select.dataset;
            var txt    = data.check;

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

	            var select = document.querySelector('#form_workshop_info');
	            var data   = select.dataset;
	            var txt    = data.check;

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
