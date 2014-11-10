
    $(document).ready(function() {

        $( "#flt_socio" ).focus();
	    var id_socio  = $("#id_socio" ).val();
	    var id_taller = $("#id_taller").val();
        if(id_socio  != 'none') $("#flt_socio" ).val( id_socio  );
        if(id_taller != 'none') $("#flt_taller").val( id_taller );

        $(".incidenceRow").click(function() {

    		var route = 'show_incidence';
		    var locale = $(document).find("#data_locale").val();
		    var id_incidence = $("#id_incidence").val();

            var url    = Routing.generate(route, {_locale: locale, id_incidence: id_incidence });

            window.open(url, "_self");

        });

        function codeNumeric(){
			if (!$.isNumeric($( "#flt_taller" ).val()) || !$.isNumeric($( "#flt_socio" ).val())) {
				event.preventDefault();

		    	var num = $("#code_numeric").val();
				alert(num);
			}else{
	            list_incidences();
	        }
		}

	    function list_incidences() {
	        var id_socio  = $('#flt_socio' ).val();
	        var id_taller = $('#flt_taller').val();

	        if( id_socio  == null ) id_socio  = 'none';
	        if( id_taller == null ) id_taller = 'none';

		    var route = 'list_incidences';
		    var locale = $(document).find("#data_locale").val();
		    var url = Routing.generate(route, {_locale: locale, page: 1, id_taller: id_taller, id_socio: id_socio });

	        window.open(url, "_self");
	    }

		$( "#flt_socio" ).on( "keydown", function( event ) {

			if (event.which == 13) {
			  	event.preventDefault();
			  	$( "#flt_taller" ).focus();
			}
		});

		$( "#flt_taller" ).on( "keydown", function( event ) {

		  	if (event.which == 13) {
		  		if ($( "#flt_socio" ).val() == "") {
			  		event.preventDefault();
					$( "#flt_socio" ).focus();
		    		var partner = $("#code_partner").val();
					$( "#lbl_code" ).html(partner);
				}else{
		  			codeNumeric();
		  		}
		  	}
		});

	    //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
	    $('#btn_search').click(function() {

	        if ($( "#flt_taller" ).val() == "" || $( "#flt_socio" ).val() == "") {
	            event.preventDefault();
	            $( "#flt_socio" ).focus();
	    		var partner = $("#code_partner").val();
	            $( "#lbl_code" ).html(partner);
	        }else{
	            codeNumeric();
	        }
	    });
	});