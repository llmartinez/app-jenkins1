
$(document).ready(function() {

    //vacia tbl_similar
    $('#new_car_form_brand').change(function() { clear_tbl_similar('brand' ) });
    $('#new_car_form_model').change(function() { clear_tbl_similar('model' ) });
    $('#id_system'         ).change(function() { clear_tbl_similar('system') });

    //llena tbl_similar
    //$('#new_car_form_model'       ).change(function() { list_tbl_similar() });
    $("#new_ticket_form_subsystem").change(function() { list_tbl_similar() });

    var new_ticket = $('#newTicket');
    if (typeof new_ticket != 'undefined') {
        new_ticket.focus();
    }
});

/**
 * busca tickets que coincidan en modelo o subsistema con el ticket actual
 * @return AjaxFunction
 */
function list_tbl_similar() {

    var select = document.querySelector('#form_data');
    var data   = select.dataset;

    var url_show = data.similarshow;

    fill_tbl_similar(url_show);
}

/**
 * vacia tbl_similar
 * @return AjaxFunction
 */
function clear_tbl_similar(parent) {

    $( "#tbl_similar" ).empty();

    if (parent == 'brand' )                 { var son = 'Model';     }
    else {
            if (parent == 'model' )         { var son = 'System';    }
            else {
                    if (parent == 'system') { var son = 'Subsystem'; }
            }
    }

    $( "#tbl_similar" ).append("<p>Select "+ son +" for matching similar tickets..</p>");
}

/**
 * Comprueba el checkbox open/closed
 */
function setCheckStatus(){

    if ($('#flt_open').is(':checked') && !($('#flt_closed').is(':checked')))     { var status = 'open';   }
    else{
        if ($('#flt_closed').is(':checked') && !($('#flt_open').is(':checked'))) { var status = 'closed'; }
        else { var status = 'all'; }
    }
    return status;
}

/**
 * Comprueba el checkbox de id ticket
 */
function setCheckId(){
    var filter_id = $('#flt_id').val();

    if (filter_id != "") {

        var reg = new RegExp('^[0-9]+$');

        if( ! reg.test(filter_id)){ var filter_id = 'all'; }
    }
    else{ var filter_id = 'all'; }

    return filter_id;
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

// /**
//  * Rellena (fill) el combo de los tickets segun la opcion seleccionada por el usuario
//  * @param {url de tipo {{ path('mi_path') }}} url_ajax
//  */
// function fill_tickets(url_ajax, url_show) {

//     var option   = $('select[id=slct_historyTickets]').val();
//     var new_page = $('#page').val();

//     var filter_id   = setCheckId();
//     var status      = setCheckStatus();

//     $.ajax({
//         type: "POST",
//         url: url_ajax,
//         data: { option: option, filter_id: filter_id, status: status, url_show: url_show },
//         dataType: "json",
//         success: function(data) {
//             // Limpiamos y llenamos el combo con las opciones del json
//             $('#ticketBody').empty();

//             $.each(data, function(idx, elm) {

//                 if (elm.error) {  $('#ticketBody').append("<tr><td>" + elm.error + "</td><td></td><td></td><td></td><td></td></tr>"); }
//                 else{

//                     var lim_actual = limit*page;
//                     var num_actual = lim_actual-limit;

//                     if (cont > num_actual && cont <= lim_actual ) {

//                         url = url_show.replace("PLACEHOLDER", elm.id);

//                         if( elm.created == 'workshop'){ var created = '<span class="glyphicon glyphicon-user" title="Created by workshop" ></span>';     }
//                         else {                          var created = '<span class="glyphicon glyphicon-earphone" title="Created by assessor" ></span>'; }

//                         $('#ticketBody').append("<tr onclick='window.open(\""+ url +"\",\"_self\")'>"
//                                                    + "<td>" + created +" "+ elm.id + "</td>"
//                                                    + "<td>" + elm.date             + "</td>"
//                                                    + "<td>" + elm.workshop         + "</td>"
//                                                    + "<td>" + elm.car              + "</td>"
//                                                    + "<td>" + elm.description      + "</td>"
//                                                 );
//                     }
//                     cont++;
//                 }

//                 if (!elm.error){ paginator(num_pag); }
//             });

//         },
//         error: function() {
//             console.log("Error al cargar tickets...");
//         }
//     });
// }

// /**
//  * Rellena (fill) el combo de los tickets segun el workshop seleccionado por el usuario
//  * @param {url de tipo {{ path('mi_path') }}} url_ajax
//  */
// function fill_tickets_from_workshop(url_ajax, url_show, user) {

//     var id_workshop = $('#id_workshop').val();
//     var new_page = $('#page').val();

//     if(new_page == "" || new_page == null){ new_page = 1 };

//     var filter_id = setCheckId();
//     var status    = setCheckStatus();

//     $.ajax({
//         type: "POST",
//         url: url_ajax,
//         data: { id_workshop: id_workshop, filter_id: filter_id, status: status, url_show: url_show, user: user },
//         dataType: "json",
//         success: function(data) {
//             // Limpiamos y llenamos el combo con las opciones del json
//             $('#ticketBody').empty();

//             //PAGINACION
//             var total = data.length;
//             var cont  = 1;
//             var limit = max_rows_page;
//             var num_pag = Math.ceil(total/limit);
//             var page = new_page;

//             $.each(data, function(idx, elm) {

//                 if (elm.error) {  $('#ticketBody').append("<tr><td>" + elm.error + "</td><td></td><td></td><td></td><td></td></tr>"); }
//                 else{

//                     var lim_actual = limit*page;
//                     var num_actual = lim_actual-limit;

//                     if (cont > num_actual && cont <= lim_actual ) {

//                         url = url_show.replace("PLACEHOLDER", elm.id);

//                         if( elm.created == 'workshop'){ var created = '<span class="glyphicon glyphicon-user" title="Created by workshop" ></span>';     }
//                         else {                          var created = '<span class="glyphicon glyphicon-earphone" title="Created by assessor" ></span>'; }

//                         if (elm.status instanceof Object) {
//                             if (elm.status['blocked_id'] == user) {
//                                     status = '<a id="locked_ticket" style="color:red"  title="you have this ticket blocked for an answer" >Pending</a>'; }
//                             else {
//                                     status = '<a id="locked_ticket" style="color:gray" title="this ticket is blocked by '+ elm.status['blocked_by'] +'" >Blocked</a>'; }
//                         }
//                         else{ status = elm.status; }


//                         $('#ticketBody').append("<tr onclick='window.open(\""+ url +"\",\"_self\")'>"
//                                                    + "<td>" + created +" "+ elm.id + "</td>"
//                                                    + "<td>" + elm.date             + "</td>"
//                                                    + "<td>" + elm.car              + "</td>"
//                                                    + "<td>" + elm.assignedTo       + "</td>"
//                                                    + "<td>" + elm.description      + "</td>"
//                                                    + "<td>" + status               + "</td>"
//                                                 );
//                     }
//                     cont++;
//                 }

//                 if (!elm.error){ paginator(num_pag); }
//             });

//         },
//         error: function() {
//             console.log("Error al cargar tickets...");
//         }
//     });
// }
