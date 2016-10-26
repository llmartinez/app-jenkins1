
    $.ajaxSetup({ cache: false });
/**
 * Funcion que rellena (populate) el combo de las regiones segun el país seleccionado por el usuario
 * @param {url de tipo {{ path('mi_path') }}} route
 */
function populate_region(route, region, city){
    var id_country = $('form').find('select[name*=country]').val();
    var route = 'regions_from_country';
    var locale = $(document).find("#data_locale").val();

    $.ajax({
        type        : "POST",
        url         : Routing.generate(route, {_locale: locale}),
        data        : {id_country : id_country},
        dataType    : "json",
        beforeSend: function(){ $("body").css("cursor", "progress"); },
        complete: function(){ $("body").css("cursor", "default"); },
        success : function(data) {
            // Limpiamos y llenamos el combo con las opciones del json
            $('#data_regions').empty();
            $("#slct_region").empty();

            var region_edit = '';
            $.each(data, function(idx, elm) {

                if (idx != "error") {
                    if((region != undefined) && (string_to_slug(elm.region) == string_to_slug(region)))
                    {
                        region_edit = elm.region;
                        city_edit   = city;
                        $('#data_regions').append("<option value="+elm.id+" selected>"+elm.region+"</option>");
                    }
                    else{
                        if( region != 'no-region' ) {
                            region_edit = region;
                            city_edit   = city;
                            $('#data_regions').append("<option value="+elm.id+">"+elm.region+"</option>");
                        }
                        else $('#data_regions').append("<option value="+elm.id+">"+elm.region+"</option>");
                    }
                }
                else{
                    $( "#data_regions" ).empty();
                    $( "#data_regions" ).append("<tr><td>" + elm + "</td></tr>");
                }
            });
            $("#slct_region").html($('#data_regions').html());
        },
        error : function(){
            console.log("Error al cargar las regiones...");
        }
    });
}

/**
 * Funcion que rellena (populate) el combo de las ciudades segun la region seleccionada por el usuario
 */
function populate_city(){
    var route     = 'cities_from_region';
    var locale    = $(document).find("#data_locale").val();

    $.ajax({
        type        : "POST",
        url         : Routing.generate(route, {_locale: locale}),
        data        : {id_region : id_region},
        dataType    : "json",
        beforeSend: function(){ $("body").css("cursor", "progress"); },
        complete: function(){ $("body").css("cursor", "default"); },
        success : function(data) {
            // Limpiamos y llenamos el combo con las opciones del json
            $('#data_cities').empty();
            $("#slct_city"  ).empty();
            $.each(data, function(idx, elm)
            {
                if(elm.city == city) $('#data_cities').append("<option value="+elm.id+" selected>"+elm.city+"</option>");
                else                 $('#data_cities').append("<option value="+elm.id+">"+elm.city+"</option>");
            });
            $("#slct_city").html($('#data_cities').html());
            $('div#div_regions span.select2-chosen').text(id_region);
        },
        error : function(){
            console.log("Error al cargar las ciudades...");
        }
    });
}

/**
 * Funcion que rellena (populate) el combo de las socios segun la CatServ seleccionada por el usuario
 */
function populate_partner(){
    var id_catserv = $('form').find('select[name*=category_service]').val();
    if (id_catserv == undefined) { id_catserv = $('#id_catserv').val(); }

    var route  = 'partners_from_catserv';
    var locale = $(document).find("#data_locale").val();

    $('form').find('select[id$=partner]').empty();

    $.ajax({
        type        : "POST",
        url         : Routing.generate(route, {_locale: locale }),
        data        : {id_catserv : id_catserv},
        dataType    : "json",
        beforeSend: function(){ $("body").css("cursor", "progress"); },
        complete: function(){ $("body").css("cursor", "default"); },
        success : function(data) {
            // Limpiamos y llenamos el combo con las opciones del json
            if (data['error'] != "No hay coincidencias") {
                 $('form').find('select[id*=_partner]').append("<option value=0></option>");
                $.each(data, function(idx, elm) {

                    $('form').find('select[id$=_partner]').append("<option value="+elm.id+">"+elm.name+"</option>");
                });
            }
        },
        error : function(){
            console.log("Error al cargar los socios...");
        }
    });
}
/**
 * Funcion que rellena (populate) el combo de las socios segun la CatServ seleccionada por el usuario
 */
function populate_partner2(partner){
    var id_catserv = $('form').find('select[name*=category_service]').val();
    if (id_catserv == undefined) { id_catserv = $('#id_catserv').val(); }

    var route  = 'partners_from_catserv';
    var locale = $(document).find("#data_locale").val();

    $('form').find('select[id$=_partner]').empty();

    $.ajax({
        type        : "POST",
        url         : Routing.generate(route, {_locale: locale }),
        data        : {id_catserv : id_catserv},
        dataType    : "json",
        beforeSend: function(){ $("body").css("cursor", "progress"); },
        complete: function(){ $("body").css("cursor", "default"); },
        success : function(data) {
            // Limpiamos y llenamos el combo con las opciones del json
            if (data['error'] != "No hay coincidencias") {

                $('form').find('select[id*=_partner]').append("<option value=0></option>");
                $.each(data, function(idx, elm) {
                    $('form').find('select[id$=e_partner]').append("<option value="+elm.id+">"+elm.name+"</option>");
                });
                $('form').find('select[id$=e_partner]').val(partner);
                var typology = $('form').find('select[id$=typology]').val();
                if(typology != undefined) {
                    // DIAG. MACHINE
                        $('form').find('select[id$=typology]').empty();

                    populate_typology2(typology);
                }
            }
        },
        error : function(){
            console.log("Error al cargar los socios...");
        }
    });
}
function populate_partner3(){
    var id_catserv = $('select[name*=category_service]').val();
    if (id_catserv == undefined) { id_catserv = $('#id_catserv').val(); }

    var route  = 'partners_from_catserv';
    var locale = $(document).find("#data_locale").val();

    $('select[id$=_partner]').empty();

    $.ajax({
        type        : "POST",
        url         : Routing.generate(route, {_locale: locale }),
        data        : {id_catserv : id_catserv},
        dataType    : "json",
        beforeSend: function(){ $("body").css("cursor", "progress"); },
        complete: function(){ $("body").css("cursor", "default"); },
        success : function(data) {
            // Limpiamos y llenamos el combo con las opciones del json
            if (data['error'] != "No hay coincidencias") {

                var all = $('#lbl_all').val();
                $('select[id*=_partner]').append("<option value=0>"+all+"</option>");

                $.each(data, function(idx, elm) {
                    $('select[id$=_partner]').append("<option value="+elm.id+">"+elm.name+"</option>");
                });
                var typology = $('select[id$=typology]').val();
                if(typology != undefined) {
                    // DIAG. MACHINE
                        $('select[id$=typology]').empty();

                    populate_typology3(typology);
                }
            }
        },
        error : function(){
            console.log("Error al cargar los socios...");
        }
    });
}

