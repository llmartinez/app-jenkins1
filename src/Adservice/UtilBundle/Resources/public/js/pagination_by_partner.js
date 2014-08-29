
    $(document).ready(function() {
    	//REDIRIGE A LA PRIMERA PAGINA
	    $('#firstpage').click(function() {

            var partner = $('#flt_partner').val();
            if(partner == null) partner = 'none';

            var select = document.querySelector('#pagination');
            var data   = select.dataset;
            var url    = data.url;
	    	url = url.replace("plc_page", 1);
            url = url.replace("plc_partner", partner);

	    	window.open(url, "_self");
	    });

	    //REDIRIGE A LA ANTERIOR PAGINA
	    $('#btn_anterior').click(function() {

	    	var prev_page = parseFloat($('#page').val()) - parseFloat(1);

	    	if (prev_page > 0 ) {

            var partner = $('#flt_partner').val();
            if(partner == null) partner = 'none';

	            var select = document.querySelector('#pagination');
	            var data   = select.dataset;
	            var url    = data.url;
		    	url = url.replace("plc_page", prev_page);
            	url = url.replace("plc_partner", partner);

		    	window.open(url, "_self");
		    }
	    });

	    //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
	    $('.change_page').click(function() {

            var partner = $('#flt_partner').val();
            if(partner == null) partner = 'none';

            var select = document.querySelector('#pagination');
            var data   = select.dataset;
            var url    = data.url;
	    	url = url.replace("plc_page", $(this).text());
            url = url.replace("plc_partner", partner);

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

            var partner = $('#flt_partner').val();
            if(partner == null) partner = 'none';

		    	url = url.replace("plc_page", next_page);
           		url = url.replace("plc_partner", partner);

		    	window.open(url, "_self");
		    }
	    });

	    //REDIRIGE A LA ULTIMA PAGINA
	    $('#totalpage').click(function() {

            var partner = $('#flt_partner').val();
            if(partner == null) partner = 'none';

            var select = document.querySelector('#pagination');
            var data   = select.dataset;
        	var total  = data.total;
            var url    = data.url;
		    	url = url.replace("plc_page", total);
            url = url.replace("plc_partner", partner);

	    	window.open(url, "_self");
	    });
    });