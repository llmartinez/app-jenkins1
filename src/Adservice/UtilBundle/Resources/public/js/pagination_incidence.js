
    $(document).ready(function() {
    	//REDIRIGE A LA PRIMERA PAGINA
	    $('#firstpage').click(function() {

		    var route = 'list_incidences';
		    var locale = $(document).find("#data_locale").val();
		    var id_socio  = $("#id_socio" ).val();
		    var id_taller = $("#id_taller").val();

	        if(id_socio  != 'none') $("#flt_socio" ).val( id_socio  );
	        if(id_taller != 'none') $("#flt_taller").val( id_taller );

		    var url = Routing.generate(route, {_locale: locale, page: 1, id_socio: id_socio, id_taller: id_taller });
	    	window.open(url, "_self");
	    });

	    //REDIRIGE A LA ANTERIOR PAGINA
	    $('#btn_anterior').click(function() {

	    	var prev_page = parseFloat($('#page').val()) - parseFloat(1);

	    	if (prev_page > 0 ) {

			    var route = 'list_incidences';
			    var locale = $(document).find("#data_locale").val();
			    var id_socio  = $("#id_socio" ).val();
			    var id_taller = $("#id_taller").val();

		        if(id_socio  != 'none') $("#flt_socio" ).val( id_socio  );
		        if(id_taller != 'none') $("#flt_taller").val( id_taller );

			    var url = Routing.generate(route, {_locale: locale, page: prev_page, id_socio: id_socio, id_taller: id_taller });

		    	window.open(url, "_self");
		    }
	    });

	    //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
	    $('.change_page').click(function() {

			    var route = 'list_incidences';
			    var locale = $(document).find("#data_locale").val();
			    var id_socio  = $("#id_socio" ).val();
			    var id_taller = $("#id_taller").val();

		        if(id_socio  != 'none') $("#flt_socio" ).val( id_socio  );
		        if(id_taller != 'none') $("#flt_taller").val( id_taller );

			    var url = Routing.generate(route, {_locale: locale, page: $(this).text(), id_socio: id_socio, id_taller: id_taller });

		    	window.open(url, "_self");
	    });

	    //REDIRIGE A LA SIGUIENTE PAGINA
	    $('#btn_siguiente').click(function() {

	    	var next_page = parseFloat($('#page').val()) + parseFloat(1);
	    	var total = $('#total').val();

	    	if (next_page <= total ) {

			    var route = 'list_incidences';
			    var locale = $(document).find("#data_locale").val();
			    var id_socio  = $("#id_socio" ).val();
			    var id_taller = $("#id_taller").val();

		        if(id_socio  != 'none') $("#flt_socio" ).val( id_socio  );
		        if(id_taller != 'none') $("#flt_taller").val( id_taller );

			    var url = Routing.generate(route, {_locale: locale, page: next_page, id_socio: id_socio, id_taller: id_taller });

		    	window.open(url, "_self");
		    }
	    });

	    //REDIRIGE A LA ULTIMA PAGINA
	    $('#totalpage').click(function() {

	    	var total = $('#total').val();
		    var route = 'list_incidences';
		    var locale = $(document).find("#data_locale").val();
		    var id_socio  = $("#id_socio" ).val();
		    var id_taller = $("#id_taller").val();

	        if(id_socio  != 'none') $("#flt_socio" ).val( id_socio  );
	        if(id_taller != 'none') $("#flt_taller").val( id_taller );

		    var url = Routing.generate(route, {_locale: locale, page: total, id_socio: id_socio, id_taller: id_taller });

	    	window.open(url, "_self");
	    });
    });
