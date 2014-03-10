/**
 * Rellena (fill) el combo de los subsistemas (subsystem) segun el sistema (system) seleccionado por el usuario
 * @param {url de tipo {{ path('mi_path') }}} url_ajax
 */
function fill_subsystem(url_ajax) {

    var id_system = $('form[id=contact]').find('select[id=id_system]').val();

    $.ajax({
        type: "POST",
        url: url_ajax,
        data: {id_system: id_system},
        dataType: "json",
        success: function(data) {
            // Limpiamos y llenamos el combo con las opciones del json
            $('#new_ticket_form_subsystem').empty();
            $('#close_ticket_form_subsystem').empty();
            //Primer campo vacío
            $('form[id=contact]').find('select[id=new_ticket_form_subsystem]').append("<option value=0>Select System..</option>");
            $('form[id=contact]').find('select[id=close_ticket_form_subsystem]').append("<option value=0>Select System..</option>");
            $.each(data, function(idx, elm) {
                $('form[id=contact]').find('select[id=new_ticket_form_subsystem]').append("<option value=" + elm.id + ">" + elm.name + "</option>");
                $('form[id=contact]').find('select[id=close_ticket_form_subsystem]').append("<option value=" + elm.id + ">" + elm.name + "</option>");
            });
        },
        error: function() {
            console.log("Error al cargar subsistemas...");
        }
    });
}