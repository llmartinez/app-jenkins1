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
                var route = $('#route').val(); 
                route = route.replace("PLACEHOLDER", elm.id );
                $('#ticketBody').append("<tr> <td>" + elm.date + "</td><td>" + elm.workshop + "</td>"
                                       +"<td>#"+ elm.id +": <a href='"+route+"'>" + elm.title +  "</a></td></tr>");
            }); 
           
        },
        error: function() {
            console.log("Error al cargar tickets...");
        }
    });
} 

/**
 * Cam
 * @param {url de tipo {{ path('mi_path') }}} url_ajax
 */
function assignement(url_ajax) {
    
    var option = $('#assignement').val();
    
    $.ajax({
        type: "POST",
        url: url_ajax,
        data: {option: option},
        dataType: "json",
        success: function(data) {
            // Limpiamos y llenamos el combo con las opciones del json
            $('#ticketBody').empty();
            $.each(data, function(idx, elm) {
                var route = $('#route').val(); 
                route = route.replace("PLACEHOLDER", elm.id );
                $('#ticketBody').append("<tr> <td>" + elm.date + "</td><td>" + elm.workshop + "</td>"
                                       +"<td>#"+ elm.id +": <a href='"+route+"'>" + elm.title +  "</a></td></tr>");
            }); 
           
        },
        error: function() {
            console.log("Error al cargar tickets...");
        }
    });
}