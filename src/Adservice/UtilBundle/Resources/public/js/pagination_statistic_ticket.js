
    $(document).ready(function() {

    	//REDIRIGE A LA PRIMERA PAGINA
	    $('#firstpage').click(function() {

	        var from_y  = $('#tck_from_y').val();
	        var from_m  = $('#tck_from_m').val();
	        var from_d  = $('#tck_from_d').val();
	        var to_y    = $('#tck_to_y').val();
	        var to_m    = $('#tck_to_m').val();
	        var to_d    = $('#tck_to_d').val();
	    	var partner = $('#flt_tck_partner').val();
	    	var status  = $('#flt_tck_status').val();

	        if(from_y  == ""  ) from_y  = 'none';
	        if(from_m  == ""  ) from_m  = 'none';
	        if(from_d  == ""  ) from_d  = 'none';
	        if(to_y    == ""  ) to_y    = 'none';
	        if(to_m    == ""  ) to_m    = 'none';
	        if(to_d    == ""  ) to_d    = 'none';
	    	if(partner == null) partner = 'none';
	    	if(status  == null) status  = 'none';

            var select = document.querySelector('#pagination');
            var data   = select.dataset;
            var url    = data.url;

	        url = url.replace("plc_page", 1);
	        url = url.replace("plc_from_m", from_m);
	        url = url.replace("plc_from_d", from_d);
	        url = url.replace("plc_to_y", to_y);
	        url = url.replace("plc_to_m", to_m);
	        url = url.replace("plc_to_d", to_d);
	    	url = url.replace("plc_partner", partner);
	    	url = url.replace("plc_status", status);

	    	window.open(url, "_self");
	    });

	    //REDIRIGE A LA ANTERIOR PAGINA
	    $('#btn_anterior').click(function() {

	    	var prev_page = parseFloat($('#page').val()) - parseFloat(1);

	    	if (prev_page > 0 ) {

	    		var from_y  = $('#tck_from_y').val();
		        var from_m  = $('#tck_from_m').val();
		        var from_d  = $('#tck_from_d').val();
		        var to_y    = $('#tck_to_y').val();
		        var to_m    = $('#tck_to_m').val();
		        var to_d    = $('#tck_to_d').val();
	    		var partner = $('#flt_tck_partner').val();

	    		var status  = $('#flt_tck_status').val();

		        if(from_y  == ""  ) from_y  = 'none';
		        if(from_m  == ""  ) from_m  = 'none';
		        if(from_d  == ""  ) from_d  = 'none';
		        if(to_y    == ""  ) to_y    = 'none';
		        if(to_m    == ""  ) to_m    = 'none';
		        if(to_d    == ""  ) to_d    = 'none';
		    	if(partner == null) partner = 'none';

		    	if(status  == null) status  = 'none';

	            var select = document.querySelector('#pagination');
	            var data   = select.dataset;
	            var url    = data.url;

		    	url = url.replace("plc_page", prev_page);
		        url = url.replace("plc_from_y", from_y);
		        url = url.replace("plc_from_m", from_m);
		        url = url.replace("plc_from_d", from_d);
		        url = url.replace("plc_to_y", to_y);
		        url = url.replace("plc_to_m", to_m);
		        url = url.replace("plc_to_d", to_d);
		    	url = url.replace("plc_partner", partner);
		    	url = url.replace("plc_status", status);

		    	window.open(url, "_self");
		    }
	    });

	    //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
	    $('.change_page').click(function() {

	    	var from_y  = $('#tck_from_y').val();
	        var from_m  = $('#tck_from_m').val();
	        var from_d  = $('#tck_from_d').val();
	        var to_y    = $('#tck_to_y').val();
	        var to_m    = $('#tck_to_m').val();
	        var to_d    = $('#tck_to_d').val();
	    	var partner = $('#flt_tck_partner').val();
	    	var status  = $('#flt_tck_status').val();

	        if(from_y  == ""  ) from_y  = 'none';
	        if(from_m  == ""  ) from_m  = 'none';
	        if(from_d  == ""  ) from_d  = 'none';
	        if(to_y    == ""  ) to_y    = 'none';
	        if(to_m    == ""  ) to_m    = 'none';
	        if(to_d    == ""  ) to_d    = 'none';
	    	if(partner == null) partner = 'none';
	    	if(status  == null) status  = 'none';

            var select = document.querySelector('#pagination');
            var data   = select.dataset;
            var url    = data.url;

	    	url = url.replace("plc_page", $(this).text());

	        url = url.replace("plc_from_y", from_y);
	        url = url.replace("plc_from_m", from_m);
	        url = url.replace("plc_from_d", from_d);
	        url = url.replace("plc_to_y", to_y);
	        url = url.replace("plc_to_m", to_m);
	        url = url.replace("plc_to_d", to_d);
	    	url = url.replace("plc_partner", partner);
	    	url = url.replace("plc_status", status);


	    	window.open(url, "_self");
	    });

	    //REDIRIGE A LA SIGUIENTE PAGINA
	    $('#btn_siguiente').click(function() {

	    	var next_page = parseFloat($('#page').val()) + parseFloat(1);

		        var select = document.querySelector('#pagination');
		        var data   = select.dataset;
        		var total  = data.total;
	            var url    = data.url;

	    	if (next_page <= total ) {

	    		var from_y  = $('#tck_from_y').val();
		        var from_m  = $('#tck_from_m').val();
		        var from_d  = $('#tck_from_d').val();
		        var to_y    = $('#tck_to_y').val();
		        var to_m    = $('#tck_to_m').val();
		        var to_d    = $('#tck_to_d').val();
	    		var partner = $('#flt_tck_partner').val();

	    		var status  = $('#flt_tck_status').val();

		        if(from_y  == ""  ) from_y  = 'none';
		        if(from_m  == ""  ) from_m  = 'none';
		        if(from_d  == ""  ) from_d  = 'none';
		        if(to_y    == ""  ) to_y    = 'none';
		        if(to_m    == ""  ) to_m    = 'none';
	        	if(to_d    == ""  ) to_d    = 'none';
		    	if(partner == null) partner = 'none';
		    	if(status  == null) status  = 'none';

		    	url = url.replace("plc_page", next_page);
		        url = url.replace("plc_from_y", from_y);
		        url = url.replace("plc_from_m", from_m);
		        url = url.replace("plc_from_d", from_d);
		        url = url.replace("plc_to_y", to_y);
		        url = url.replace("plc_to_m", to_m);
		        url = url.replace("plc_to_d", to_d);
		    	url = url.replace("plc_partner", partner);

		    	url = url.replace("plc_status", status);


		    	window.open(url, "_self");
		    }
	    });

	    //REDIRIGE A LA ULTIMA PAGINA
	    $('#totalpage').click(function() {

	    	var from_y  = $('#tck_from_y').val();
	        var from_m  = $('#tck_from_m').val();
	        var from_d  = $('#tck_from_d').val();
	        var to_y    = $('#tck_to_y').val();
	        var to_m    = $('#tck_to_m').val();
	        var to_d    = $('#tck_to_d').val();
	    	var partner = $('#flt_tck_partner').val();
	    	var status  = $('#flt_tck_status').val();

	        if(from_y  == ""  ) from_y  = 'none';
	        if(from_m  == ""  ) from_m  = 'none';
	        if(from_d  == ""  ) from_d  = 'none';
	        if(to_y    == ""  ) to_y    = 'none';
	        if(to_m    == ""  ) to_m    = 'none';
	        if(to_d    == ""  ) to_d    = 'none';
	    	if(partner == null) partner = 'none';
	    	if(status  == null) status  = 'none';

	        var select = document.querySelector('#pagination');
	        var data   = select.dataset;
    		var total  = data.total;
            var url    = data.url;

	        url = url.replace("plc_page", total);
	        url = url.replace("plc_from_y", from_y);
	        url = url.replace("plc_from_m", from_m);
	        url = url.replace("plc_from_d", from_d);
	        url = url.replace("plc_to_y", to_y);
	        url = url.replace("plc_to_m", to_m);
	        url = url.replace("plc_to_d", to_d);
	    	url = url.replace("plc_partner", partner);
	    	url = url.replace("plc_status", status);


	    	window.open(url, "_self");
	    });
    });