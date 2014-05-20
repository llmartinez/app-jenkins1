
/**
 * Funcion que rellena (populate) el combo de las regiones segun el país seleccionado por el usuario
 * @param {url de tipo {{ path('mi_path') }}} url_ajax
 */
function populate_region(url_ajax, region){
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
            $.each(data, function(idx, elm) {

                if(elm.region == region) $('#data_regions').append("<option value="+elm.id+" selected>"+elm.region+"</option>");
                else                     $('#data_regions').append("<option value="+elm.id+">"+elm.region+"</option>");
            });
            $("#slct_region").html($('#data_regions').html());
            $("#slct_region").select2({
                placeholder: "Select a State",
                allowClear: true
            });
            $(':text[id*=region]').val( $("#s2id_slct_region .select2-chosen").text('sin region') );
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
            $("#slct_city").empty();
            $.each(data, function(idx, elm) {

                if(elm.city == city) $('#data_cities').append("<option value="+elm.id+" selected>"+elm.city+"</option>");
                else                 $('#data_cities').append("<option value="+elm.id+">"+elm.city+"</option>");
            });
            $("#slct_city").html($('#data_cities').html());
            $("#slct_city").select2({
                placeholder: "Select a State",
                allowClear: true
            });
        },
        error : function(){
            console.log("Error al cargar las ciudades...");
        }
    });
}

/**
 * Funcion que rellena (populate) el combo de las regiones segun el país seleccionado por el usuario
 * @param {url de tipo {{ path('mi_path') }}} url_ajax_partner
 */
function populate_shop(url_ajax_partner, shop){
   var id_partner = $('form').find('select[name*=partner]').val();

   $.ajax({
        type        : "POST",
        url         : url_ajax_partner,
        data        : {id_partner : id_partner},
        dataType    : "json",
        success : function(data) {
            // Limpiamos y llenamos el combo con las opciones del json
            $('form').find('select[id*=_shop]').empty();
            $.each(data, function(idx, elm) {

                if(elm.shop == shop) $('form').find('select[id*=_shop]').append("<option value="+elm.id+" selected>"+elm.shop+"</option>");
                else                 $('form').find('select[id*=_shop]').append("<option value="+elm.id+">"+elm.shop+"</option>");
            });
        },
        error : function(){
            console.log("Error al cargar las tiendas...");
        }
    });
}

/**
 * Rellena (fill) el combo de los modelos (model) segun la marca (brand) seleccionada por el usuario
 * @param {url de tipo {{ path('mi_path') }}} url_ajax
 */
function fill_model(url_ajax) {

    var id_brand = $('form[id=contact]').find('select[id=new_car_form_brand]').val();

    $.ajax({
        type: "POST",
        url: url_ajax,
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
 * @param {url de tipo {{ path('mi_path') }}} url_ajax
 */
function fill_version(url_ajax) {

    var id_model = $('form[id=contact]').find('select[id=new_car_form_model]').val();

    $.ajax({
        type: "POST",
        url: url_ajax,
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
 * @param {url de tipo {{ path('mi_path') }}} url_ajax
 */
function fill_subsystem(url_ajax, form_subsystem) {

    var id_system = $('form[id=contact]').find('select[id=id_system]').val();

    //Valor del subsistema del ticket al cerrar
    var id_subsystem = ($('#'+form_subsystem).val());
    if (id_subsystem == null) $('#'+form_subsystem).empty();

    $.ajax({
        type: "POST",
        url: url_ajax,
        data: {id_system: id_system},
        dataType: "json",
        success: function(data) {

            // Limpiamos y llenamos el combo con las opciones del json
            $('#'+form_subsystem).empty();

            //Primer campo vacío
            $.each(data, function(idx, elm) {
                if (elm.id == id_subsystem)
                    $('form[id=contact]').find('select[id='+form_subsystem+']').append("<option value=" + elm.id + " selected>" + elm.name + "</option>");
                else
                    $('form[id=contact]').find('select[id='+form_subsystem+']').append("<option value=" + elm.id + ">" + elm.name + "</option>");
            });
        },
        error: function() {
            console.log("Error al cargar subsistemas...");
        }
    });
}