
    $(document).ready(function() {

    	//REDIRIGE A LA PRIMERA PAGINA
	    $('#firstpage').click(function() {

		    var route = $("#route" ).val();
		    var locale = $(document).find("#data_locale").val();
		    var url = Routing.generate(route, {_locale: locale, page: 1 });

	    	url = url.replace("plc_page", 1);
	    	window.open(url, "_self");
	    });

	    //REDIRIGE A LA ANTERIOR PAGINA
	    $('#btn_anterior').click(function() {

	    	var prev_page = parseFloat($('#page').val()) - parseFloat(1);

	    	if (prev_page > 0 ) {

			    var route = $("#route" ).val();
			    var locale = $(document).find("#data_locale").val();
			    var url = Routing.generate(route, {_locale: locale, page: prev_page });

		    	window.open(url, "_self");
		    }
	    });

	    //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
	    $('.change_page').click(function() {

		    var route = $("#route" ).val();
		    var locale = $(document).find("#data_locale").val();
		    var url = Routing.generate(route, {_locale: locale, page: $(this).text() });

	    	window.open(url, "_self");
	    });

	    //REDIRIGE A LA SIGUIENTE PAGINA
	    $('#btn_siguiente').click(function() {

	    	var next_page = parseFloat($('#page').val()) + parseFloat(1);
	    	var total = $('#total').val();

	    	if (next_page <= total ) {

			    var route = $("#route" ).val();
			    var locale = $(document).find("#data_locale").val();
			    var url = Routing.generate(route, {_locale: locale, page: next_page });

		    	window.open(url, "_self");
		    }
	    });

	    //REDIRIGE A LA ULTIMA PAGINA
	    $('#totalpage').click(function() {

	    	var total = $('#total').val();
		    var route = $("#route" ).val();
		    var locale = $(document).find("#data_locale").val();
		    var url = Routing.generate(route, {_locale: locale, page: total });

	    	window.open(url, "_self");
	    });
    });