/**
 * Funcion que rellena (populate) el combo de las socios segun la CatServ seleccionada por el usuario
 */
function populate_typology(){
    var id_catserv = $('form').find('select[name*=category_service]').val();
    if (id_catserv == undefined) { id_catserv = $('#id_catserv').val(); }

    var route  = 'typologies_from_catserv';
    var locale = $(document).find("#data_locale").val();

    $('form').find('select[id$=typology]').empty();

    $.ajax({
        type        : "POST",
        url         : Routing.generate(route, {_locale: locale }),
        data        : {id_catserv : id_catserv},
        dataType    : "json",
        beforeSend: function(){ $("body").css("cursor", "progress"); },
        complete: function(){ $("body").css("cursor", "default"); },
        success : function(data) {
            // Limpiamos y llenamos el combo con las opciones del json
            if (data['error'] != "No hay coincidencias") {
                $('form').find('select[id$=typology]').append("<option></option>");
                $.each(data, function(idx, elm) {

                    $('form').find('select[id$=typology]').append("<option value="+elm.id+">"+elm.name+"</option>");
                });
            }
        },
        error : function(){
            console.log("Error al cargar las tipologias...");
        }
    });
}

/**
 * Funcion que rellena (populate) el combo de las socios segun la CatServ seleccionada por el usuario
 */
function populate_typology2(typology){
    var id_catserv = $('form').find('select[name*=category_service]').val();
    if (id_catserv == undefined) { id_catserv = $('#id_catserv').val(); }

    var route  = 'typologies_from_catserv';
    var locale = $(document).find("#data_locale").val();

    $('#slct_typology').empty();

    $.ajax({
        type        : "POST",
        url         : Routing.generate(route, {_locale: locale }),
        data        : {id_catserv : id_catserv},
        dataType    : "json",
        beforeSend: function(){ $("body").css("cursor", "progress"); },
        complete: function(){ $("body").css("cursor", "default"); },
        success : function(data) {
            // Limpiamos y llenamos el combo con las opciones del json
            if (data['error'] != "No hay coincidencias") {
                $('form').find('select[id$=typology]').append("<option></option>");
                $.each(data, function(idx, elm) {

                    $('form').find('select[id$=typology]').append("<option value="+elm.id+">"+elm.name+"</option>");
                });

                 $('form').find('select[id$=typology]').val(typology);
                var diag_machine =  $('form').find('select[id$=diagnosis_machines]').val();

                // TIPOLOGY
                    $('form').find('select[id$=diagnosis_machines]').empty();

                populate_diagmachine2(diag_machine);

            }
        },
        error : function(){
            console.log("Error al cargar las tipologias...");
        }
    });
}

/**
 * Funcion que rellena (populate) el combo de las socios segun la CatServ seleccionada por el usuario
 */
function populate_typology3(typology){
    var id_catserv = $('form').find('select[name*=category_service]').val();
    if (id_catserv == undefined) { id_catserv = $('#id_catserv').val(); }

    var route  = 'typologies_from_catserv';
    var locale = $(document).find("#data_locale").val();

    $('#slct_typology').empty();

    $.ajax({
        type        : "POST",
        url         : Routing.generate(route, {_locale: locale }),
        data        : {id_catserv : id_catserv},
        dataType    : "json",
        beforeSend: function(){ $("body").css("cursor", "progress"); },
        complete: function(){ $("body").css("cursor", "default"); },
        success : function(data) {
            // Limpiamos y llenamos el combo con las opciones del json
            if (data['error'] != "No hay coincidencias") {

                var all = $('#lbl_all').val();
                $('select[id$=typology]').append("<option value='0'>"+all+"</option>");

                $.each(data, function(idx, elm) {
                    $('select[id$=typology]').append("<option value="+elm.id+">"+elm.name+"</option>");
                });
                $('select[id$=typology]').val(typology);

            }
        },
        error : function(){
            console.log("Error al cargar las tipologias...");
        }
    });
}
/**
 * Funcion que rellena (populate) el combo de las socios segun la CatServ seleccionada por el usuario
 */
