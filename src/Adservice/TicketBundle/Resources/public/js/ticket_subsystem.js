
$(document).ready(function() {

    // Limpiamos el combo de subsystem del formulario y cargamos subsistemas
    list_tbl_subsystem();

    //cambiar model en funcion de brand
    $('#id_system').change(function() { list_tbl_subsystem(); });
});


/**
 * Vacia el combo de subsystem del formulario y cargamos subsistemas
 * @return AjaxFunction
 */
function list_tbl_subsystem() {

    var select = document.querySelector('#form_data');
    var data   = select.dataset;

    var form_subsystem = data.formname;
    var route          = 'ticket_system';

    fill_subsystem(route, form_subsystem);
}