
    // $(document).ready(function() {
    // 	//REDIRIGE A LA PRIMERA PAGINA
	   //  $('#firstpage').click(function() {

	   //      var select = document.querySelector('#pagination');
	   //      var data   = select.dataset;
	   //      var option = data.option;
	   //      var url    = data.url;

		  //   var route = 'regions_from_country';
		  //   var locale = $(document).find("#data_locale").val();
		  //   var url = Routing.generate(route, {_locale: locale, page: 1, option: option });

	   //  	window.open(url, "_self");
	   //  });

	   //  //REDIRIGE A LA ANTERIOR PAGINA
	   //  $('#btn_anterior').click(function() {

	   //  	var prev_page = parseFloat($('#page').val()) - parseFloat(1);

	   //  	if (prev_page > 0 ) {

		  //       var select = document.querySelector('#pagination');
		  //       var data   = select.dataset;
		  //       var option = data.option;
		  //       var url    = data.url;
		  //   	url = url.replace("plc_page", prev_page);
		  //   	url = url.replace("plc_option", option);

		  //   	window.open(url, "_self");
		  //   }
	   //  });

	   //  //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
	   //  $('.change_page').click(function() {

	   //      var select = document.querySelector('#pagination');
	   //      var data   = select.dataset;
	   //      var option = data.option;
	   //      var url    = data.url;
	   //  	url = url.replace("plc_page", $(this).text());
	   //  	url = url.replace("plc_option", option);

	   //  	window.open(url, "_self");
	   //  });

	   //  //REDIRIGE A LA SIGUIENTE PAGINA
	   //  $('#btn_siguiente').click(function() {

	   //  	var next_page = parseFloat($('#page').val()) + parseFloat(1);
	   //      var select = document.querySelector('#pagination');
	   //      var data   = select.dataset;
	   //      var option = data.option;
	   //      var total  = data.total;
	   //      var url    = data.url;

	   //  	if (next_page <= total ) {

		  //   	url = url.replace("plc_page", next_page);
	   //  		url = url.replace("plc_option", option);

		  //   	window.open(url, "_self");
		  //   }
	   //  });

	   //  //REDIRIGE A LA ULTIMA PAGINA
	   //  $('#totalpage').click(function() {

	   //      var select = document.querySelector('#pagination');
	   //      var data   = select.dataset;
	   //      var option = data.option;
	   //      var total  = data.total;
	   //      var url    = data.url;
	   //  	url = url.replace("plc_page", total);
	   //  	url = url.replace("plc_option", option);

	   //  	window.open(url, "_self");
	   //  });
    // });