function populate_diagmachine(){
    var id_catserv = $('form').find('select[name*=category_service]').val();
    if (id_catserv == undefined) { id_catserv = $('#id_catserv').val(); }

    var route  = 'diag_machines_from_catserv';
    var locale = $(document).find("#data_locale").val();

    $('form').find('select[id$=diagnosis_machines]').empty();

    $.ajax({
        type        : "POST",
        url         : Routing.generate(route, {_locale: locale }),
        data        : {id_catserv : id_catserv},
        dataType    : "json",
        beforeSend: function(){ $("body").css("cursor", "progress"); },
        complete: function(){ $("body").css("cursor", "default"); },
        success : function(data) {
            // Limpiamos y llenamos el combo con las opciones del json
            if (data['error'] != "No hay coincidencias") {
                // $('form').find('select[id*=diagnosis_machines]').append("<option value></option>");
                $.each(data, function(idx, elm) {

                    $('form').find('select[id$=diagnosis_machines]').append("<option value="+elm.id+">"+elm.name+"</option>");
                });
            }
        },
        error : function(){
            console.log("Error al cargar las maquinas de diagnosis...");
        }
    });
}

/**
 * Funcion que rellena (populate) el combo de las socios segun la CatServ seleccionada por el usuario
 */
function populate_diagmachine2(diag_machine){
    var id_catserv = $('form').find('select[name*=category_service]').val();
    if (id_catserv == undefined) { id_catserv = $('#id_catserv').val(); }

    var route  = 'diag_machines_from_catserv';
    var locale = $(document).find("#data_locale").val();

    $('form').find('select[id$=diagnosis_machines]').empty();

    $.ajax({
        type        : "POST",
        url         : Routing.generate(route, {_locale: locale }),
        data        : {id_catserv : id_catserv},
        dataType    : "json",
        beforeSend: function(){ $("body").css("cursor", "progress"); },
        complete: function(){ $("body").css("cursor", "default"); },
        success : function(data) {
            // Limpiamos y llenamos el combo con las opciones del json
            if (data['error'] != "No hay coincidencias") {
                // $('form').find('select[id*=diagnosis_machines]').append("<option value></option>");
                $.each(data, function(idx, elm) {

                    $('form').find('select[id$=diagnosis_machines]').append("<option value="+elm.id+">"+elm.name+"</option>");
                });


                $('form').find('select[id$=diagnosis_machines]').val(diag_machine);
            }
        },
        error : function(){
            console.log("Error al cargar las maquinas de diagnosis...");
        }
    });


}

/**
 * Funcion que rellena (populate) el combo de las tiendas segun el socio seleccionado por el usuario
 */
function populate_shop(id_shop){
    var id_partner = $('form').find('select[name*=partner]').val();
    if (id_partner == undefined) { id_partner = $('#id_partner').val(); }
    if(id_shop == undefined){ id_shop = ''; }

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
                $('form').find('select[id*=_shop]').empty();
                // $('form').find('select[id*=_shop]').append("<option value=0></option>");
                $.each(data, function(idx, elm) {

                    if(elm.id == id_shop) $('form').find('select[id*=_shop]').append("<option value="+elm.id+" selected>"+elm.shop+"</option>");
                    else                  $('form').find('select[id*=_shop]').append("<option value="+elm.id+">"+elm.shop+"</option>");
                });
            }
        },
        error : function(){
            console.log("Error al cargar las tiendas...");
        }
    });
}
/**
 * Funcion que rellena (populate) el combo de los talleres segun el socio seleccionado por el usuario
 */
function populate_workshop(id_partner){

    var route  = 'workshops_from_partner';
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
                $('form').find('select[id*=_workshop]').empty();

                var all = $('#lbl_all').val();
                $('form').find('select[id*=_workshop]').append("<option value=0>"+all+"</option>");
                $.each(data, function(idx, elm) {
                    $('form').find('select[id*=_workshop]').append("<option value="+elm.id+">"+elm.code_partner+"-"+elm.code_workshop+": "+elm.name+"</option>");
                });
            }
        },
        error : function(){
            console.log("Error al cargar las tiendas...");
        }
    });
}

/**
 * Funcion que rellena (populate) el combo de las tiendas segun el socio seleccionado por el usuario
 */
function fill_code_partner(id_partner){
    var route  = 'get_code_partner';
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
                $('#code_partner').empty();
                $.each(data, function(idx, elm) {

                    $('#code_partner').val(elm.code_partner);
                });
            }
        },
        error : function(){
            console.log("Error al cargar el codigo de socio...");
        }
    });
}

/**
 * Funcion que rellena (populate) el combo de las tiendas segun el socio seleccionado por el usuario
 */
function fill_code_workshop(id_partner){
    var route  = 'get_code_workshop';
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
                $('form').find('input[name*=code_workshop]').empty();

                $('form').find('input[name*=code_workshop]').val(data['code']);

            }
        },
        error : function(){
            console.log("Error al cargar el codigo de socio...");
        }
    });
}

/**
 * Rellena el combo de los modelos segun la marca seleccionada por el usuario
 */
