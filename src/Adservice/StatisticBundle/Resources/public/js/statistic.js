
$(document).ready(function() {
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

        if(from_y  == ""  ) from_y  = 'none';
        if(from_m  == ""  ) from_m  = 'none';
        if(from_d  == ""  ) from_d  = 'none';
        if(to_y    == ""  ) to_y    = 'none';
        if(to_m    == ""  ) to_m    = 'none';
        if(to_d    == ""  ) to_d    = 'none';
        if(partner == null) partner = 'none';
        if(status  == null) status  = 'none';
        if(country == null) country = 'none';

        var select = document.querySelector('#btn_search_ticket');
        var data   = select.dataset;
        var url    = data.url;

        url = url.replace("plc_page", 1);
        url = url.replace("plc_from_y", from_y);
        url = url.replace("plc_from_m", from_m);
        url = url.replace("plc_from_d", from_d);
        url = url.replace("plc_to_y", to_y);
        url = url.replace("plc_to_m", to_m);
        url = url.replace("plc_to_d", to_d);
        url = url.replace("plc_partner", partner);
        url = url.replace("plc_country", country);
        url = url.replace("plc_status", status);

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

        if(from_y  == ""  ) from_y  = 'none';
        if(from_m  == ""  ) from_m  = 'none';
        if(from_d  == ""  ) from_d  = 'none';
        if(to_y    == ""  ) to_y    = 'none';
        if(to_m    == ""  ) to_m    = 'none';
        if(to_d    == ""  ) to_d    = 'none';
        if(partner == null) partner = 'none';
        if(status  == null) status  = 'none';
        if(country == null) country = 'none';

        var select = document.querySelector('#btn_search_workshop');
        var data   = select.dataset;
        var url    = data.url;

        url = url.replace("plc_page", 1);
        url = url.replace("plc_from_y", from_y);
        url = url.replace("plc_from_m", from_m);
        url = url.replace("plc_from_d", from_d);
        url = url.replace("plc_to_y", to_y);
        url = url.replace("plc_to_m", to_m);
        url = url.replace("plc_to_d", to_d);
        url = url.replace("plc_partner", partner);
        url = url.replace("plc_status", status);
        url = url.replace("plc_country", country);

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

        if(from_y  == ""  ) from_y  = 'none';
        if(from_m  == ""  ) from_m  = 'none';
        if(from_d  == ""  ) from_d  = 'none';
        if(to_y    == ""  ) to_y    = 'none';
        if(to_m    == ""  ) to_m    = 'none';
        if(to_d    == ""  ) to_d    = 'none';
        if(partner == null) partner = 'none';
        if(status  == null) status  = 'none';
        if(country == null) country = 'none';

        var select = document.querySelector('#doExcel_ticket');
        var data   = select.dataset;
        var url    = data.url;

        url = url.replace("plc_page", 1);
        url = url.replace("plc_from_y", from_y);
        url = url.replace("plc_from_m", from_m);
        url = url.replace("plc_from_d", from_d);
        url = url.replace("plc_to_y", to_y);
        url = url.replace("plc_to_m", to_m);
        url = url.replace("plc_to_d", to_d);
        url = url.replace("plc_partner", partner);
        url = url.replace("plc_status", status);
        url = url.replace("plc_country", country);


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

        if(from_y  == ""  ) from_y  = 'none';
        if(from_m  == ""  ) from_m  = 'none';
        if(from_d  == ""  ) from_d  = 'none';
        if(to_y    == ""  ) to_y    = 'none';
        if(to_m    == ""  ) to_m    = 'none';
        if(to_d    == ""  ) to_d    = 'none';
        if(partner == null) partner = 'none';
        if(status  == null) status  = 'none';
        if(country == null) country = 'none';

        var select = document.querySelector('#doExcel_workshop');
        var data   = select.dataset;
        var url    = data.url;

        url = url.replace("plc_page", 1);
        url = url.replace("plc_from_y", from_y);
        url = url.replace("plc_from_m", from_m);
        url = url.replace("plc_from_d", from_d);
        url = url.replace("plc_to_y", to_y);
        url = url.replace("plc_to_m", to_m);
        url = url.replace("plc_to_d", to_d);
        url = url.replace("plc_partner", partner);
        url = url.replace("plc_status", status);
        url = url.replace("plc_country", country);


        window.open(url, "_self");
    });
});