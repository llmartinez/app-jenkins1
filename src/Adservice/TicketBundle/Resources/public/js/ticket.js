
/*Numero maximo de tickets que se mostraran en la tabla */
var max_rows_page = 10;

/*Numero maximo de paginas que se mostraran a los lados de la pagina actual de la paginacion */
var max_pages_pagination = 2;

/**
 * Rellena (fill) el combo de los tickets segun la opcion seleccionada por el usuario
 * @param {url de tipo {{ path('mi_path') }}} url_ajax
 */
function fill_tickets(url_ajax, url_show) {

    var option   = $('select[id=slct_historyTickets]').val();
    var new_page = $('#page').val();

    var filter_id = setCheckId();
    var status    = setCheckStatus();

    $.ajax({
        type: "POST",
        url: url_ajax,
        data: { option: option, filter_id: filter_id, status: status, url_show: url_show },
        dataType: "json",
        success: function(data) {
            // Limpiamos y llenamos el combo con las opciones del json
            $('#ticketBody').empty();

            //PAGINACION
            var total = data.length;
            var cont  = 1;
            var limit = max_rows_page;
            var num_pag = Math.ceil(total/limit);
            if(new_page != ""){ page = new_page } else{ page = 1 };

            $.each(data, function(idx, elm) {

                if (elm.error) {  $('#ticketBody').append("<tr><td>" + elm.error + "</td><td></td><td></td><td></td><td></td></tr>"); }
                else{

                    var lim_actual = limit*page;
                    var num_actual = lim_actual-limit;

                    if (cont > num_actual && cont <= lim_actual ) {

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
                    cont++;
                }

                if (!elm.error){ paginator(num_pag); }
            });

        },
        error: function() {
            console.log("Error al cargar tickets...");
        }
    });
}

/**
 * Rellena (fill) el combo de los tickets segun el workshop seleccionado por el usuario
 * @param {url de tipo {{ path('mi_path') }}} url_ajax
 */
function fill_tickets_from_workshop(url_ajax, url_show, user) {

    var id_workshop = $('#id_workshop').val();
    var new_page = $('#page').val();

    var filter_id = setCheckId();
    var status    = setCheckStatus();

    $.ajax({
        type: "POST",
        url: url_ajax,
        data: { id_workshop: id_workshop, filter_id: filter_id, status: status, url_show: url_show, user: user },
        dataType: "json",
        success: function(data) {
            // Limpiamos y llenamos el combo con las opciones del json
            $('#ticketBody').empty();

            //PAGINACION
            var total = data.length;
            var cont  = 1;
            var limit = max_rows_page;
            var num_pag = Math.ceil(total/limit);
            if(new_page != ""){ page = new_page } else{ page = 1 };

            $.each(data, function(idx, elm) {

                if (elm.error) {  $('#ticketBody').append("<tr><td>" + elm.error + "</td><td></td><td></td><td></td><td></td></tr>"); }
                else{

                    var lim_actual = limit*page;
                    var num_actual = lim_actual-limit;

                    if (cont > num_actual && cont <= lim_actual ) {

                        url = url_show.replace("PLACEHOLDER", elm.id);

                        if( elm.created == 'workshop'){ var created = '<span class="glyphicon glyphicon-user" title="Created by workshop" ></span>';     }
                        else {                          var created = '<span class="glyphicon glyphicon-earphone" title="Created by assessor" ></span>'; }

                        if (elm.status instanceof Object) {
                            if (elm.status['blocked_id'] == user) {
                                    status = '<a id="locked_ticket" style="color:red"  title="you have this ticket blocked for an answer" >Pending</a>'; }
                            else {
                                    status = '<a id="locked_ticket" style="color:gray" title="this ticket is blocked by '+ elm.status['blocked_by'] +'" >Blocked</a>'; }
                        }
                        else{ status = elm.status; }


                        $('#ticketBody').append("<tr onclick='window.open(\""+ url +"\",\"_self\")'>"
                                                   + "<td>" + created +" "+ elm.id + "</td>"
                                                   + "<td>" + elm.date             + "</td>"
                                                   + "<td>" + elm.car              + "</td>"
                                                   + "<td>" + elm.assignedTo       + "</td>"
                                                   + "<td>" + elm.description      + "</td>"
                                                   + "<td>" + status               + "</td>"
                                                );
                    }
                    cont++;
                }

                if (!elm.error){ paginator(num_pag); }
            });

        },
        error: function() {
            console.log("Error al cargar tickets...");
        }
    });
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
 * paginacion de la lista de tickets
 */
function paginator(num_pag){
        page = $('#page').val();

        $('#prev_pages').empty();
        $('#next_pages').empty();

        for ( var i=1; i<=max_pages_pagination; i++) {

            var prev = parseFloat($('#page').val()) - parseFloat(i);
            if( prev > 0 ) { $('#prev_pages').prepend('<button class="page_number change_page" >'+ prev +'</button>'); }

            var next = parseFloat($('#page').val()) + parseFloat(i);
            if( next <= num_pag){ $('#next_pages').append('<button class="page_number change_page" >'+ next +'</button>'); }

        }
        // if (prev > 1)        { $('#prev_pages').prepend('<input  type="text" id="change_page" class="page_number" value="1"  disabled >...'); }
        // if (next < num_pag) { $('#next_pages').append('<input  type="text" id="change_page" class="page_number" value="'+ num_pag +'"  disabled >'); }
        $('.change_page').click(function() {
           $('#page').val($(this).text());
           tickets_ajax();
        });
        $('#totalpage').val(num_pag);
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