function fill_model(model) {
    $('#new_car_form_version').empty();
    $('#car').text($('select[id=new_car_form_brand] option:selected').text());

    var id_brand = $('form[id=contact]').find('select[id=new_car_form_brand]').val();

    if (id_brand != undefined && id_brand != "") {

        var route  = 'car_model';
        var locale = $(document).find("#data_locale").val();
        var filter = '';
        var filter_value = '';
        // var id_mts = '';
        // var motor = '';

        if ($('#year_selected').val()  == '') { filter       = 'year';
                                                filter_value = $('#new_car_form_year').val(); }

        if ($('#motor_selected').val() == '') { filter       = 'motor';
                                                filter_value = $('#new_car_form_motor').val();
                                                // id_mts       = $('#id_mts').val();
                                                // motor        = $('form[id=contact]').find('#new_car_form_motor').val();
                                              }

        $.ajax({
            type: "POST",
            url: Routing.generate(route, {_locale: locale, id_brand: id_brand, filter: filter, filter_value: filter_value}),
            data: {id_brand: id_brand, filter: filter, filter_value: filter_value}, //, id_mts: id_mts, motor: motor},
            dataType: "json",
            beforeSend: function(){ $("body").css("cursor", "progress"); },
            complete: function(){ $("body").css("cursor", "default"); },
            success: function(data) {
                // Limpiamos y llenamos el combo con las opciones del json
                $('#new_car_form_model').empty();
                if($('#new_car_form_plateNumber').val() != ""){
                    $('#new_car_form_model').empty();
                    $('#new_car_form_version').empty();
                    $('#new_car_form_version').append("<option></option>");
                }
                if (data['error'] != "No hay coincidencias") {
                    //Primer campo vacío
                    $('form[id=contact]').find('select[id=new_car_form_model]').append("<option></option>");
                    $.each(data, function(idx, elm) {
                        // if (idx == 'id_mts') $('#id_mts').val(elm);
                        // else {
                            if(model == elm.id )
                                $('form[id=contact]').find('select[id=new_car_form_model]').append("<option value=" + elm.id + " selected>" + elm.name + "</option>");
                            else
                                $('form[id=contact]').find('select[id=new_car_form_model]').append("<option value=" + elm.id + ">" + elm.name + "</option>");
                        // }
                    });
                }
                if(id_brand == 0){
                    $('#new_car_form_model').empty();
                    $('form[id=contact]').find('select[id=new_car_form_model]').append("<option value=0 selected>OTHER</option>");
                    $('#new_car_form_version').empty();
                    $('form[id=contact]').find('select[id=new_car_form_version]').append("<option value=0>OTHER</option>");

                   // var version = $("#ticket_version").val();
                   // if(version != undefined && version != ""){
                   //     fill_version(version);
                   // }
                }
                else if(model != undefined && model != '' ) {
                    var version = $("#ticket_version").val();
                    if(version != undefined && version != ""){
                        fill_version(version);
                    }
                }
            },
            error: function() {
                console.log("Error al cargar modelos...");
            }
        });
    }
}

/**
 * Rellena (fill) el combo de las versiones (version) segun el modelo (model) seleccionado por el usuario
 */
function fill_version(version) {

        $('#car').text($('select[id=new_car_form_brand] option:selected').text()+ ' '+$('select[id=new_car_form_model] option:selected').text());
        var id_model = $('form[id=contact]').find('select[id=new_car_form_model]').val();

        var motor = $('form[id=contact]').find('input[id=flt_motor]').val();
        if (motor == undefined) motor = $('form[id=contact]').find('input[id=new_car_form_motor]').val();

        if (id_model != undefined && id_model != "") {
            var route  = 'car_version';
            var locale = $(document).find("#data_locale").val();
            var filter = '';
            var filter_value = '';
            // var id_mts = '';

            if ($('#year_selected').val()  == '') { filter       = 'year';
                                                    filter_value = $('#new_car_form_year').val(); }

            if ($('#motor_selected').val() == '') { filter       = 'motor';
                                                    filter_value = $('#new_car_form_motor').val();
                                                    // id_mts       = $('#id_mts').val();
                                                  }

            $.ajax({
                type: "POST",
                url: Routing.generate(route, {_locale: locale, id_model: id_model, filter: filter, filter_value: filter_value}),
                data: {id_model: id_model, filter: filter, filter_value: filter_value}, //, id_mts: id_mts},
                dataType: "json",
                beforeSend: function(){ $("body").css("cursor", "progress"); },
                complete: function(){ $("body").css("cursor", "default"); },
                success: function(data) {
                    // Limpiamos y llenamos el combo con las opciones del json
                    $('#new_car_form_version').empty();
                    $('#id_system').val("");
                    $('#ticket_form_subsystem').empty();

                    var flt_year = $('#flt_year').val();
                    if(flt_year == undefined || flt_year == '' || flt_year == '0') $('#new_car_form_year').val('');

                    var flt_motor = $('#flt_motor').val();
                    if(flt_motor == undefined || flt_motor == '' || flt_motor == '0') $('#new_car_form_motor').val('');

                    $('#new_car_form_kW').val('');
                    $('#new_car_form_displacement').val('');

                    if (data['error'] != "No hay coincidencias") {

                        var dis_url = $( "#dis-url" ).val();
                        var vts_url = $( "#vts-url" ).val();

                        //Primer campo vacío
                        $('form[id=contact]').find('select[id=new_car_form_version]').append("<option></option>");

                        $.each(data, function(idx, elm) {
                            // if (idx == 'id_mts') $('#id_mts').val(elm);
                            // else {
                                if(version == elm.id ){
                                    var mt = elm.name.substring(elm.name.indexOf("[")+1, elm.name.indexOf("]"));

                                    if(motor == undefined || (motor == mt))
                                        $('form[id=contact]').find('select[id=new_car_form_version]').append("<option value=" + elm.id + " selected>" + elm.name + "</option>");
                                }
                                else
                                    $('form[id=contact]').find('select[id=new_car_form_version]').append("<option value=" + elm.id + ">" + elm.name + "</option>");

                                $( "#dis" ).attr("href", dis_url+'/model-'+elm.model);
                                $( "#vts" ).attr("href", vts_url+'/'+elm.brand+'/'+elm.model);
                            // }
                        });
                    }
                    if(version != undefined && version != '' ) {
                        fill_car_data();
                        var system = $('#id_system').val();
                        if(system == '' ) { system = $("#ticket_system").val(); }

                        if(system != '' ) {
                            $('#id_system').val(system);
                            var subsystem = $("#ticket_subsystem").val();
                            if (subsystem != undefined)
                               fill_subsystem(subsystem);
                        }
                    }

                },
                error: function() {
                    console.log("Error al cargar versiones...");
                }
            });
        }

}

/**
 * Rellena (fill) el combo de las versiones (version) segun el modelo (model) seleccionado por el usuario
 */
