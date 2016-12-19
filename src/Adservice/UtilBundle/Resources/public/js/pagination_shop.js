
    $(document).ready(function() {

    	//REDIRIGE A LA PRIMERA PAGINA
	    $('#firstpage').click(function() {

			var route   = $("#route" ).val();
			var option  = $("#option").val();
			var country = $("#flt_country").val();
			var term    = $("#flt_search_term").val();
			var field   = $("#flt_search_field").val();
			var partner = $("#flt_partner").val();
			var catserv = $("#flt_catserv").val();
			if(country == undefined || country == '' || country == 'none') country = '0';
			if(option  == undefined || option  == '' || option  == 'none') option  = '0';
			var locale = $(document).find("#data_locale").val();
			var url = Routing.generate(route, {_locale: locale, page: 1, country: country, catserv: catserv, partner: partner, term: term, field: field, option: option});

			url = url.replace("plc_page", 1);
			window.open(url, "_self");
		});

		//REDIRIGE A LA ANTERIOR PAGINA
		$('#btn_anterior').click(function() {

			var prev_page = parseFloat($('#page').val()) - parseFloat(1);
			var country = '0';

			if (prev_page > 0 ) {

			var route   = $("#route" ).val();
			var option  = $("#option").val();
			var country = $("#flt_country").val();
			var term    = $("#flt_search_term").val();
			var field   = $("#flt_search_field").val();
			var partner = $("#flt_partner").val();
			var catserv = $("#flt_catserv").val();
				if(country == undefined || country == '' || country == 'none') country = '0';
				if(option  == undefined || option  == '' || option  == 'none') option  = '0';
			var locale = $(document).find("#data_locale").val();
			var url = Routing.generate(route, {_locale: locale, page: $(this).text(), country: country, catserv: catserv, partner: partner, term: term, field: field, option: option});

                    window.open(url, "_self");
                }
	    });

	    //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
	    $('.change_page').click(function() {

			var route   = $("#route" ).val();
			var option  = $("#option").val();
			var country = $("#flt_country").val();
			var term    = $("#flt_search_term").val();
			var field   = $("#flt_search_field").val();
			var partner = $("#flt_partner").val();
			var catserv = $("#flt_catserv").val();
			if(country == undefined || country == '' || country == 'none') country = '0';
			if(option  == undefined || option  == '' || option  == 'none') option  = '0';
			var locale = $(document).find("#data_locale").val();
			var url = Routing.generate(route, {_locale: locale, page: $(this).text(), country: country, catserv: catserv, partner: partner, term: term, field: field, option: option});

			window.open(url, "_self");
	    });

	    //REDIRIGE A LA SIGUIENTE PAGINA
	    $('#btn_siguiente').click(function() {

			var total = $('#total').val();
			var next_page = parseFloat($('#page').val()) + parseFloat(1);
			if (next_page <= total ) var next_page = parseFloat($('#page').val()) + parseFloat(1);
			else 					 var next_page = total;
			var country = '0';
			var route   = $("#route" ).val();
			var option  = $("#option").val();
			var country = $("#flt_country").val();
			var term    = $("#flt_search_term").val();
			var field   = $("#flt_search_field").val();
			var partner = $("#flt_partner").val();
			var catserv = $("#flt_catserv").val();
			if(country == undefined || country == '' || country == 'none') country = '0';
			if(option  == undefined || option  == '' || option  == 'none') option  = '0';
			var locale = $(document).find("#data_locale").val();
			var url = Routing.generate(route, {_locale: locale, page: next_page, country: country, catserv: catserv, partner: partner, term: term, field: field, option: option});

            window.open(url, "_self");

	    });

	    //REDIRIGE A LA ULTIMA PAGINA
	    $('#totalpage').click(function() {

			var total   = $('#total').val();
			var route   = $("#route" ).val();
			var option  = $("#option").val();
			var country = $("#flt_country").val();
			var term    = $("#flt_search_term").val();
			var field   = $("#flt_search_field").val();
			var partner = $("#flt_partner").val();
			var catserv = $("#flt_catserv").val();
			if(country == undefined || country == '') country = '0';
			if(option  == undefined || option  == '' ) option =  '0';
			var locale = $(document).find("#data_locale").val();
			var url = Routing.generate(route, {_locale: locale, page: total, option: option, country: country, catserv: catserv, partner: partner, term: term, field: field});

	    	window.open(url, "_self");
	    });
    });