
$(document).ready(function() {

    //Rellena los campos socio, pais y estado del filtro de ticket o taller con la busqueda realizada
    var type    = $(document).find("#type").val();
    var from_y  = $(document).find("#type_from_y" ).val();
    var from_m  = $(document).find("#type_from_m" ).val();
    var from_d  = $(document).find("#type_from_d" ).val();
    var to_y    = $(document).find("#type_to_y"   ).val();
    var to_m    = $(document).find("#type_to_m"   ).val();
    var to_d    = $(document).find("#type_to_d"   ).val();
    var partner = $(document).find("#type_partner").val();
    var country = $(document).find("#type_country").val();
    var status  = $(document).find("#type_status" ).val();

    if(type == 'all') $(document).find("#type").val(0);

    if(type == 'ticket') {
            $("#tck_from_y" ).val(from_y );
            $("#wks_from_y" ).val('');
            $("#tck_from_m" ).val(from_m );
            $("#wks_from_m" ).val('');
            $("#tck_from_d" ).val(from_d );
            $("#wks_from_d" ).val('');
            $("#tck_to_y"   ).val(to_y   );
            $("#wks_to_y"   ).val('');
            $("#tck_to_m"   ).val(to_m   );
            $("#wks_to_m"   ).val('');
            $("#tck_to_d"   ).val(to_d   );
            $("#wks_to_d"   ).val('');
            $("#flt_tck_partner").val(partner);
            $("#flt_tck_country").val(country);
            $("#flt_tck_status" ).val(status );
    }else{
        if(type == 'workshop') {
            $("#tck_from_y" ).val('');
            $("#wks_from_y" ).val(from_y );
            $("#tck_from_m" ).val('');
            $("#wks_from_m" ).val(from_m );
            $("#tck_from_d" ).val('');
            $("#wks_from_d" ).val(from_d );
            $("#tck_to_y"   ).val('');
            $("#wks_to_y"   ).val(to_y   );
            $("#tck_to_m"   ).val('');
            $("#wks_to_m"   ).val(to_m   );
            $("#tck_to_d"   ).val('');
            $("#wks_to_d"   ).val(to_d   );
            $("#flt_wks_partner").val(partner);
            $("#flt_wks_country").val(country);
            $("#flt_wks_status" ).val(status );
        }
    }
    if(from_y  == 0 ) { $("#tck_from_y" ).val('');
                        $("#wks_from_y" ).val(''); }
    if(from_m  == 0 ) { $("#tck_from_m" ).val('');
                        $("#wks_from_m" ).val(''); }
    if(from_d  == 0 ) { $("#tck_from_d" ).val('');
                        $("#wks_from_d" ).val(''); }
    if(to_y    == 0 ) { $("#tck_to_y"   ).val('');
                        $("#wks_to_y"   ).val(''); }
    if(to_m    == 0 ) { $("#tck_to_m"   ).val('');
                        $("#wks_to_m"   ).val(''); }
    if(to_d    == 0 ) { $("#tck_to_d"   ).val('');
                        $("#wks_to_d"   ).val(''); }

//REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
    $('#btn_search_ticket').click(function() {

        var from_y  = $('#tck_from_y').val();
        var from_m  = $('#tck_from_m').val();
        var from_d  = $('#tck_from_d').val();
        var to_y    = $('#tck_to_y').val();
        var to_m    = $('#tck_to_m').val();
        var to_d    = $('#tck_to_d').val();
        var partner = $('#flt_tck_partner').val();
        var country = $('#flt_tck_country').val();
        var status  = $('#flt_tck_status').val();

        if(from_y  == "" || from_y  == 0 ) from_y  = '0';
        if(from_m  == "" || from_m  == 0 ) from_m  = '0';
        if(from_d  == "" || from_d  == 0 ) from_d  = '0';
        if(to_y    == "" || to_y    == 0 ) to_y    = '0';
        if(to_m    == "" || to_m    == 0 ) to_m    = '0';
        if(to_d    == "" || to_d    == 0 ) to_d    = '0';

        var route  = 'listStatistics';
        var locale = $(document).find("#data_locale").val();
        var url    = Routing.generate(route, {_locale: locale, type: 'ticket', page: 1, from_y: from_y, from_m: from_m, from_d: from_d, to_y: to_y, to_m: to_m, to_d: to_d, partner: partner, status: status, country: country });

        window.open(url, "_self");
    });

    //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
    $('#btn_search_workshop').click(function() {

        var from_y  = $('#wks_from_y').val();
        var from_m  = $('#wks_from_m').val();
        var from_d  = $('#wks_from_d').val();
        var to_y    = $('#wks_to_y').val();
        var to_m    = $('#wks_to_m').val();
        var to_d    = $('#wks_to_d').val();
        var partner = $('#flt_wks_partner').val();
        var country = $('#flt_wks_country').val();
        var status  = $('#flt_wks_status').val();

        if(from_y  == "" || from_y  == 0 ) from_y  = '0';
        if(from_m  == "" || from_m  == 0 ) from_m  = '0';
        if(from_d  == "" || from_d  == 0 ) from_d  = '0';
        if(to_y    == "" || to_y    == 0 ) to_y    = '0';
        if(to_m    == "" || to_m    == 0 ) to_m    = '0';
        if(to_d    == "" || to_d    == 0 ) to_d    = '0';

        var route  = 'listStatistics';
        var locale = $(document).find("#data_locale").val();
        var url    = Routing.generate(route, {_locale: locale, type: 'workshop', page: 1, from_y: from_y, from_m: from_m, from_d: from_d, to_y: to_y, to_m: to_m, to_d: to_d, partner: partner, status: status, country: country });

        window.open(url, "_self");
    });

    $('#doExcel_ticket').click(function() {

        var from_y  = $('#tck_from_y').val();
        var from_m  = $('#tck_from_m').val();
        var from_d  = $('#tck_from_d').val();
        var to_y    = $('#tck_to_y').val();
        var to_m    = $('#tck_to_m').val();
        var to_d    = $('#tck_to_d').val();
        var partner = $('#flt_tck_partner').val();
        var country = $('#flt_tck_country').val();
        var status  = $('#flt_tck_status').val();

        if(from_y  == "" || from_y  == 0 ) from_y  = '0';
        if(from_m  == "" || from_m  == 0 ) from_m  = '0';
        if(from_d  == "" || from_d  == 0 ) from_d  = '0';
        if(to_y    == "" || to_y    == 0 ) to_y    = '0';
        if(to_m    == "" || to_m    == 0 ) to_m    = '0';
        if(to_d    == "" || to_d    == 0 ) to_d    = '0';

        var route  = 'doExcel';
        var locale = $(document).find("#data_locale").val();
        var url    = Routing.generate(route, {_locale: locale, type: 'ticket', page: 1, from_y: from_y, from_m: from_m, from_d: from_d, to_y: to_y, to_m: to_m, to_d: to_d, partner: partner, status: status, country: country });

        window.open(url, "_self");
    });

    $('#doExcel_workshop').click(function() {

        var from_y  = $('#wks_from_y').val();
        var from_m  = $('#wks_from_m').val();
        var from_d  = $('#wks_from_d').val();
        var to_y    = $('#wks_to_y').val();
        var to_m    = $('#wks_to_m').val();
        var to_d    = $('#wks_to_d').val();
        var partner = $('#flt_wks_partner').val();
        var country = $('#flt_wks_country').val();
        var status  = $('#flt_wks_status').val();

        if(from_y  == "" || from_y  == 0 ) from_y  = '0';
        if(from_m  == "" || from_m  == 0 ) from_m  = '0';
        if(from_d  == "" || from_d  == 0 ) from_d  = '0';
        if(to_y    == "" || to_y    == 0 ) to_y    = '0';
        if(to_m    == "" || to_m    == 0 ) to_m    = '0';
        if(to_d    == "" || to_d    == 0 ) to_d    = '0';

        var route  = 'doExcel';
        var locale = $(document).find("#data_locale").val();
        var url    = Routing.generate(route, {_locale: locale, type: 'workshop', page: 1, from_y: from_y, from_m: from_m, from_d: from_d, to_y: to_y, to_m: to_m, to_d: to_d, partner: partner, status: status, country: country });

        window.open(url, "_self");
    });
});