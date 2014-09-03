
    $.ajaxSetup({ cache: false });
/**
 * Funcion que rellena (populate) el combo de las regiones segun el país seleccionado por el usuario
 * @param {url de tipo {{ path('mi_path') }}} url_ajax
 */
function populate_region(url_ajax, region, city){
    var id_country = $('form').find('select[name*=country]').val();

   $.ajax({
        type        : "POST",
        url         : url_ajax,
        data        : {id_country : id_country},
        dataType    : "json",
        success : function(data) {
            // Limpiamos y llenamos el combo con las opciones del json
            $('#data_regions').empty();
            $("#slct_region").empty();

            var region_edit = '';
            $.each(data, function(idx, elm) {

                if((region != undefined) && (string_to_slug(elm.region) == string_to_slug(region))) {  region_edit = elm.region; city_edit = city;
                                            $('#data_regions').append("<option value="+elm.id+" selected>"+elm.region+"</option>");}
                else{
                    if( region != 'none' ) { region_edit = region; city_edit = city;
                        $('#data_regions').append("<option value="+elm.id+">"+elm.region+"</option>");
                    }
                    else $('#data_regions').append("<option value="+elm.id+">"+elm.region+"</option>");
                }
            });
            $("#slct_region").html($('#data_regions').html());
            $("#slct_region").select2({
                placeholder: "Select a State",
                allowClear: true
            });

            if($(':text[id*=region]').val() != ''){
                if(region_edit != '') {
                                        $("#s2id_slct_region .select2-chosen").text(region_edit);
                                        $("#s2id_slct_city .select2-chosen"  ).text(city_edit);
                }
                else                  {
                                        $("#s2id_slct_region .select2-chosen").text($('#no-region').val());
                                        $("#s2id_slct_city .select2-chosen"  ).text($('#no-city'  ).val());
                }
            }
            else{
                $("#s2id_slct_region .select2-chosen").text($('#no-region').val());

                $(':text[id*=region]').val($('#no-region').val());
            }
        },
        error : function(){
            console.log("Error al cargar las regiones...");
        }
    });
}

/**
 * Funcion que rellena (populate) el combo de las ciudades segun la region seleccionada por el usuario
 * @param {url de tipo {{ path('mi_path') }}} url_ajax
 */
function populate_city(url_ajax, city){
   var id_region = $('#s2id_slct_region .select2-chosen').text();

   $.ajax({
        type        : "POST",
        url         : url_ajax,
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
            $("#slct_city").select2({
                placeholder: "Select a State",
                allowClear: true
            });
            $('div#div_regions span.select2-chosen').text(id_region);
        },
        error : function(){
            console.log("Error al cargar las ciudades...");
        }
    });
}

/**
 * Funcion que rellena (populate) el combo de las regiones segun el país seleccionado por el usuario
 * @param route
 */
function populate_shop(route, id_shop){
    var id_partner = $('form').find('select[name*=partner]').val();
    if(id_shop == undefined){ id_shop = ''; }

    var locale = $(document).find("#data_locale").val();

    $.ajax({
        type        : "POST",
        url         : Routing.generate(route, {_locale: locale }),
        data        : {id_partner : id_partner},
        dataType    : "json",
        success : function(data) {
            // Limpiamos y llenamos el combo con las opciones del json
            $('form').find('select[id*=_shop]').empty();
            $.each(data, function(idx, elm) {

                if(elm.id == id_shop) $('form').find('select[id*=_shop]').append("<option value="+elm.id+" selected>"+elm.shop+"</option>");
                else                  $('form').find('select[id*=_shop]').append("<option value="+elm.id+">"+elm.shop+"</option>");
            });
        },
        error : function(){
            console.log("Error al cargar las tiendas...");
        }
    });
}

/**
 * Rellena el combo de los modelos segun la marca seleccionada por el usuario
 * @param route
 */

function fill_model(route) {

    var id_brand = $('form[id=contact]').find('select[id=new_car_form_brand]').val();

    var locale = $(document).find("#data_locale").val();

    $.ajax({
        type: "POST",
        url: Routing.generate(route, {_locale: locale, id_brand: id_brand}),
        data: {id_brand: id_brand},
        dataType: "json",
        success: function(data) {
            // Limpiamos y llenamos el combo con las opciones del json
            $('#new_car_form_model').empty();
            //Primer campo vacío
            // $('form[id=contact]').find('select[id=new_car_form_model]').append("<option value=0>Select Model..</option>");
            $.each(data, function(idx, elm) {
                $('form[id=contact]').find('select[id=new_car_form_model]').append("<option value=" + elm.id + ">" + elm.name + "</option>");
            });
        },
        error: function() {
            console.log("Error al cargar modelos...");
        }
    });
}

/**
 * Rellena (fill) el combo de las versiones (version) segun el modelo (model) seleccionado por el usuario
 * @param route
 */
function fill_version(route) {

    var id_model = $('form[id=contact]').find('select[id=new_car_form_model]').val();

    var locale = $(document).find("#data_locale").val();

    $.ajax({
        type: "POST",
        url: Routing.generate(route, {_locale: locale, id_model: id_model}),
        data: {id_model: id_model},
        dataType: "json",
        success: function(data) {
            // Limpiamos y llenamos el combo con las opciones del json
            $('#new_car_form_version').empty();
            //Primer campo vacío
            // $('form[id=contact]').find('select[id=new_car_form_version]').append("<option value=0>Select Version..</option>");
            $.each(data, function(idx, elm) {
                $('form[id=contact]').find('select[id=new_car_form_version]').append("<option value=" + elm.id + ">" + elm.name + "</option>");
            });
        },
        error: function() {
            console.log("Error al cargar versiones...");
        }
    });
}

/**
 * Rellena (fill) el combo de los subsistemas (subsystem) segun el sistema (system) seleccionado por el usuario
 * @param route
 */
function fill_subsystem(route, form_subsystem) {

    var id_system = $('form[id=contact]').find('select[id=id_system]').val();

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