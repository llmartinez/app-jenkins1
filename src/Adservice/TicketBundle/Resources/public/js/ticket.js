/**
 * Rellena (fill) el combo de los tickets segun la opcion seleccionada por el usuario
 * @param {url de tipo {{ path('mi_path') }}} url_ajax
 */
function fill_tickets(url_ajax, url_show) {

    var option = $('select[id=slct_historyTickets]').val();

    $.ajax({
        type: "POST",
        url: url_ajax,
        data: { option: option, url_show: url_show },
        dataType: "json",
        success: function(data) {
            // Limpiamos y llenamos el combo con las opciones del json
            $('#ticketBody').empty();

            $.each(data, function(idx, elm) {

                if (elm.error) {  $('#ticketBody').append("<tr><td>" + elm.error + "</td><td></td><td></td><td></td><td></td></tr>"); }
                else{
                    url = url_show.replace("PLACEHOLDER", elm.id);

                    if( elm.created == 'workshop'){ var created = '<span class="glyphicon glyphicon-user" title="Created by workshop" ></span>';     }
                    else {                          var created = '<span class="glyphicon glyphicon-earphone" title="Created by assessor" ></span>'; }

                    $('#ticketBody').append("<tr onclick='window.open(\""+ url +"\",\"_self\")'>"
                                               + "<td>" + created +" "+ elm.id + "</td>"
                                               + "<td>" + elm.date             + "</td>"
                                               + "<td>" + elm.workshop         + "</td>"
                                               + "<td>" + elm.car              + "</td>"
                                               + "<td>" + elm.description      + "</td>"
                                            );
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
                $('form[id=contact]').find('select[id=new_ticket_form_subsystem]'  ).append("<option value=" + elm.id + ">" + elm.name + "</option>");
                $('form[id=contact]').find('select[id=edit_ticket_form_subsystem]' ).append("<option value=" + elm.id + ">" + elm.name + "</option>");
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
function fill_tbl_similar(url_ajax, url_show) {

    var id_model     = $('form[id=contact]').find('select[id=new_car_form_model]').val();
    var id_subsystem = $('form[id=contact]').find('select[id=new_ticket_form_subsystem]').val();

    $.ajax({
        type: "POST",
        url: url_ajax,
        data: { id_model: id_model, id_subsystem: id_subsystem, url_show: url_show },
        dataType: "json",
        success: function(data) {

            // Limpiamos y llenamos el combo con las opciones del json
            $( "#tbl_similar" ).empty();
            $( "#tbl_similar" ).append("<tr><th class='padded'> CAR </th><th class='padded'> DESCRIPTION </th></tr>");
            //Primer campo vacío
            $.each(data, function(idx, elm) {

                if (idx != "error") {
                    url = url_show.replace("PLACEHOLDER", elm.id);
                    $("#tbl_similar").append("<tr><td class='padded'>" + elm.car + "</td><td class='padded'><a onclick='window.open( \""+ url +"\" , \"Ticket #"+ elm.id +"\", \"width=1000, height=800, top=100px, left=100px, toolbar=no, status=no, location=no, directories=no, menubar=no\" )' > " + elm.description + "</a></td></tr>");
                }
                else{
                    $( "#tbl_similar" ).empty();
                    $( "#tbl_similar" ).append("<tr><td>" + elm + "</td></tr>");
                }
            });
        },
        error: function() {
            console.log("Error al cargar tickets de subsistemas...");
        }
    });
}
