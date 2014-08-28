
    $(document).ready(function() {
        $(".incidenceRow").click(function() {

            var select = document.querySelector('.incidenceRow');
            var data   = select.dataset;
            var url    = data.url;
            url = url.replace("PLACEHOLDER", $(this).children('#id_incidence').text());

            window.open(url, "_self");

        });

        function codeNumeric(){
			if (!$.isNumeric($( "#flt_taller" ).val()) || !$.isNumeric($( "#flt_socio" ).val())) {
				event.preventDefault();

	            var select = document.querySelector('#flt_taller');
	            var data   = select.dataset;
	            var num    = data.num; //trans code.numeric
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

	        var select = document.querySelector('#flt_taller');
	        var data   = select.dataset;
	        var url    = data.url;

	        url = url.replace("plc_page", 1);
	        url = url.replace("plc_socio" , id_socio );
	        url = url.replace("plc_taller", id_taller);

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
	                var select = document.querySelector('#flt_taller');
	                var data   = select.dataset;
	                var partner= data.partner; //trans code.partner
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
	                var select = document.querySelector('#flt_taller');
	                var data   = select.dataset;
	                var partner= data.partner; //trans code.partner
	            $( "#lbl_code" ).html(partner);
	        }else{
	            codeNumeric();
	        }
	    });
	});