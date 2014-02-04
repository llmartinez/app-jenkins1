/**
 * Rellena (populate) el combo de las provincias segun la comunidad autonoma seleccionada por el usuario
 * @param {url de tipo {{ path('mi_path') }}} url_ajax
 */
function fill_model(url_ajax) {
    
    var id_brand = $('form[id=contact]').find('select[id=idBrand]').val()
    
    $.ajax({
        type: "POST",
        url: url_ajax,
        data: {id_brand: id_brand},
        dataType: "json",
        success: function(data) {
            // Limpiamos y llenamos el combo con las opciones del json
            $('#idModel').empty();
            $.each(data, function(idx, elm) {
                $('form[id=contact]').find('select[id=idModel]').append("<option value=" + elm.id + ">" + elm.name + "</option>");
            });
//            console.log(data);        
        },
        error: function() {
            console.log("Error al cargar modelos...");
        }
    });
}

/**
 * Rellena (populate) el combo de las provincias segun la comunidad autonoma seleccionada por el usuario
 * @param {url de tipo {{ path('mi_path') }}} url_ajax
 */
function fill_version(url_ajax) {
    
    var id_model = $('form[id=contact]').find('select[id=idModel]').val()
    
    $.ajax({
        type: "POST",
        url: url_ajax,
        data: {id_model: id_model},
        dataType: "json",
        success: function(data) {
            // Limpiamos y llenamos el combo con las opciones del json
            $('#idVersion').empty();
            $.each(data, function(idx, elm) {
                $('form[id=contact]').find('select[id=idVersion]').append("<option value=" + elm.id + ">" + elm.name + "</option>");
            });
//            console.log(data);        
        },
        error: function() {
            console.log("Error al cargar versiones...");
        }
    });
}