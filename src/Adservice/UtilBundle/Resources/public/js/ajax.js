
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
        success : function(data) {
            // Limpiamos y llenamos el combo con las opciones del json
            $('#data_regions').empty();
            $("#slct_region").empty();

            var region_edit = '';
            $.each(data, function(idx, elm) {

                if (idx != "error") {
                    if((region != undefined) && (string_to_slug(elm.region) == string_to_slug(region)))
                    {
                        region_edit = elm.region; city_edit = city;
                        $('#data_regions').append("<option value="+elm.id+" selected>"+elm.region+"</option>");
                    }
                    else{
                        if( region != 'no-region' ) { region_edit = region; city_edit = city;
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
        success : function(data) {
            // Limpiamos y llenamos el combo con las opciones del json
            if (data['error'] != "No hay coincidencias") {
                $('form').find('select[id*=_shop]').empty();
                $('form').find('select[id*=_shop]').append("<option value=0></option>");
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
        success : function(data) {
            // Limpiamos y llenamos el combo con las opciones del json
            if (data['error'] != "No hay coincidencias") {
                $('#adservice_workshopbundle_workshoptype_code_workshop').empty();

                $('#adservice_workshopbundle_workshoptype_code_workshop').val(data['code']);

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

function fill_model() {

    $('#car').text($('select[id=new_car_form_brand] option:selected').text());

    var id_brand = $('form[id=contact]').find('select[id=new_car_form_brand]').val();

    var route  = 'car_model';
    var locale = $(document).find("#data_locale").val();
    var filter = '';
    var filter_value = '';

    if ($('#year_selected').val()  == '') { filter       = 'year';
                                            filter_value = $('#new_car_form_year').val(); }

    if ($('#motor_selected').val() == '') { filter       = 'motor';
                                            filter_value = $('#new_car_form_motor').val(); }

    $.ajax({
        type: "POST",
        url: Routing.generate(route, {_locale: locale, id_brand: id_brand, filter: filter, filter_value: filter_value}),
        data: {id_brand: id_brand, filter: filter, filter_value: filter_value},
        dataType: "json",
        success: function(data) {
            // Limpiamos y llenamos el combo con las opciones del json
            $('#new_car_form_model').empty();
            $('#new_car_form_version').empty();

            if (data['error'] != "No hay coincidencias") {
                //Primer campo vacío
                $.each(data, function(idx, elm) {
                    $('form[id=contact]').find('select[id=new_car_form_model]').append("<option value=" + elm.id + ">" + elm.name + "</option>");
                });
            }
        },
        error: function() {
            console.log("Error al cargar modelos...");
        }
    });
}

/**
 * Rellena (fill) el combo de las versiones (version) segun el modelo (model) seleccionado por el usuario
 */
function fill_version() {

    $('#car').text($('select[id=new_car_form_brand] option:selected').text()+ ' '+$('select[id=new_car_form_model] option:selected').text());
    var id_model = $('form[id=contact]').find('select[id=new_car_form_model]').val();

    var route  = 'car_version';
    var locale = $(document).find("#data_locale").val();
    var filter = '';
    var filter_value = '';

    if ($('#year_selected').val()  == '') { filter       = 'year';
                                            filter_value = $('#new_car_form_year').val(); }

    if ($('#motor_selected').val() == '') { filter       = 'motor';
                                            filter_value = $('#new_car_form_motor').val(); }

    $.ajax({
        type: "POST",
        url: Routing.generate(route, {_locale: locale, id_model: id_model, filter: filter, filter_value: filter_value}),
        data: {id_model: id_model, filter: filter, filter_value: filter_value},
        dataType: "json",
        success: function(data) {
            // Limpiamos y llenamos el combo con las opciones del json
            $('#new_car_form_version').empty();
            $('#ticket_form_subsystem').empty();
            $('#new_car_form_year').val('');
            $('#new_car_form_motor').val('');
            $('#new_car_form_kW').val('');
            $('#new_car_form_displacement').val('');
            $('#new_car_form_vin').val('');
            $('#new_car_form_plateNumber').val('');

            if (data['error'] != "No hay coincidencias") {

            var dis_url = $( "#dis-url" ).val();
            var vts_url = $( "#vts-url" ).val();

                $.each(data, function(idx, elm) {
                    $('form[id=contact]').find('select[id=new_car_form_version]').append("<option value=" + elm.id + ">" + elm.name + "</option>");
                    $( "#dis" ).attr("href", dis_url+'/model-'+elm.model);
                    $( "#vts" ).attr("href", vts_url+'/'+elm.brand+'/'+elm.model);
                });
            }

        },
        error: function() {
            console.log("Error al cargar versiones...");
        }
    });
}

/**
 * Rellena (fill) el combo de las versiones (version) segun el modelo (model) seleccionado por el usuario
 */
function fill_car_data() {

    $('#car').text($('select[id=new_car_form_brand] option:selected').text()+ ' '+$('select[id=new_car_form_model] option:selected').text()+ ' '+$('select[id=new_car_form_version] option:selected').text());
    var id_version = $('form[id=contact]').find('select[id=new_car_form_version]').val();

    var route  = 'car_data';
    var locale = $(document).find("#data_locale").val();

    $.ajax({
        type: "POST",
        url: Routing.generate(route, {_locale: locale, id_version: id_version}),
        data: {id_version: id_version},
        dataType: "json",
        success: function(data) {
            // Limpiamos y llenamos los campos del coche
            $.each(data, function(idx, elm) {
                $('form[id=contact]').find('#new_car_form_year'        ).val(elm.year        );
                $('form[id=contact]').find('#new_car_form_motor'       ).val(elm.motor       );
                $('form[id=contact]').find('#new_car_form_kW'          ).val(elm.kw          );
                $('form[id=contact]').find('#new_car_form_displacement').val(elm.displacement);
                var dis_url = $( "#dis-url" ).val();
                var vts_url = $( "#vts-url" ).val();

                $( "#dis" ).attr("href", dis_url+'/'+elm.id);
                $( "#vts" ).attr("href", vts_url+'/'+elm.brand+'/'+elm.model+'/'+elm.id);
            });
        },
        error: function() {
            console.log("Error al cargar datos de coche...");
        }
    });
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
        success: function(data) {
            // Vaciamos marca, modelo y gama y recargamos las marcas filtradas
            $('#new_car_form_brand').empty();
            $('#new_car_form_model').empty();
            $('#new_car_form_version').empty();
            $.each(data, function(idx, elm) {
                $('form[id=contact]').find('select[id=new_car_form_brand]').append("<option value=" + elm.id + ">" + elm.name + "</option>");
            });
            //Cambiamos el icono para indicar que se esta filtrando por motor
            $('#filter_motor').empty();
            $('#filter_motor').append('<img class="img_icon" src='+$('#funnel').val()+'></a>');

            $('#filter_year').empty();
            if(year != ''){
                $('#filter_year').append('<img class="img_icon" id="year_selected" src='+$('#funnel_filtered').val()+'></a>');
            }else{
                $('#filter_year').append('<img class="img_icon" id="year_selected" src='+$('#funnel').val()+'></a>');
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

        $.ajax({
            type: "POST",
            url: Routing.generate(route, {_locale: locale, motor: motor}),
            data: {motor: motor},
            dataType: "json",
            success: function(data) {
                // Vaciamos marca, modelo y gama y recargamos las marcas filtradas
                $('#new_car_form_brand').empty();
                $('#new_car_form_model').empty();
                $('#new_car_form_version').empty();
                $.each(data, function(idx, elm) {
                    $('form[id=contact]').find('select[id=new_car_form_brand]').append("<option value=" + elm.id + ">" + elm.name + "</option>");
                });
                //Cambiamos el icono para indicar que se esta filtrando por motor
                $('#filter_year').empty();
                $('#filter_year').append('<img class="img_icon" src='+$('#funnel').val()+'></a>');

                $('#filter_motor').empty();
                if(motor != ''){
                    $('#filter_motor').append('<img class="img_icon" id="motor_selected" src='+$('#funnel_filtered').val()+'></a>');
                }else{
                    $('#filter_motor').append('<img class="img_icon" id="motor_selected" src='+$('#funnel').val()+'></a>');
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
function fill_subsystem() {

    var id_system = $('form[id=contact]').find('select[id=id_system]').val();

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
        success: function(data) {

            // Limpiamos y llenamos el combo con las opciones del json
            $('select[id*=_subsystem]').empty();

            //Primer campo vacío
            $.each(data, function(idx, elm) {
                if (elm.id == id_subsystem) $('form[id=contact]').find('select[id*=_subsystem]').append("<option value=" + elm.id + " selected>" + elm.name + "</option>");
                else                        $('form[id=contact]').find('select[id*=_subsystem]').append("<option value=" + elm.id + ">" + elm.name + "</option>");
            });
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

    $.ajax({
        type: "POST",
        url: Routing.generate(route, {_locale: locale }),
        data: { id_model: id_model, id_subsystem: id_subsystem },
        dataType: "json",
        success: function(data) {

            // Limpiamos y llenamos el combo con las opciones del json
            $( "#tbl_similar" ).empty();
            $( "#tbl_similar" ).append("<tr><th class='padded'> CAR </th><th class='padded'> DESCRIPTION </th></tr>");
            //Primer campo vacío
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

    var route  = 'tbl_repeated';
    var locale = $(document).find("#data_locale").val();
    var id_model     = $('form').find('select[id*=model]').val();
    var id_subsystem = $('form').find('select[id*=subsystem]').val();

    $.ajax({
        type: "POST",
        url: Routing.generate(route, {_locale: locale }),
        data: { id_model: id_model, id_subsystem: id_subsystem },
        dataType: "json",
        success: function(data) {

            // Limpiamos y llenamos el combo con las opciones del json
            $( "#tbl_repeated" ).empty();
            $( "#tbl_repeated" ).append("<tr><th class='padded'> CAR </th><th class='padded'> DESCRIPTION </th></tr>");
            //Primer campo vacío
            $.each(data, function(idx, elm) {

                if (idx != "error") {
                    var url = Routing.generate('showTicketReadonly', {_locale: locale, id: elm.id });
                    $("#tbl_repeated").append("<tr><td class='padded'>" + elm.car + "</td><td class='padded'><a onclick='window.open( \""+ url +"\" , \"width=1000, height=800, top=100px, left=100px, toolbar=no, status=no, location=no, directories=no, menubar=no\" )' > " + elm.description + "</a></td></tr>");
                }
                else{
                    $( "#tbl_repeated" ).empty();
                    $( "#tbl_repeated" ).append("<tr><td>" + elm + "</td></tr>");
                }
            });
        },
        error: function() {
            console.log("Error al cargar tickets repetidos..");
        }
    });
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