function fill_car_data() {

    $('#car').text($('select[id=new_car_form_brand] option:selected').text()+ ' '+$('select[id=new_car_form_model] option:selected').text()+ ' '+$('select[id=new_car_form_version] option:selected').text());
    var id_version = $('form[id=contact]').find('select[id=new_car_form_version]').val();
    var version_motor = $('form[id=contact]').find('select[id=new_car_form_version] option:selected').text().split('[')[1].slice(0, -1);

    var motor = $('form[id=contact]').find('input[id=flt_motor]').val();
    if (motor = undefined) motor = $('form[id=contact]').find('input[id=new_car_form_motor]').val();

    var year = $('form[id=contact]').find('input[id=flt_year]').val();
    if (year = undefined) year = $('form[id=contact]').find('input[id=new_car_form_year]').val();

    if (id_version != undefined && id_version != "") {
        var route  = 'car_data';
        var locale = $(document).find("#data_locale").val();

        $.ajax({
            type: "POST",
            url: Routing.generate(route, {_locale: locale, id_version: id_version, version_motor: version_motor, motor: motor}),
            data: {id_version: id_version, version_motor: version_motor, motor: motor, year: year},
            dataType: "json",
            beforeSend: function(){ $("body").css("cursor", "progress"); },
            complete: function(){ $("body").css("cursor", "default"); },
            success: function(data) {
                if (data['error'] != "No hay coincidencias") {
                    // Limpiamos y llenamos los campos del coche
                    $.each(data, function(idx, elm) {

                        var inicio = elm.inicio.slice(0,4);
                        var fin    = elm.fin.slice(0,4);
                        var fecha  = inicio+' - '+fin;

                        var year_setted = $('form[id=contact]').find('#new_car_form_year').attr('value');

                        if(year_setted != undefined && year_setted != ''){
                            $('form[id=contact]').find('#new_car_form_year'    ).val(year_setted);
                            $('form[id=contact]').find('#new_car_form_year').attr('value', '');
                        }else{
                            if(year == undefined) {
                                $('form[id=contact]').find('#new_car_form_year'    ).val(fecha);
                            }
                        }
                        $('form[id=contact]').find('#new_car_form_motor'       ).val(elm.motor  );
                        $('form[id=contact]').find('#new_car_form_kW'          ).val(elm.kw     );
                        $('form[id=contact]').find('#new_car_form_displacement').val(elm.cm3    );
                        var dis_url = $( "#dis-url" ).val();
                        var vts_url = $( "#vts-url" ).val();

                        $( "#dis" ).attr("href", dis_url+'/'+elm.id);
                        $( "#vts" ).attr("href", vts_url+'/'+elm.brand+'/'+elm.model+'/'+elm.id);
                    });
                }
            },
            error: function() {
                console.log("Error al cargar datos de coche...");
            }
        });
    }
}

/**
 * Rellena (fill) los combos segun el año
 */
function fill_car_by_year() {

    var year = $('form[id=contact]').find('#new_car_form_year').val();

    var route  = 'car_by_year';
    var locale = $(document).find("#data_locale").val();

    $.ajax({
        type: "POST",
        url: Routing.generate(route, {_locale: locale, year: year}),
        data: {year: year},
        dataType: "json",
        beforeSend: function(){ $("body").css("cursor", "progress"); },
        complete: function(){ $("body").css("cursor", "default"); },
        success: function(data) {
            if (data['error'] != "No hay coincidencias") {
                if (data['error'] == "msg_bad_filter") {
                    msg_bad_filter = $('#msg_bad_filter').val();
                    alert(msg_bad_filter);
                }else{
                    // Vaciamos marca, modelo y gama y recargamos las marcas filtradas
                    $('#new_car_form_brand').empty();
                    $('#new_car_form_model').empty();
                    $('#new_car_form_version').empty();

                    //Primer campo vacío
                    $('form[id=contact]').find('select[id=new_car_form_brand]').append("<option></option>");

                    $.each(data, function(idx, elm) {
                        $('form[id=contact]').find('select[id=new_car_form_brand]').append("<option value=" + elm.id + ">" + elm.name + "</option>");
                    });
                    //Cambiamos el icono para indicar que se esta filtrando por motor
                    $('#filter_motor').empty();
                    $('#filter_motor').append('<a id="flt_motor" value="'+year+'"><img class="img_icon" src='+$('#funnel').val()+'></a>');

                    $('#filter_year').empty();
                    if(year != ''){
                        $('#filter_year').append('<input type="hidden" id="flt_year" name="flt_year" value="'+year+'"><img class="img_icon" id="year_selected" src='+$('#funnel_filtered').val()+'></a>');
                    }else{
                        $('#filter_year').append('<img class="img_icon" id="year_selected" src='+$('#funnel').val()+'></a>');
                    }
                }
            }
        },
        error: function() {
            console.log("Error al filtrar por año...");
        }
    });
}

/**
 * Rellena (fill) los combos segun el motor
 */
