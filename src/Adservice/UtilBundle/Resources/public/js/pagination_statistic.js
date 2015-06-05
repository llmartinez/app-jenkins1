
    $(document).ready(function() {

    	//REDIRIGE A LA PRIMERA PAGINA
	    $('#firstpage').click(function() {

            var type        = $(document).find("#type").val();
			if(type == 'all') $(document).find("#type").val(0);

            if(type == 'ticket') {
		        var from_y  = $('#tck_from_y').val();
		        var from_m  = $('#tck_from_m').val();
		        var from_d  = $('#tck_from_d').val();
		        var to_y    = $('#tck_to_y').val();
		        var to_m    = $('#tck_to_m').val();
		        var to_d    = $('#tck_to_d').val();
		    	var partner = $('#flt_tck_partner').val();
	        	var country = $('#flt_tck_country').val();
	        	var status  = $('#flt_tck_status').val();
	        }else{
        		if(type == 'workshop') {
			        var from_y  = $('#wks_from_y').val();
			        var from_m  = $('#wks_from_m').val();
			        var from_d  = $('#wks_from_d').val();
			        var to_y    = $('#wks_to_y').val();
			        var to_m    = $('#wks_to_m').val();
			        var to_d    = $('#wks_to_d').val();
			    	var partner = $('#flt_wks_partner').val();
		        	var country = $('#flt_wks_country').val();
		        	var status  = $('#flt_wks_status').val();
		        }
		        else{
		        	if(type == 'no-ticket') {
				    	var partner = '0';
			        	var country = '0';
						var from_y  = '0';
						var from_m  = '0';
						var from_d  = '0';
						var to_y    = '0';
						var to_m    = '0';
						var to_d    = '0';
			        }
			        else{
			        	if(type == '0') {
					    	var partner = '0';
				        	var country = '0';
							var from_y  = '0';
							var from_m  = '0';
							var from_d  = '0';
							var to_y    = '0';
							var to_m    = '0';
							var to_d    = '0';
				        }
				    }
		        }
	        }

	        if(from_y  == "" || from_y  == 0 ) from_y  = '0';
	        if(from_m  == "" || from_m  == 0 ) from_m  = '0';
	        if(from_d  == "" || from_d  == 0 ) from_d  = '0';
	        if(to_y    == "" || to_y    == 0 ) to_y    = '0';
	        if(to_m    == "" || to_m    == 0 ) to_m    = '0';
	        if(to_d    == "" || to_d    == 0 ) to_d    = '0';

            var route  = 'listStatistics';
	        var locale = $(document).find("#data_locale").val();
	        var url    = Routing.generate(route, {_locale: locale, type: type, page: 1, from_y: from_y, from_m: from_m, from_d: from_d, to_y: to_y, to_m: to_m, to_d: to_d, partner: partner, status: status, country: country });

	    	window.open(url, "_self");
	    });

	    //REDIRIGE A LA ANTERIOR PAGINA
	    $('#btn_anterior').click(function() {

	    	var prev_page = parseFloat($('#page').val()) - parseFloat(1);

	    	if (prev_page > 0 ) {

	            var type        = $(document).find("#type").val();
				if(type == 'all') $(document).find("#type").val(0);

	            if(type == 'ticket') {
			        var from_y  = $('#tck_from_y').val();
			        var from_m  = $('#tck_from_m').val();
			        var from_d  = $('#tck_from_d').val();
			        var to_y    = $('#tck_to_y').val();
			        var to_m    = $('#tck_to_m').val();
			        var to_d    = $('#tck_to_d').val();
			    	var partner = $('#flt_tck_partner').val();
		        	var country = $('#flt_tck_country').val();
		        	var status  = $('#flt_tck_status').val();
		        }else{
        			if(type == 'workshop') {
				        var from_y  = $('#wks_from_y').val();
				        var from_m  = $('#wks_from_m').val();
				        var from_d  = $('#wks_from_d').val();
				        var to_y    = $('#wks_to_y').val();
				        var to_m    = $('#wks_to_m').val();
				        var to_d    = $('#wks_to_d').val();
				    	var partner = $('#flt_wks_partner').val();
			        	var country = $('#flt_wks_country').val();
			        	var status  = $('#flt_wks_status').val();
			        }
			        else{
			        	if(type == 'no-ticket') {
					    	var partner = '0';
				        	var country = '0';
							var from_y  = '0';
							var from_m  = '0';
							var from_d  = '0';
							var to_y    = '0';
							var to_m    = '0';
							var to_d    = '0';
				        }
				        else{
				        	if(type == '0') {
						    	var partner = '0';
					        	var country = '0';
								var from_y  = '0';
								var from_m  = '0';
								var from_d  = '0';
								var to_y    = '0';
								var to_m    = '0';
								var to_d    = '0';
					        }
					    }
			        }
		        }

		        if(from_y  == "" || from_y  == 0 ) from_y  = '0';
		        if(from_m  == "" || from_m  == 0 ) from_m  = '0';
		        if(from_d  == "" || from_d  == 0 ) from_d  = '0';
		        if(to_y    == "" || to_y    == 0 ) to_y    = '0';
		        if(to_m    == "" || to_m    == 0 ) to_m    = '0';
		        if(to_d    == "" || to_d    == 0 ) to_d    = '0';

	            var route  = 'listStatistics';
		        var locale = $(document).find("#data_locale").val();
	        	var page   = 1;
		        var url    = Routing.generate(route, {_locale: locale, type: type, page: page, from_y: from_y, from_m: from_m, from_d: from_d, to_y: to_y, to_m: to_m, to_d: to_d, partner: partner, status: status, country: country });

		    	window.open(url, "_self");
		    }
	    });

	    //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
	    $('.change_page').click(function() {

	    	var type        = $(document).find("#type").val();

			if(type == 'all') { $(document).find("#type").val(0); }

			if(type == 'ticket')
    		{
		        var from_y  = $('#tck_from_y').val();
		        var from_m  = $('#tck_from_m').val();
		        var from_d  = $('#tck_from_d').val();
		        var to_y    = $('#tck_to_y').val();
		        var to_m    = $('#tck_to_m').val();
		        var to_d    = $('#tck_to_d').val();
		    	var partner = $('#flt_tck_partner').val();
	        	var country = $('#flt_tck_country').val();
	        	var status  = $('#flt_tck_status').val();
	        }
	        else{
        		if(type == 'workshop') {
			        var from_y  = $('#wks_from_y').val();
			        var from_m  = $('#wks_from_m').val();
			        var from_d  = $('#wks_from_d').val();
			        var to_y    = $('#wks_to_y').val();
			        var to_m    = $('#wks_to_m').val();
			        var to_d    = $('#wks_to_d').val();
			    	var partner = $('#flt_wks_partner').val();
		        	var country = $('#flt_wks_country').val();
		        	var status  = $('#flt_wks_status').val();
		        }
		        else{
		        	if(type == 'no-ticket') {
				    	var partner = '0';
			        	var country = '0';
						var from_y  = '0';
						var from_m  = '0';
						var from_d  = '0';
						var to_y    = '0';
						var to_m    = '0';
						var to_d    = '0';
			        }
			        else{
			        	if(type == '0') {
					    	var partner = '0';
				        	var country = '0';
							var from_y  = '0';
							var from_m  = '0';
							var from_d  = '0';
							var to_y    = '0';
							var to_m    = '0';
							var to_d    = '0';
				        }
				    }
		        }
	        }

	        if(from_y  == "" || from_y  == 0 ) from_y  = '0';
	        if(from_m  == "" || from_m  == 0 ) from_m  = '0';
	        if(from_d  == "" || from_d  == 0 ) from_d  = '0';
	        if(to_y    == "" || to_y    == 0 ) to_y    = '0';
	        if(to_m    == "" || to_m    == 0 ) to_m    = '0';
	        if(to_d    == "" || to_d    == 0 ) to_d    = '0';

            var route  = 'listStatistics';
	        var locale = $(document).find("#data_locale").val();
	        var page   = $(this).text();
	        var url    = Routing.generate(route, {_locale: locale, type: type, page: page, from_y: from_y, from_m: from_m, from_d: from_d, to_y: to_y, to_m: to_m, to_d: to_d, partner: partner, status: status, country: country });

	    	window.open(url, "_self");
	    });

	    //REDIRIGE A LA SIGUIENTE PAGINA
	    $('#btn_siguiente').click(function() {

	    	var next_page = parseFloat($('#page').val()) + parseFloat(1);
	        var total     = $('#total').val();

	    	if (next_page <= total ) {

	    		var type        = $(document).find("#type").val();
				if(type == 'all') $(document).find("#type").val(0);

	            if(type == 'ticket') {
			        var from_y  = $('#tck_from_y').val();
			        var from_m  = $('#tck_from_m').val();
			        var from_d  = $('#tck_from_d').val();
			        var to_y    = $('#tck_to_y').val();
			        var to_m    = $('#tck_to_m').val();
			        var to_d    = $('#tck_to_d').val();
			    	var partner = $('#flt_tck_partner').val();
		        	var country = $('#flt_tck_country').val();
		        	var status  = $('#flt_tck_status').val();
		        }else{
        			if(type == 'workshop') {
				        var from_y  = $('#wks_from_y').val();
				        var from_m  = $('#wks_from_m').val();
				        var from_d  = $('#wks_from_d').val();
				        var to_y    = $('#wks_to_y').val();
				        var to_m    = $('#wks_to_m').val();
				        var to_d    = $('#wks_to_d').val();
				    	var partner = $('#flt_wks_partner').val();
			        	var country = $('#flt_wks_country').val();
			        	var status  = $('#flt_wks_status').val();
			        }
			        else{
			        	if(type == 'no-ticket') {
					    	var partner = '0';
				        	var country = '0';
							var from_y  = '0';
							var from_m  = '0';
							var from_d  = '0';
							var to_y    = '0';
							var to_m    = '0';
							var to_d    = '0';
				        }
				        else{
				        	if(type == '0') {
						    	var partner = '0';
					        	var country = '0';
								var from_y  = '0';
								var from_m  = '0';
								var from_d  = '0';
								var to_y    = '0';
								var to_m    = '0';
								var to_d    = '0';
					        }
					    }
			        }
		        }

		        if(from_y  == "" || from_y  == 0 ) from_y  = '0';
		        if(from_m  == "" || from_m  == 0 ) from_m  = '0';
		        if(from_d  == "" || from_d  == 0 ) from_d  = '0';
		        if(to_y    == "" || to_y    == 0 ) to_y    = '0';
		        if(to_m    == "" || to_m    == 0 ) to_m    = '0';
		        if(to_d    == "" || to_d    == 0 ) to_d    = '0';

	            var route  = 'listStatistics';
		        var locale = $(document).find("#data_locale").val();
		        var page   = next_page;
		        var url    = Routing.generate(route, {_locale: locale, type: type, page: page, from_y: from_y, from_m: from_m, from_d: from_d, to_y: to_y, to_m: to_m, to_d: to_d, partner: partner, status: status, country: country });

		    	window.open(url, "_self");
		    }
	    });

	    //REDIRIGE A LA ULTIMA PAGINA
	    $('#totalpage').click(function() {
				var type        = $(document).find("#type").val();
				if(type == 'all') $(document).find("#type").val(0);

	            if(type == 'ticket') {
			        var from_y  = $('#tck_from_y').val();
			        var from_m  = $('#tck_from_m').val();
			        var from_d  = $('#tck_from_d').val();
			        var to_y    = $('#tck_to_y').val();
			        var to_m    = $('#tck_to_m').val();
			        var to_d    = $('#tck_to_d').val();
			    	var partner = $('#flt_tck_partner').val();
		        	var country = $('#flt_tck_country').val();
		        	var status  = $('#flt_tck_status').val();
		        }else{
        			if(type == 'workshop') {
				        var from_y  = $('#wks_from_y').val();
				        var from_m  = $('#wks_from_m').val();
				        var from_d  = $('#wks_from_d').val();
				        var to_y    = $('#wks_to_y').val();
				        var to_m    = $('#wks_to_m').val();
				        var to_d    = $('#wks_to_d').val();
				    	var partner = $('#flt_wks_partner').val();
			        	var country = $('#flt_wks_country').val();
			        	var status  = $('#flt_wks_status').val();
			        }
			        else{
			        	if(type == 'no-ticket') {
					    	var partner = '0';
				        	var country = '0';
							var from_y  = '0';
							var from_m  = '0';
							var from_d  = '0';
							var to_y    = '0';
							var to_m    = '0';
							var to_d    = '0';
				        }
				        else{
				        	if(type == '0') {
						    	var partner = '0';
					        	var country = '0';
								var from_y  = '0';
								var from_m  = '0';
								var from_d  = '0';
								var to_y    = '0';
								var to_m    = '0';
								var to_d    = '0';
					        }
					    }
			        }
		        }

		        if(from_y  == "" || from_y  == 0 ) from_y  = '0';
		        if(from_m  == "" || from_m  == 0 ) from_m  = '0';
		        if(from_d  == "" || from_d  == 0 ) from_d  = '0';
		        if(to_y    == "" || to_y    == 0 ) to_y    = '0';
		        if(to_m    == "" || to_m    == 0 ) to_m    = '0';
		        if(to_d    == "" || to_d    == 0 ) to_d    = '0';

	            var route  = 'listStatistics';
		        var locale = $(document).find("#data_locale").val();
		        var page   = $('#total').val();
		        var url    = Routing.generate(route, {_locale: locale, type: type, page: page, from_y: from_y, from_m: from_m, from_d: from_d, to_y: to_y, to_m: to_m, to_d: to_d, partner: partner, status: status, country: country });


	    	window.open(url, "_self");
	    });
    });