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
                                       +"<td>#"+ elm.id +": <a href='"+route+"'>" + elm.title +  "</a></td></tr>");
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
