
    $(document).ready(function() {
    	//REDIRIGE A LA PRIMERA PAGINA
	    $('#firstpage').click(function() {

	    	var route = $('#slct_historyTickets').val();
	    	var num_rows = $('#slct_historyTickets').val();
	    	var option = $('#slct_historyTickets').val();

		    var locale = $(document).find("#data_locale").val();
		    var url = Routing.generate(route, {_locale: locale, page: 1, num_rows: num_rows, option: option });

	    	window.open(url, "_self");
	    });

	    //REDIRIGE A LA ANTERIOR PAGINA
	    $('#btn_anterior').click(function() {

	    	var prev_page = parseFloat($('#page').val()) - parseFloat(1);

	    	if (prev_page > 0 ) {

		    	var route = $('#slct_historyTickets').val();
		    	var num_rows = $('#slct_historyTickets').val();
		    	var option = $('#slct_historyTickets').val();

			    var locale = $(document).find("#data_locale").val();
			    var url = Routing.generate(route, {_locale: locale, page: prev_page, num_rows: num_rows, option: option });

		    	window.open(url, "_self");
		    }
	    });

	    //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
	    $('.change_page').click(function() {

	    	var route = $('#slct_historyTickets').val();
	    	var num_rows = $('#slct_historyTickets').val();
	    	var option = $('#slct_historyTickets').val();

		    var locale = $(document).find("#data_locale").val();
		    var url = Routing.generate(route, {_locale: locale, page: $(this).text(), num_rows: num_rows, option: option });

	    	window.open(url, "_self");
	    });

	    //REDIRIGE A LA SIGUIENTE PAGINA
	    $('#btn_siguiente').click(function() {

	    	var total = $('#totalpag').val();

	    	if (next_page <= total ) {

		    	var route = $('#slct_historyTickets').val();
		    	var num_rows = $('#slct_historyTickets').val();
		    	var option = $('#slct_historyTickets').val();

			    var locale = $(document).find("#data_locale").val();
			    var url = Routing.generate(route, {_locale: locale, page: next_page, num_rows: num_rows, option: option });

		    	window.open(url, "_self");
		    }
	    });

	    //REDIRIGE A LA ULTIMA PAGINA
	    $('#totalpage').click(function() {

	    	var total = $('#totalpag').val();
	    	var route = $('#slct_historyTickets').val();
	    	var num_rows = $('#slct_historyTickets').val();
	    	var option = $('#slct_historyTickets').val();

		    var locale = $(document).find("#data_locale").val();
		    var url = Routing.generate(route, {_locale: locale, page: total, num_rows: num_rows, option: option });

	    	window.open(url, "_self");
	    });
    });
