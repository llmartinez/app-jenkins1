/**
 * Rellena (fill) el combo de los tickets segun la opcion seleccionada por el usuario
 * @param {url de tipo {{ path('mi_path') }}} url_ajax
 */
function fill_tickets(url_ajax) {

    var option = $('select[id=slct_historyTickets]').val();
    $.ajax({
        type: "POST",
        url: url_ajax,
        data: {option: option},
        dataType: "json",
        success: function(data) {
            // Limpiamos y llenamos el combo con las opciones del json
            $('#ticketBody').empty();

            $.each(data, function(idx, elm) {

                if (elm.error) {
                    $('#ticketBody').append("<tr><td></td><td></td><td>" + elm.error + "</td></tr>");
                }else{
                    var route = $('#route').val();
                    route = route.replace("PLACEHOLDER", elm.id );
                    $('#ticketBody').append("<tr> <td>" + elm.date + "</td><td>" + elm.workshop + "</td>"
                                       +"<td>#"+ elm.id +": <a href='"+route+"'>" + elm.description +  "</a></td></tr>");
                }
            });

        },
        error: function() {
            console.log("Error al cargar tickets...");
        }
    });
}

/**
 * De la href del modal que envia al delete, se le cambia el "foo" por el id que queremos borrar
 * @param int user_id
 */
function confirm_delete_ticket_modal(id_ticket) {
    var custom_href = $('.modal-footer').find('a').attr('href');
    custom_href = custom_href.replace('foo', id_ticket);
    $('.modal-footer').find('a').attr('href', custom_href);
}

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
            $('#edit_ticket_form_subsystem').empty();
            $('#close_ticket_form_subsystem').empty();
            //Primer campo vacío
            $.each(data, function(idx, elm) {
                $('form[id=contact]').find('select[id=new_ticket_form_subsystem]').append("<option value=" + elm.id + ">" + elm.name + "</option>");
                $('form[id=contact]').find('select[id=edit_ticket_form_subsystem]').append("<option value=" + elm.id + ">" + elm.name + "</option>");
                $('form[id=contact]').find('select[id=close_ticket_form_subsystem]').append("<option value=" + elm.id + ">" + elm.name + "</option>");
            });
        },
        error: function() {
            console.log("Error al cargar subsistemas...");
        }
    });
}
/**
 * Rellena (fill) el combo de los subsistemas (subsystem) segun el sistema (system) seleccionado por el usuario
 * @param {url de tipo {{ path('mi_path') }}} url_ajax
 */
function fill_tbl_subsystem(url_ajax) {

    var id_subsystem = $('form[id=contact]').find('select[id=new_ticket_form_subsystem]').val();

    $.ajax({
        type: "POST",
        url: url_ajax,
        data: {id_subsystem: id_subsystem},
        dataType: "json",
        success: function(data) {

            // Limpiamos y llenamos el combo con las opciones del json
            $( "#tbl_systems" ).empty();
            $( "#tbl_systems" ).append( "<legend>Similar Tickets (subsystem)</legend>");
            $( "#tbl_systems" ).append("<tr><th class='padded'> CAR </th><th class='padded'> DESCRIPTION </th></tr>");
            //Primer campo vacío
            $.each(data, function(idx, elm) {

                if (idx != "error") {
                    $("#tbl_systems").append("<tr><td class='padded'>" + elm.car + "</td><td class='padded'>" + elm.description + "</td></tr>");
                }
                else{
                    $( "#tbl_systems" ).empty();
                    // $( "#tbl_systems" ).append("<tr><td>" + elm + "</td></tr>");
                }
            });
        },
        error: function() {
            console.log("Error al cargar tickets de subsistemas...");
        }
    });
}