function fill_car_by_motor() {

    var motor = $('form[id=contact]').find('#new_car_form_motor').val();

    var route  = 'car_by_motor';
    var locale = $(document).find("#data_locale").val();

    $('form[id=contact]').find('select[id=new_car_form_brand]').val(0);
    $('form[id=contact]').find('select[id=new_car_form_model]').val(0);
    $('form[id=contact]').find('select[id=new_car_form_version]').val(0);

    $.ajax({
        type: "POST",
        url: Routing.generate(route, {_locale: locale, motor: motor}),
        data: {motor: motor},
        dataType: "json",
        beforeSend: function(){ $("body").css("cursor", "progress"); },
        complete: function(){ $("body").css("cursor", "default"); },
        success: function(data) {
            if (data['error'] != "No hay coincidencias") {
                if (data['error'] == "msg_bad_filter") {
                    msg_bad_filter = $('#msg_bad_filter').val();
                    alert(msg_bad_filter);
                }else{
                    // Vaciamos marca, modelo y gama y recargamos las marcas filtradas
                    $('#new_car_form_brand').empty();
                    $('#new_car_form_model').empty();
                    $('#new_car_form_version').empty();

                    //Primer campo vacío
                    $('form[id=contact]').find('select[id=new_car_form_brand]').append("<option></option>");

                    $.each(data, function(idx, elm) {
                        if (idx == 'id_mts') $('#id_mts').val(elm);
                        else
                            $('form[id=contact]').find('select[id=new_car_form_brand]').append("<option value=" + elm.id + ">" + elm.name + "</option>");
                    });
                    //Cambiamos el icono para indicar que se esta filtrando por motor
                    $('#filter_year').empty();
                    $('#filter_year').append('<img class="img_icon" src='+$('#funnel').val()+'></a>');

                    $('#filter_motor').empty();
                    if(motor != ''){
                        $('#filter_motor').append('<input type="hidden" id="flt_motor" name="flt_motor" value="'+motor+'"><img class="img_icon" id="motor_selected" src='+$('#funnel_filtered').val()+'></a>');
                    }else{
                        $('#filter_motor').append('<img class="img_icon" id="motor_selected" src='+$('#funnel').val()+'></a>');
                    }
                }
            }else{
                msg_bad_filter = $('#msg_bad_filter').val();
                alert(msg_bad_filter);
            }
        },
        error: function() {
            console.log("Error al filtrar por motor...");
        }
    });
}

/**
 * Rellena (fill) el combo de los subsistemas (subsystem) segun el sistema (system) seleccionado por el usuario
 */
function fill_subsystem(subsystem) {

    var id_system = $('form[id=contact]').find('select[id=id_system]').val();

    if(id_system == '0') $("#ticket_system").val();

    var route  = 'ticket_system';
    var locale = $(document).find("#data_locale").val();

    //Valor del subsistema del ticket al cerrar
    var id_subsystem = ($('select[id*=_subsystem]').val());
    if (id_subsystem == null) $('select[id*=_subsystem]').empty();

    $.ajax({
        type: "POST",
        url: Routing.generate(route, {_locale: locale }),
        data: {id_system: id_system},
        dataType: "json",
        beforeSend: function(){ $("body").css("cursor", "progress"); },
        complete: function(){ $("body").css("cursor", "default"); },
        success: function(data) {
            if (data['error'] != "No hay coincidencias") {

                // Limpiamos y llenamos el combo con las opciones del json
                $('select[id*=_subsystem]').empty();

                //Primer campo vacío
                $('form[id=contact]').find('select[id*=_subsystem]').append("<option></option>");

                $.each(data, function(idx, elm) {
                    if (elm.id == id_subsystem || elm.id == subsystem)
                        $('form[id=contact]').find('select[id*=_subsystem]').append("<option value=" + elm.id + " selected>" + elm.name + "</option>");
                    else
                        $('form[id=contact]').find('select[id*=_subsystem]').append("<option value=" + elm.id + ">" + elm.name + "</option>");
                });

                var subsys_id = $('#subsystem_id').val();
                if(subsys_id != undefined && subsys_id != '0' ) $('#edit_ticket_form_subsystem').val(subsys_id);
            }else{
                $('select[id*=_subsystem]').empty();
            }
        },
        error: function() {
            console.log("Error al cargar subsistemas...");
        }
    });
}

/**
 * Rellena (fill) una tabla con tickets similares
 */
function fill_tbl_similar() {

    var route  = 'tbl_similar';
    var locale = $(document).find("#data_locale").val();
    var id_model     = $('form').find('select[id*=model]').val();
    var id_subsystem = $('form').find('select[id*=subsystem]').val();
    var id_country   = $('#id_country').val();

    $.ajax({
        type: "POST",
        url: Routing.generate(route, {_locale: locale }),
        data: { id_model: id_model, id_subsystem: id_subsystem, id_country: id_country },
        dataType: "json",
        beforeSend: function(){ $("body").css("cursor", "progress"); },
        complete: function(){ $("body").css("cursor", "default"); },
        success: function(data) {

            // Limpiamos y llenamos el combo con las opciones del json
            $( "#tbl_similar" ).empty();
            $( "#tbl_similar" ).append("<tr><th class='padded'> CAR </th><th class='padded'> DESCRIPTION </th></tr>");

            $.each(data, function(idx, elm) {
                if (idx != "error") {
                    var url = Routing.generate('showTicketReadonly', {_locale: locale, id: elm.id });
                    $("#tbl_similar").append("<tr><td class='padded'>" + elm.car + "</td><td class='padded'><a onclick='window.open( \""+ url +"\" , \"width=1000, height=800, top=100px, left=100px, toolbar=no, status=no, location=no, directories=no, menubar=no\" )' > " + elm.description + "</a></td></tr>");
                }
                else{
                    $( "#tbl_similar" ).empty();
                    $( "#tbl_similar" ).append("<tr><td>" + elm + "</td></tr>");
                }
            });
        },
        error: function() {
            console.log("Error al cargar tickets similares..");
        }
    });
}

/**
 * Rellena (fill) una tabla con tickets repetidos
 */
