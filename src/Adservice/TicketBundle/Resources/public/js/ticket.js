
$(document).ready(function() {

    var new_ticket = $('#newTicket');
    if (typeof new_ticket != 'undefined') {
        new_ticket.focus();
    }
});

    //vacia tbl_similar
    $(document).on('change','#new_car_form_brand'   ,function(){ clear_tbl_similar('brand' ); });
    $(document).on('change','#new_car_form_model'   ,function(){ clear_tbl_similar('model' ); update_ged_btns('model'); });
    $(document).on('change','#new_car_form_version' ,function(){ update_ged_btns('version'); });
    $(document).on('change','#id_system'            ,function(){ clear_tbl_similar('system'); });
    //llena tbl_similar
    $(document).on('change','#ticket_form_subsystem',function(){ fill_tbl_similar(); });

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

/**
 * Actualiza la url de los botones de otras plataformas con los datos del vehiculo
 */
function update_ged_btns(type) {
    
    var brand = $( "#new_car_form_brand" ).val();
    var model = $( "#new_car_form_model" ).val();
    var dis_url = $( "#dis-url" ).val();
    var vts_url = $( "#vts-url" ).val();
    
    if (type == 'model') {
        $( "#dis" ).attr("href", dis_url+'/model-'+model);
        $( "#vts" ).attr("href", vts_url+'/'+brand+'/'+model);
    }
    else{
        var version = $( "#new_car_form_version" ).val();
        
        $( "#dis" ).attr("href", dis_url+'/'+version);
        $( "#vts" ).attr("href", vts_url+'/'+brand+'/'+model+'/'+version);
    }
}
