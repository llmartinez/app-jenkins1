
	function checkForBMV() {
    	var findByBMV = 0;
	    var brand    = $('#new_car_form_brand').val();
	    var model    = $('#new_car_form_model').val();
	    var version  = $('#new_car_form_version').val();
	    var system   = $('#id_system').val();
	    var subsystem= $('#new_car_form_subsystem').val();

       	if ((brand != undefined && brand != '0') || (model != undefined && model != '0') || (version != undefined && version != '0') || (system != undefined && system != '0') || (subsystem != undefined && subsystem != '0')){
       		findByBMV = 1;
		}

		return findByBMV;
	}

	function updatePageAndFindByBMV(page) {
		$('#ftbmv_page').val(page);
		$('#btn_search_by_bmv').click();
	}

    $(document).ready(function() {

    	//REDIRIGE A LA PRIMERA PAGINA
	    $('#firstpage').click(function() {

	    	var num_rows = $('#slct_historyTickets').val();
	    	var option = $('#slct_historyTickets').val();

		    var locale = $(document).find("#data_locale").val();

		    var findByBMV = checkForBMV();

		    if (findByBMV == 0) var url = Routing.generate($('#findTicketByBMV').val(), {_locale: locale, page: 1, num_rows: num_rows, option: option });
    		else 			  	var url = Routing.generate($('#slct_historyTickets').val(), {_locale: locale, page: 1, brand: brand, model: model, version: version, system: system, subsystem: subsystem, num_rows: num_rows });

	    	window.open(url, "_self");
	    });

	    //REDIRIGE A LA ANTERIOR PAGINA
	    $('#btn_anterior').click(function() {

	    	var prev_page = parseFloat($('#page').val()) - parseFloat(1);

	    	if (prev_page > 0 ) {

		    	var num_rows = $('#slct_historyTickets').val();
		    	var option = $('#slct_historyTickets').val();

			    var locale = $(document).find("#data_locale").val();

		    	var findByBMV = checkForBMV();

			    if (findByBMV == 0) var url = Routing.generate(route, {_locale: locale, page: prev_page, num_rows: num_rows, option: option });
	    		else 			  	var url = Routing.generate(route, {_locale: locale, page: prev_page, brand: brand, model: model, version: version, system: system, subsystem: subsystem, num_rows: num_rows });

		    	window.open(url, "_self");
		    }
	    });

	    //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
	    $('.change_page').click(function() {

	    	var num_rows = $('#slct_historyTickets').val();
	    	var option = $('#slct_historyTickets').val();

		    var locale = $(document).find("#data_locale").val();

		    var findByBMV = checkForBMV();

		    if (findByBMV == 0) var url = Routing.generate(route, {_locale: locale, page: $(this).text(), num_rows: num_rows, option: option });
    		else 			  	var url = Routing.generate(route, {_locale: locale, page: $(this).text(), brand: brand, model: model, version: version, system: system, subsystem: subsystem, num_rows: num_rows });

	    	window.open(url, "_self");
	    });

	    //REDIRIGE A LA SIGUIENTE PAGINA
	    $('#btn_siguiente').click(function() {

	    	var total = $('#totalpag').val();

	    	if (next_page <= total ) {

		    	var num_rows = $('#slct_historyTickets').val();
		    	var option = $('#slct_historyTickets').val();

			    var locale = $(document).find("#data_locale").val();

		    	var findByBMV = checkForBMV();

			    if (findByBMV == 0) var url = Routing.generate(route, {_locale: locale, page: next_page, num_rows: num_rows, option: option });
	    		else 			  	var url = Routing.generate(route, {_locale: locale, page: next_page, brand: brand, model: model, version: version, system: system, subsystem: subsystem, num_rows: num_rows });

		    	window.open(url, "_self");
		    }
	    });

	    //REDIRIGE A LA ULTIMA PAGINA
	    $('#totalpage').click(function() {

	    	var total = $('#totalpag').val();
	    	var num_rows = $('#slct_historyTickets').val();
	    	var option = $('#slct_historyTickets').val();

		    var locale = $(document).find("#data_locale").val();

		    var findByBMV = checkForBMV();

		    if (findByBMV == 0) var url = Routing.generate(route, {_locale: locale, page: total, num_rows: num_rows, option: option });
    		else 			  	var url = Routing.generate(route, {_locale: locale, page: total, brand: brand, model: model, version: version, system: system, subsystem: subsystem, num_rows: num_rows });

	    	window.open(url, "_self");
	    });
    });
