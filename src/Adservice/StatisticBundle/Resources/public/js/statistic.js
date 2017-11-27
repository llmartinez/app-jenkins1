
$(document).ready(function() {

    $("#flt_partner").change(function() {

        var id_partner = $('#flt_partner option:selected').val();

        if (id_partner != undefined) {

            populate_workshop(id_partner);

            var route  = 'shops_from_partner';
            var locale = $(document).find("#data_locale").val();

            $.ajax({
                type        : "POST",
                url         : Routing.generate(route, {_locale: locale }),
                data        : {id_partner : id_partner},
                dataType    : "json",
                beforeSend: function(){ $("body").css("cursor", "progress"); },
                complete: function(){ $("body").css("cursor", "default"); },
                success : function(data) {
                    // Limpiamos y llenamos el combo con las opciones del json
                    if (data['error'] != "No hay coincidencias") {
                        $('#flt_wks_shop').empty();
                        var lbl_all = $('#lbl_all').val();
                        $('#flt_wks_shop').append("<option value='0'>"+ lbl_all +"</option>");
                        $.each(data, function(idx, elm) {

                            $('#flt_wks_shop').append("<option value="+elm.id+">"+elm.shop+"</option>");
                        });
                    }
                },
                error : function(){
                    console.log("Error al cargar las tiendas...");
                }
            });
        }
    });

    //Rellena los campos socio, pais y estado del filtro de ticket o taller con la busqueda realizada
    var type     = $(document).find("#type").val();
    var from_y   = $(document).find("#type_from_y"  ).val();
    var from_m   = $(document).find("#type_from_m"  ).val();
    var from_d   = $(document).find("#type_from_d"  ).val();
    var to_y     = $(document).find("#type_to_y"    ).val();
    var to_m     = $(document).find("#type_to_m"    ).val();
    var to_d     = $(document).find("#type_to_d"    ).val();
    var partner  = $(document).find("#type_partner" ).val();
    var shop     = $(document).find("#type_shop"    ).val();
    var workshop = $(document).find("#type_workshop").val();
    var assessor = $(document).find("#type_assessor").val();
    var created_by = $(document).find("#type_created_by").val();
    var typology = $(document).find("#type_typology").val();
    var country  = $(document).find("#type_country" ).val();
    var status   = $(document).find("#type_status"  ).val();

    if(type == 'all') $(document).find("#type").val(0);

    if(type == 'ticket' || type == '') {
            $("#from_y" ).val(from_y );
            $("#from_m" ).val(from_m );
            $("#from_d" ).val(from_d );
            $("#to_y"   ).val(to_y   );
            $("#to_m"   ).val(to_m   );
            $("#to_d"   ).val(to_d   );
            $("#flt_partner").val(partner);
            $("#flt_country").val(country);
            $("#flt_tck_workshop").val(workshop);
            $("#flt_tck_assessor").val(assessor);
            $("#flt_tck_created_by").val(created_by);
            $("#flt_tck_status" ).val(status );
    }else{
        if(type == 'workshop') {
            $("#from_y" ).val(from_y );
            $("#from_m" ).val(from_m );
            $("#from_d" ).val(from_d );
            $("#to_y"   ).val(to_y   );
            $("#to_m"   ).val(to_m   );
            $("#to_d"   ).val(to_d   );
            $("#flt_partner").val(partner);
            $("#flt_country").val(country);
            $("#flt_wks_shop").val(shop);
            $("#flt_wks_typology").val(typology);
            $("#flt_wks_status" ).val(status );
        }
    }
    if(from_y  == 0 ) { $("#from_y" ).val(''); }
    if(from_m  == 0 ) { $("#from_m" ).val(''); }
    if(from_d  == 0 ) { $("#from_d" ).val(''); }
    if(to_y    == 0 ) { $("#to_y"   ).val(''); }
    if(to_m    == 0 ) { $("#to_m"   ).val(''); }
    if(to_d    == 0 ) { $("#to_d"   ).val(''); }

//REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
    $('#btn_search_ticket').click(function() {

        var from_y  = $('#from_y').val();
        var from_m  = $('#from_m').val();
        var from_d  = $('#from_d').val();
        var to_y    = $('#to_y').val();
        var to_m    = $('#to_m').val();
        var to_d    = $('#to_d').val();
        var partner = $('#flt_partner').val();
        var country = $('#flt_country').val();
        var catserv = $('#flt_catserv').val();
        var raport  = $('#flt_raport').val();
        var workshop= $("#flt_tck_workshop").val();
        var assessor= $("#flt_tck_assessor").val();
        var created_by= $("#flt_tck_created_by").val();
        var status  = $('#flt_tck_status').val();
        var shop    = '0';
        var typology= '0';

        if(from_y  == "" || from_y  == 0 ) from_y  = '0';
        if(from_m  == "" || from_m  == 0 ) from_m  = '0';
        if(from_d  == "" || from_d  == 0 ) from_d  = '0';
        if(to_y    == "" || to_y    == 0 ) to_y    = '0';
        if(to_m    == "" || to_m    == 0 ) to_m    = '0';
        if(to_d    == "" || to_d    == 0 ) to_d    = '0';

        var route  = 'doExcel';
        var locale = $(document).find("#data_locale").val();
        var url    = Routing.generate(route, {_locale: locale, type: 'ticket', page: 1, from_y: from_y, from_m: from_m, from_d: from_d, to_y: to_y, to_m: to_m, to_d: to_d, partner: partner, shop: shop, workshop: workshop, typology: typology, status: status, country: country, catserv: catserv, assessor: assessor, created_by: created_by, raport: raport });

        window.open(url, "_self");
    });

    //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
    $('#btn_search_workshop').click(function() {

        var from_y  = $('#from_y').val();
        var from_m  = $('#from_m').val();
        var from_d  = $('#from_d').val();
        var to_y    = $('#to_y').val();
        var to_m    = $('#to_m').val();
        var to_d    = $('#to_d').val();
        var partner = $('#flt_partner').val();
        var country = $('#flt_country').val();
        var catserv = $('#flt_catserv').val();
        var raport  = $('#flt_raport').val();
        var shop    = $("#flt_wks_shop").val();
        var typology= $("#flt_wks_typology").val();
        var status  = $('#flt_wks_status').val();
        var workshop= '0';
        var assessor= '0';
        var created_by= '0';

        if(from_y  == "" || from_y  == 0 ) from_y  = '0';
        if(from_m  == "" || from_m  == 0 ) from_m  = '0';
        if(from_d  == "" || from_d  == 0 ) from_d  = '0';
        if(to_y    == "" || to_y    == 0 ) to_y    = '0';
        if(to_m    == "" || to_m    == 0 ) to_m    = '0';
        if(to_d    == "" || to_d    == 0 ) to_d    = '0';

        var route  = 'doExcel';
        var locale = $(document).find("#data_locale").val();
        var url    = Routing.generate(route, {_locale: locale, type: 'workshop', page: 1, from_y: from_y, from_m: from_m, from_d: from_d, to_y: to_y, to_m: to_m, to_d: to_d, partner: partner, shop: shop, workshop: workshop, typology: typology, status: status, country: country, catserv: catserv, assessor: assessor, created_by: created_by, raport: raport });

        window.open(url, "_self");
    });
    
    $('#btn_search_partner').click(function() {
        var raport  = "partner";
        
         var from_y  = $('#from_y').val();
        var from_m  = $('#from_m').val();
        var from_d  = $('#from_d').val();
        var to_y    = $('#to_y').val();
        var to_m    = $('#to_m').val();
        var to_d    = $('#to_d').val();
        var partner = $('#flt_partner').val();
        var country = $('#flt_country').val();
        var catserv = $('#flt_catserv').val();
        var shop    = $("#flt_wks_shop").val();
        var typology= $("#flt_wks_typology").val();
        var status  = $('#flt_wks_status').val();
        var workshop= '0';
        var assessor= '0';
        var created_by= '0';

         if(from_y  == "" || from_y  == 0 ) from_y  = '0';
        if(from_m  == "" || from_m  == 0 ) from_m  = '0';
        if(from_d  == "" || from_d  == 0 ) from_d  = '0';
        if(to_y    == "" || to_y    == 0 ) to_y    = '0';
        if(to_m    == "" || to_m    == 0 ) to_m    = '0';
        if(to_d    == "" || to_d    == 0 ) to_d    = '0';
        
        
        var route  = 'doExcel';
        var locale = $(document).find("#data_locale").val();
        var url    = Routing.generate(route, {_locale: locale, type: 'undefined', page: 1, from_y: from_y, from_m: from_m, from_d: from_d, to_y: to_y, to_m: to_m, to_d: to_d, partner: partner, shop: shop, workshop: workshop, typology: typology, status: status, country: country, catserv: catserv, assessor: assessor, created_by: created_by, raport: raport });

        window.open(url, "_self");
    });
    //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
    $('#btn_raport_partner').click(function() {

        var raport  = "0";
        var code_zone  = $('#code_zone').val();

        var from_y  = $('#from_y').val();
        var from_m  = $('#from_m').val();
        var from_d  = $('#from_d').val();
        var to_y    = $('#to_y').val();
        var to_m    = $('#to_m').val();
        var to_d    = $('#to_d').val();
        var partner = $('#flt_partner').val();
        var catserv = $('#id_catserv').val();
        var country = $('#id_country').val();
        var shop    = $("#flt_wks_shop").val();
        var typology= $("#flt_wks_typology").val();
        var status  = $('#flt_wks_status').val();
        var workshop= '0';
        var assessor= '0';
        var created_by= '0';

        if(code_zone == "" || code_zone == 0 || code_zone == undefined ) code_zone = '0';
        if(partner   == "" || partner   == 0 || partner   == undefined ) partner   = '0';
        if(shop      == "" || shop      == 0 || shop      == undefined ) shop      = '0';
        if(typology  == "" || typology  == 0 || typology  == undefined ) typology  = '0';
        if(from_y    == "" || from_y    == 0 || from_y    == undefined ) from_y    = '0';
        if(from_m    == "" || from_m    == 0 || from_m    == undefined ) from_m    = '0';
        if(from_d    == "" || from_d    == 0 || from_d    == undefined ) from_d    = '0';
        if(to_y      == "" || to_y      == 0 || to_y      == undefined ) to_y      = '0';
        if(to_m      == "" || to_m      == 0 || to_m      == undefined ) to_m      = '0';
        if(to_d      == "" || to_d      == 0 || to_d      == undefined ) to_d      = '0';

        var route  = 'doExcel';
        var locale = $(document).find("#data_locale").val();
        var url    = Routing.generate(route, {_locale: locale, type: 'undefined', page: 1, from_y: from_y, from_m: from_m, from_d: from_d, to_y: to_y, to_m: to_m, to_d: to_d, partner: partner, shop: shop, workshop: workshop, typology: typology, status: status, country: country, catserv: catserv, assessor: assessor, created_by: created_by, raport: raport, code_zone: code_zone });

        window.open(url, "_self");
    });

    // //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
    // $('#btn_search_no_ticket').click(function() {

    //     var from_y  = '0';
    //     var from_m  = '0';
    //     var from_d  = '0';
    //     var to_y    = '0';
    //     var to_m    = '0';
    //     var to_d    = '0';
    //     var partner = '0';
    //     var shop    = '0';
    //     var typology= '0';
    //     var country = '0';
    //     var status  = '0';

    //     if(from_y  == "" || from_y  == 0 ) from_y  = '0';
    //     if(from_m  == "" || from_m  == 0 ) from_m  = '0';
    //     if(from_d  == "" || from_d  == 0 ) from_d  = '0';
    //     if(to_y    == "" || to_y    == 0 ) to_y    = '0';
    //     if(to_m    == "" || to_m    == 0 ) to_m    = '0';
    //     if(to_d    == "" || to_d    == 0 ) to_d    = '0';

    //     var route  = 'listStatistics';
    //     var locale = $(document).find("#data_locale").val();
    //     var url    = Routing.generate(route, {_locale: locale, type: 'no-ticket', page: 1, from_y: from_y, from_m: from_m, from_d: from_d, to_y: to_y, to_m: to_m, to_d: to_d, partner: partner, shop: shop, workshop: workshop, typology: typology, status: status, country: country, raport: raport });

    //     window.open(url, "_self");
    // });

    //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
    // $('#btn_search_last_ticket').click(function() {

    //     var from_y  = '0';
    //     var from_m  = '0';
    //     var from_d  = '0';
    //     var to_y    = '0';
    //     var to_m    = '0';
    //     var to_d    = '0';
    //     var partner = '0';
    //     var shop    = '0';
    //     var typology= '0';
    //     var country = '0';
    //     var status  = '0';

    //     if(from_y  == "" || from_y  == 0 ) from_y  = '0';
    //     if(from_m  == "" || from_m  == 0 ) from_m  = '0';
    //     if(from_d  == "" || from_d  == 0 ) from_d  = '0';
    //     if(to_y    == "" || to_y    == 0 ) to_y    = '0';
    //     if(to_m    == "" || to_m    == 0 ) to_m    = '0';
    //     if(to_d    == "" || to_d    == 0 ) to_d    = '0';

    //     var route  = 'listStatistics';
    //     var locale = $(document).find("#data_locale").val();
    //     var url    = Routing.generate(route, {_locale: locale, type: '0', page: 1, from_y: from_y, from_m: from_m, from_d: from_d, to_y: to_y, to_m: to_m, to_d: to_d, partner: partner, shop: shop, workshop: workshop, typology: typology, status: status, country: country, raport: raport });

    //     window.open(url, "_self");
    // });

    $('#doExcel_ticket').click(function() {

        var from_y  = $('#from_y').val();
        var from_m  = $('#from_m').val();
        var from_d  = $('#from_d').val();
        var to_y    = $('#to_y').val();
        var to_m    = $('#to_m').val();
        var to_d    = $('#to_d').val();
        var partner = $('#flt_partner').val();
        var country = $('#flt_country').val();
        var catserv = $('#flt_catserv').val();
        var raport  = $('#flt_raport').val();
        var assessor= $('#flt_tck_assessor').val();
        var created_by= $('#flt_tck_created_by').val();
        var status  = $('#flt_tck_status').val();

        if(from_y  == "" || from_y  == 0 ) from_y  = '0';
        if(from_m  == "" || from_m  == 0 ) from_m  = '0';
        if(from_d  == "" || from_d  == 0 ) from_d  = '0';
        if(to_y    == "" || to_y    == 0 ) to_y    = '0';
        if(to_m    == "" || to_m    == 0 ) to_m    = '0';
        if(to_d    == "" || to_d    == 0 ) to_d    = '0';

        var route  = 'doExcel';
        var locale = $(document).find("#data_locale").val();
        var url    = Routing.generate(route, {_locale: locale, type: 'ticket', page: 1, from_y: from_y, from_m: from_m, from_d: from_d, to_y: to_y, to_m: to_m, to_d: to_d, partner: partner, shop: shop, workshop: workshop, typology: typology, status: status, country: country, catserv: catserv, assessor: assessor, created_by: created_by, raport: raport });

        window.open(url, "_self");
    });

    $('#doExcel_workshop').click(function() {

        var from_y  = $('#from_y').val();
        var from_m  = $('#from_m').val();
        var from_d  = $('#from_d').val();
        var to_y    = $('#to_y').val();
        var to_m    = $('#to_m').val();
        var to_d    = $('#to_d').val();
        var partner = $('#flt_partner').val();
        var country = $('#flt_country').val();
        var catserv = $('#flt_catserv').val();
        var raport  = $('#flt_raport').val();
        var shop    = $("#flt_wks_shop").val();
        var typology= $("#flt_wks_typology").val();
        var status  = $('#flt_wks_status').val();

        if(from_y  == "" || from_y  == 0 ) from_y  = '0';
        if(from_m  == "" || from_m  == 0 ) from_m  = '0';
        if(from_d  == "" || from_d  == 0 ) from_d  = '0';
        if(to_y    == "" || to_y    == 0 ) to_y    = '0';
        if(to_m    == "" || to_m    == 0 ) to_m    = '0';
        if(to_d    == "" || to_d    == 0 ) to_d    = '0';

        var route  = 'doExcel';
        var locale = $(document).find("#data_locale").val();
        var url    = Routing.generate(route, {_locale: locale, type: 'workshop', page: 1, from_y: from_y, from_m: from_m, from_d: from_d, to_y: to_y, to_m: to_m, to_d: to_d, partner: partner, shop: shop, workshop: workshop, typology: typology, status: status, country: country, catserv: catserv, raport: raport });

        window.open(url, "_self");
    });

    // $('#doExcel_last_ticket').click(function() {

    //     var from_y  = '0';
    //     var from_m  = '0';
    //     var from_d  = '0';
    //     var to_y    = '0';
    //     var to_m    = '0';
    //     var to_d    = '0';
    //     var partner = '0';
    //     var shop    = '0';
    //     var typology= '0';
    //     var country = '0';
    //     var status  = '0';

    //     if(from_y  == "" || from_y  == 0 ) from_y  = '0';
    //     if(from_m  == "" || from_m  == 0 ) from_m  = '0';
    //     if(from_d  == "" || from_d  == 0 ) from_d  = '0';
    //     if(to_y    == "" || to_y    == 0 ) to_y    = '0';
    //     if(to_m    == "" || to_m    == 0 ) to_m    = '0';
    //     if(to_d    == "" || to_d    == 0 ) to_d    = '0';

    //     var route  = 'doExcel';
    //     var locale = $(document).find("#data_locale").val();
    //     var url    = Routing.generate(route, {_locale: locale, type: '0', page: 1, from_y: from_y, from_m: from_m, from_d: from_d, to_y: to_y, to_m: to_m, to_d: to_d, partner: partner, shop: shop, workshop: workshop, typology: typology, status: status, country: country, raport: raport });

    //     window.open(url, "_self");
    // });

    // $('#doExcel_no_ticket').click(function() {

    //     var from_y  = '0';
    //     var from_m  = '0';
    //     var from_d  = '0';
    //     var to_y    = '0';
    //     var to_m    = '0';
    //     var to_d    = '0';
    //     var partner = '0';
    //     var shop    = '0';
    //     var typology= '0';
    //     var country = '0';
    //     var status  = '0';

    //     if(from_y  == "" || from_y  == 0 ) from_y  = '0';
    //     if(from_m  == "" || from_m  == 0 ) from_m  = '0';
    //     if(from_d  == "" || from_d  == 0 ) from_d  = '0';
    //     if(to_y    == "" || to_y    == 0 ) to_y    = '0';
    //     if(to_m    == "" || to_m    == 0 ) to_m    = '0';
    //     if(to_d    == "" || to_d    == 0 ) to_d    = '0';

    //     var route  = 'doExcel';
    //     var locale = $(document).find("#data_locale").val();
    //     var url    = Routing.generate(route, {_locale: locale, type: 'no-ticket', page: 1, from_y: from_y, from_m: from_m, from_d: from_d, to_y: to_y, to_m: to_m, to_d: to_d, partner: partner, shop: shop, workshop: workshop, typology: typology, status: status, country: country, raport: raport });

    //     window.open(url, "_self");
    // });
});