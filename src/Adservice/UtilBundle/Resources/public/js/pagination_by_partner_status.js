
    $(document).ready(function() {
    	//REDIRIGE A LA PRIMERA PAGINA
	    $('#firstpage').click(function() {

            var partner = $('#flt_partner_w').val();
            if(partner == null) var partner = $('#flt_partner_s').val();
            if(partner == null) partner = 'none';

            var status = $('#flt_status').val();
            if(status == null) status = 'none';

		    var route = $('#route').val();
		    var locale = $(document).find("#data_locale").val();
		    var url = Routing.generate(route, {_locale: locale, page: 1, partner: partner, status: status });

	    	window.open(url, "_self");
	    });

	    //REDIRIGE A LA ANTERIOR PAGINA
	    $('#btn_anterior').click(function() {

	    	var prev_page = parseFloat($('#page').val()) - parseFloat(1);

	    	if (prev_page > 0 ) {

            var partner = $('#flt_partner_w').val();
            if(partner == null) var partner = $('#flt_partner_s').val();
            if(partner == null) partner = 'none';

            var status = $('#flt_status').val();
            if(status == null) status = 'none';

		    var route = $('#route').val();
		    var locale = $(document).find("#data_locale").val();
		    var url = Routing.generate(route, {_locale: locale, page: prev_page, partner: partner, status: status });

	    	window.open(url, "_self");
		    }
	    });

	    //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
	    $('.change_page').click(function() {

            var partner = $('#flt_partner_w').val();
            if(partner == null) var partner = $('#flt_partner_s').val();
            if(partner == null) partner = 'none';

            var status = $('#flt_status').val();
            if(status == null) status = 'none';

		    var route = $('#route').val();
		    var locale = $(document).find("#data_locale").val();
		    var url = Routing.generate(route, {_locale: locale, page: $(this).text(), partner: partner, status: status });

	    	window.open(url, "_self");
	    });

	    //REDIRIGE A LA SIGUIENTE PAGINA
	    $('#btn_siguiente').click(function() {

	    	var next_page = parseFloat($('#page').val()) + parseFloat(1);
	    	var total = $('#total').val();

	    	if (next_page <= total ) {

	            var partner = $('#flt_partner_w').val();
	            if(partner == null) var partner = $('#flt_partner_s').val();
	            if(partner == null) partner = 'none';

	            var status = $('#flt_status').val();
	            if(status == null) status = 'none';

			    var route = $('#route').val();
			    var locale = $(document).find("#data_locale").val();
			    var url = Routing.generate(route, {_locale: locale, page: next_page, partner: partner, status: status });

		    	window.open(url, "_self");
		    }
	    });

	    //REDIRIGE A LA ULTIMA PAGINA
	    $('#totalpage').click(function() {

            var partner = $('#flt_partner_w').val();
            if(partner == null) var partner = $('#flt_partner_s').val();
            if(partner == null) partner = 'none';

            var status = $('#flt_status').val();
            if(status == null) status = 'none';

	    	var total = $('#total').val();
		    var route = $('#route').val();
		    var locale = $(document).find("#data_locale").val();
		    var url = Routing.generate(route, {_locale: locale, page: total, partner: partner, status: status });

	    	window.open(url, "_self");
	    });
    });