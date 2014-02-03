/**
 * Rellena (populate) el combo de las provincias segun la comunidad autonoma seleccionada por el usuario
 * @param {url de tipo {{ path('mi_path') }}} url_ajax
 * @param id_brand
 *  models
 */
function fill_model(url_ajax, id_brand, models) {

    $.ajax({
        type: "POST",
        url: url_ajax,
        data: {models: models},
        dataType: "json",
        success: function(data) {
            // Limpiamos y llenamos el combo con las opciones del json
            $('#model').empty();
            $.each(data, function(idx, elm) {
                $('#model').append("<option value=" + elm.id + ">" + elm.name + "</option>");
            });
        },
        error: function() {
            console.log("Error al cargar modelos...");
        }
    });
}