function fill_tbl_repeated() {

    // var route  = 'tbl_repeated';
    // var locale = $(document).find("#data_locale").val();
    // var id_model     = $('form').find('select[id*=model]').val();
    // var id_subsystem = $('form').find('select[id*=subsystem]').val();

    // $.ajax({
    //     type: "POST",
    //     url: Routing.generate(route, {_locale: locale }),
    //     data: { id_model: id_model, id_subsystem: id_subsystem },
    //     dataType: "json",
    //     beforeSend: function(){ $("body").css("cursor", "progress"); },
    //     complete: function(){ $("body").css("cursor", "default"); },
    //     success: function(data) {

    //         // Limpiamos y llenamos el combo con las opciones del json
    //         $( "#tbl_repeated" ).empty();
    //         $( "#tbl_repeated" ).append("<tr><th class='padded'> CAR </th><th class='padded'> DESCRIPTION </th></tr>");
    //         //Primer campo vacío
    //         $.each(data, function(idx, elm) {

    //             if (idx != "error") {
    //                 var url = Routing.generate('showTicketReadonly', {_locale: locale, id: elm.id });
    //                 $("#tbl_repeated").append("<tr><td class='padded'>" + elm.car + "</td><td class='padded'><a onclick='window.open( \""+ url +"\" , \"width=1000, height=800, top=100px, left=100px, toolbar=no, status=no, location=no, directories=no, menubar=no\" )' > " + elm.description + "</a></td></tr>");
    //             }
    //             else{
    //                 $( "#tbl_repeated" ).empty();
    //                 $( "#tbl_repeated" ).append("<tr><td>" + elm + "</td></tr>");
    //             }
    //         });
    //     },
    //     error: function() {
    //         console.log("Error al cargar tickets repetidos..");
    //     }
    // });
}

function string_to_slug(str) {
    str = str.replace(/^\s+|\s+$/g, ''); // trim
    str = str.toLowerCase();

    // remove accents, swap ñ for n, etc
    var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
    var to   = "aaaaeeeeiiiioooouuuunc------";
    for (var i=0, l=from.length ; i<l ; i++) {
        str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
    }

    str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
    .replace(/\s+/g, '-') // collapse whitespace and replace by -
    .replace(/-+/g, '-'); // collapse dashes

    return str;
}

/**
 * Funcion que rellena (populate) el combo de las ciudades segun la region seleccionada por el usuario
 */
function get_id_from_code_partner(code){
    var route     = 'get_id_from_code_partner';
    var locale    = $(document).find("#data_locale").val();

    $.ajax({
        type        : "POST",
        url         : Routing.generate(route, {_locale: locale}),
        data        : {code : code},
        dataType    : "json",
        beforeSend: function(){ $("body").css("cursor", "progress"); },
        complete: function(){ $("body").css("cursor", "default"); },
        success : function(data) {

            if(data.id != 0) {
                $('form').find('select[name*=partner]').val(data.id);
                $('form').find('input[name*=code_workshop]').val(data.code);
            }
            else{
                alert($('#partner_not_found').val());
            }
            populate_shop();

            get_country_partner(data.id);
        },
        error : function(){
            console.log("Error al cargar id desde código...");
        }
    });
}

/**
 * Funcion que rellena (populate) el combo de las ciudades segun la region seleccionada por el usuario
 */
function get_country_partner(id_partner){
    var route     = 'get_country_partner';
    var locale    = $(document).find("#data_locale").val();

    $.ajax({
        type        : "POST",
        url         : Routing.generate(route, {_locale: locale, id_partner: id_partner}),
        data        : {id_partner : id_partner},
        dataType    : "json",
        beforeSend: function(){ $("body").css("cursor", "progress"); },
        complete: function(){ $("body").css("cursor", "default"); },
        success : function(data) {

            if(data.id != 0) {
                $('form').find('select[name*=country]').empty();
                $('form').find('select[name*=country]').append("<option value="+data.id+" selected>"+data.name+"</option>");
            }
            else{
                alert($('#bad_introduction').val());
            }
            populate_shop();
        },
        error : function(){
            console.log("Error al cargar id desde código...");
        }
    });
}

$("#filter_plate_number").on('click', function ()
{
    var route     = 'get_car_from_plate_number';

    var idPlateNumber = $(document).find('#new_car_form_plateNumber').val();
    var locale    = $(document).find("#data_locale").val();

    $.ajax({
        type: "POST",
        url: Routing.generate(route, {_locale: locale, idPlateNumber: idPlateNumber}),
        dataType: "json",
        beforeSend: function () {
            $("body").css("cursor", "progress");
        },
        complete: function () {
            $("body").css("cursor", "default");
        },
        success: function (data) {
           if (data['error'] !== "No hay coincidencias") {
               var versionId = data.versionId;
               fill_model_by_PlateNumber(data);

           }
           else {
               alert($("#msg_plate_number_not_found").val());
           }

        },
        error: function () {
            console.log("Error loading models...");
        }
    });
});

/**
 * Rellena el combo de los modelos segun la matricula
 */
