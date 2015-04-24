
$(document).ready(function() {

    //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
    $('#btn_name').click(function() {

            var route   = $('#route').val();
            var name = $('#flt_name').val();
            if(name == null) name = '0';
            var country = $('#flt_country').val();
            if(country == null) country = '0';

            var locale = $(document).find("#data_locale").val();
            var url = Routing.generate(route, {_locale: locale, page: 1, w_idpartner: '0', w_id: '0', country: country, partner: '0', status: '0', name: name });

            window.open(url, "_self");
    });

	$( "#w_id" ).on( "keydown", function( event ) {

	  	if (event.which == 13) {
	  		if ($( "#w_idpartner" ).val() == "") {
		  		event.preventDefault();

	           var txt    = $( "#code_partner").val();

				$( "#w_idpartner" ).focus();
				$( "#lbl_code" ).html(txt);
			}
			else{
	            var route   = $('#route').val();
	            var w_idpartner = $('#w_idpartner').val();
	            var w_id = $('#w_id').val();
	            if(w_idpartner == null) w_idpartner = '0';
	            if(w_id == null) w_id = '0';

	            var locale = $(document).find("#data_locale").val();
	            var url = Routing.generate(route, {_locale: locale, page: 1, w_idpartner: w_idpartner, w_id: w_id, country: '0', partner: '0', status: '0' });

        		window.open(url, "_self");
			}
	  	}
	});

	$( "#w_idpartner" ).on( "keydown", function( event ) {

		if (event.which == 13) {
		  	event.preventDefault();
		  	$( "#w_id" ).focus();
		}
	});

    //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
    $('#flt_country').change(function() {

            var route   = $('#route').val();
            var country = $('#flt_country').val();
            if(country == null) country = '0';
            var partner = $('#flt_partner').val();
            if(partner == null) partner = '0';
            var status = $('#flt_status').val();
            if(status == null) status = '0';

            var locale = $(document).find("#data_locale").val();
            var url = Routing.generate(route, {_locale: locale, page: 1, w_idpartner: '0', w_id: '0', country: country, partner: partner, status: status });

        	window.open(url, "_self");
    });
    //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
    $('#flt_partner').change(function() {

            var route   = $('#route').val();
            var country = $('#flt_country').val();
            if(country == null) country = '0';
            var partner = $('#flt_partner').val();
            if(partner == null) partner = '0';
            var status = $('#flt_status').val();
            if(status == null) status = '0';

            var locale = $(document).find("#data_locale").val();
            var url = Routing.generate(route, {_locale: locale, page: 1, w_idpartner: '0', w_id: '0', country: country, partner: partner, status: status });

    		window.open(url, "_self");
    });
    //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
    $('#flt_status').change(function() {

            var route   = $('#route').val();
            var country = $('#flt_country').val();
            if(country == null) country = '0';
            var partner = $('#flt_partner').val();
            if(partner == null) partner = '0';
            var status = $('#flt_status').val();
            if(status == null) status = '0';

            var locale = $(document).find("#data_locale").val();
            var url = Routing.generate(route, {_locale: locale, page: 1, w_idpartner: '0', w_id: '0', country: country, partner: partner, status: status });

       		window.open(url, "_self");
    });
});
