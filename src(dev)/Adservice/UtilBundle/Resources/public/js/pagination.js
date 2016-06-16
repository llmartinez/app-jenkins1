
$(document).ready(function() {

	function getTotalPag(){
        var totalpag = $(document).find("#totalpag").val();
        return totalpag;
	}

	//REDIRIGE A LA PRIMERA PAGINA
    $('#firstpage').click(function() {

            var url  = $(this).attr('href');
	    	window.open(url, "_self");
    });

    //REDIRIGE A LA ANTERIOR PAGINA
    $('#btn_anterior').click(function() {

    	var prev_page = parseFloat($('#page').val()) - parseFloat(1);

    	if (prev_page > 0 ) {

            var url  = $(this).attr('href');
            window.open(url, "_self");
	    }
    });

    //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
    $('.change_page').click(function() {

            var url  = $(this).attr('href');
            window.open(url, "_self");
    });

    //REDIRIGE A LA SIGUIENTE PAGINA
    $('#btn_siguiente').click(function() {

    	var next_page = parseFloat($('#page').val()) + parseFloat(1);

    	if (next_page <= getTotalPag()) {

            var url  = $(this).attr('href');
            window.open(url, "_self");
	    }
    });

    //REDIRIGE A LA ULTIMA PAGINA
    $('#totalpage').click(function() {

            var url  = $(this).attr('href');
            window.open(url, "_self");
    });
});