function fill_model_by_PlateNumber(dataPN) {

    $('#car').text($('select[id=new_car_form_brand] option:selected').text());
    $('select#new_car_form_brand' ).val(dataPN.brandId);
    $("#ticket_model").val(dataPN.modelId);
    $("#ticket_version").val(dataPN.versionId);

    var id_brand = $('form[id=contact]').find('select[id=new_car_form_brand]').val();
    var model = dataPN.modelId;
    var motor = dataPN.motor;
    if (id_brand != undefined && id_brand != "") {

        var route  = 'car_model';
        var locale = $(document).find("#data_locale").val();
        var filter = '';
        var filter_value = '';
        // var id_mts = '';
        // var motor = '';

        if ($('#year_selected').val()  == '') { filter       = 'year';
                                                filter_value = $('#new_car_form_year').val(); }

        if ($('#motor_selected').val() == '') { filter       = 'motor';
                                                filter_value = $('#new_car_form_motor').val();
                                                // id_mts       = $('#id_mts').val();
                                                // motor        = $('form[id=contact]').find('#new_car_form_motor').val();
                                              }

        $.ajax({
            type: "POST",
            url: Routing.generate(route, {_locale: locale, id_brand: id_brand, filter: filter, filter_value: filter_value}),
            data: {id_brand: id_brand, filter: filter, filter_value: filter_value}, //, id_mts: id_mts, motor: motor},
            dataType: "json",
            beforeSend: function(){ $("body").css("cursor", "progress"); },
            complete: function(){ $("body").css("cursor", "default"); },
            success: function(data) {
                // Limpiamos y llenamos el combo con las opciones del json
                $('#new_car_form_model').empty();
                $('#new_car_form_version').empty();
                $('#new_car_form_version').append("<option></option>");


                if (data['error'] != "No hay coincidencias") {
                    //Primer campo vacío
                    $('form[id=contact]').find('select[id=new_car_form_model]').append("<option></option>");
                    $.each(data, function(idx, elm) {
                        // if (idx == 'id_mts') $('#id_mts').val(elm);
                        // else {
                            if(model == elm.id )
                                $('form[id=contact]').find('select[id=new_car_form_model]').append("<option value=" + elm.id + " selected>" + elm.name + "</option>");
                            else
                                $('form[id=contact]').find('select[id=new_car_form_model]').append("<option value=" + elm.id + ">" + elm.name + "</option>");
                        // }
                    });
                }
                if(id_brand == 0){
                    $('#new_car_form_model').empty();
                    $('form[id=contact]').find('select[id=new_car_form_model]').append("<option value=0 selected>OTHER</option>");
                    $('#new_car_form_version').empty();
                    $('form[id=contact]').find('select[id=new_car_form_version]').append("<option value=0>OTHER</option>");

                    $("#new_car_form_plateNumber").val("");
                   // var version = $("#ticket_version").val();
                   // if(version != undefined && version != ""){
                   //     fill_version(version);
                   // }
                }
                if(model != undefined && model != '' ) {
                    var version = $("#ticket_version").val();
                    if(version != undefined && version != ""){

                        $('#car').text($('select[id=new_car_form_brand] option:selected').text()+ ' '+$('select[id=new_car_form_model] option:selected').text());
                        if (motor == undefined) motor = $('form[id=contact]').find('input[id=new_car_form_motor]').val();

                        if (model != undefined && model != "") {
                            var route  = 'car_version';
                            var locale = $(document).find("#data_locale").val();
                            var filter = '';
                            var filter_value = '';
                            // var id_mts = '';

                            if ($('#year_selected').val()  == '') { filter       = 'year';
                                                                    filter_value = $('#new_car_form_year').val(); }

                            if ($('#motor_selected').val() == '') { filter       = 'motor';
                                                                    filter_value = $('#new_car_form_motor').val();
                                                                    // id_mts       = $('#id_mts').val();
                                                                  }

                            $.ajax({
                                type: "POST",
                                url: Routing.generate(route, {_locale: locale, id_model: model, filter: filter, filter_value: filter_value}),
                                data: {id_model: model, filter: filter, filter_value: filter_value}, //, id_mts: id_mts},
                                dataType: "json",
                                beforeSend: function(){ $("body").css("cursor", "progress"); },
                                complete: function(){ $("body").css("cursor", "default"); },
                                success: function(data) {
                                    // Limpiamos y llenamos el combo con las opciones del json
                                    $('#new_car_form_version').empty();


                                    if (data['error'] != "No hay coincidencias") {

                                        var dis_url = $( "#dis-url" ).val();
                                        var vts_url = $( "#vts-url" ).val();

                                        //Primer campo vacío
                                        $('form[id=contact]').find('select[id=new_car_form_version]').append("<option></option>");

                                        $.each(data, function(idx, elm) {
                                            // if (idx == 'id_mts') $('#id_mts').val(elm);
                                            // else {
                                                if(version == elm.id ){
                                                    var mt = elm.name.substring(elm.name.indexOf("[")+1, elm.name.indexOf("]"));

                                                   // if(motor == undefined || (motor == mt))

                                                        $('form[id=contact]').find('select[id=new_car_form_version]').append("<option value=" + elm.id + " selected>" + elm.name + "</option>");
                                                }
                                                else
                                                    $('form[id=contact]').find('select[id=new_car_form_version]').append("<option value=" + elm.id + ">" + elm.name + "</option>");

                                                $( "#dis" ).attr("href", dis_url+'/model-'+elm.model);
                                                $( "#vts" ).attr("href", vts_url+'/'+elm.brand+'/'+elm.model);
                                            // }
                                        });
                                    }
                                    if(version != undefined && version != '' ) {

                                        var system = $('#id_system').val();
                                        if(system == '' ) { system = $("#ticket_system").val(); }

                                        if(system != '' ) {
                                            $('#id_system').val(system);
                                            var subsystem = $("#ticket_subsystem").val();
                                            if (subsystem != undefined)
                                               fill_subsystem(subsystem);
                                        }

                                    }

                                },
                                error: function() {
                                    console.log("Error al cargar versiones...");
                                }
                            });
                        }
                    }
                    $("#new_car_form_year").val(dataPN.year);
                    $("#new_car_form_motor").val(dataPN.motor);
                    $("#new_car_form_kW").val(dataPN.kw);
                    $("#new_car_form_displacement").val(dataPN.cm3);
                    $("#new_car_form_vin").val(dataPN.vin);
                }
            },
            error: function() {
                console.log("Error al cargar modelos...");
            }
        });
    }
}

$("#btn_search_ticket_id").on('click', function ()
{
    var idTicket = $(document).find('#flt_id').val();
    var locale    = $(document).find("#data_locale").val();

    var  url=  Routing.generate('showTicket', {_locale: locale, id: idTicket});
    window.open(url, "_blank");

});
