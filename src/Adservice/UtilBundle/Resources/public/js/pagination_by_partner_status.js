
    $(document).ready(function() {
    	//REDIRIGE A LA PRIMERA PAGINA
	    $('#firstpage').click(function() {

            var partner = $('#flt_partner').val();
            if(partner == null) var partner = $('#flt_partner_s').val();
            if(partner == null) partner = '0';
            var option = $("#option").val();
            var country = $("#flt_country").val();
            var term = $("#flt_search_term").val();
            var field = $("#flt_search_field").val();
            var status = $('#flt_status').val();
            if(status == null) status = '0';
            if(country == undefined || country == '' || country == 'none') country = '0';
            if(option == undefined || option == ''|| option == 'none') option = '0';
		    var route = $('#route').val();
		    var locale = $(document).find("#data_locale").val();
		    var url = Routing.generate(route, {_locale: locale, page: 1, country: country, partner: partner, status: status, term: term, field: field, option: option});

	    	window.open(url, "_self");
	    });

	    //REDIRIGE A LA ANTERIOR PAGINA
	    $('#btn_anterior').click(function() {

	    	var prev_page = parseFloat($('#page').val()) - parseFloat(1);

	    	if (prev_page > 0 ) {
                    
            var partner = $('#flt_partner').val();
            if(partner == null) var partner = $('#flt_partner_s').val();
            if(partner == null) partner = '0';
            var option = $("#option").val();
            var country = $("#flt_country").val();
            var term = $("#flt_search_term").val();
            var field = $("#flt_search_field").val();
            var status = $('#flt_status').val();
            if(status == null) status = '0';
            if(country == undefined || country == '' || country == 'none') country = '0';
            if(option == undefined || option == ''|| option == 'none') option = '0';
            var route = $('#route').val();
            var locale = $(document).find("#data_locale").val();
            var url = Routing.generate(route, {_locale: locale, page: prev_page, country: country, partner: partner, status: status, term: term, field: field, option: option});

	    	window.open(url, "_self");
		    }
	    });

	    //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
	    $('.change_page').click(function() {

            var partner = $('#flt_partner').val();
            if(partner == null) var partner = $('#flt_partner_s').val();
            if(partner == null) partner = '0';
            var term = $("#flt_search_term").val();
            var field = $("#flt_search_field").val();
            var status = $('#flt_status').val();
            var option = $("#option").val();
            var country = $("#flt_country").val();
            if(country == undefined || country == '' || country == 'none') country = '0';
            if(option == undefined || option == ''|| option == 'none') option = '0';
            if(status == null) status = '0';

		    var route = $('#route').val();
		    var locale = $(document).find("#data_locale").val();
		    var url = Routing.generate(route, {_locale: locale, page: $(this).text(), country: country, partner: partner, status: status, term: term, field: field, option: option});

	    	window.open(url, "_self");
	    });
            
            //REDIRIGE A LA SIGUIENTE PAGINA
	    $('#btn_siguiente').click(function() {

	    	var next_page = parseFloat($('#page').val()) + parseFloat(1);
	    	var total = $('#total').val();
	    	var country = '0';
	    	if (next_page > total ) {
                    var next_page = total;
                }                
                    var route  = $("#route" ).val();
                    var option = $("#option").val();
                    var country = $("#flt_country").val();
                    var term = $("#flt_search_term").val();
                    var field = $("#flt_search_field").val();
                    var partner = $('#flt_partner').val();
	            if(partner == null) var partner = $('#flt_partner_s').val();
	            if(partner == null) partner = '0';
                    var status = $('#flt_status').val();
	            if(status == null) status = '0';
                    if(country == undefined || country == '' || country == 'none') country = '0';
                    if(option == undefined || option == ''|| option == 'none') option = '0';
                    var locale = $(document).find("#data_locale").val();
                    var url = Routing.generate(route, {_locale: locale, page: next_page, country: country, partner: partner, status: status, term: term, field: field, option: option});

                window.open(url, "_self");

	    });           
	   

	    //REDIRIGE A LA ULTIMA PAGINA
	    $('#totalpage').click(function() {

            var partner = $('#flt_partner').val();
            if(partner == null) var partner = $('#flt_partner_s').val();
            if(partner == null) partner = '0';
            var option = $("#option").val();
            var country = $("#flt_country").val();
            var status = $('#flt_status').val();
            var term = $("#flt_search_term").val();
            var field = $("#flt_search_field").val();
            if(status == null) status = '0';
            if(country == undefined || country == '' || country == 'none') country = '0';
            if(option == undefined || option == ''|| option == 'none') option = '0';
	    	var total = $('#total').val();
		    var route = $('#route').val();
		    var locale = $(document).find("#data_locale").val();
		    var url = Routing.generate(route, {_locale: locale, page: total, country: country, partner: partner, status: status, term: term, field: field, option: option});

	    	window.open(url, "_self");
	    });
    });