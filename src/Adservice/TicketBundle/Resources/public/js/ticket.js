
$(document).ready(function() {

    var new_ticket = $('#newTicket');
    if (typeof new_ticket != 'undefined') {
        new_ticket.focus();
    }
});

    //vacia tbl_similar
    $(document).on('change','#new_car_form_brand'   ,function(){ clear_tbl_similar('brand' ); });
    $(document).on('change','#new_car_form_model'   ,function(){ clear_tbl_similar('model' ); });
    $(document).on('change','#id_system'            ,function(){ clear_tbl_similar('system'); });
    //llena tbl_similar
    $(document).on('change','#ticket_form_subsystem',function(){ fill_tbl_similar(); });
    $('#btn_search_by_bmv').click(function() { search_by_bmv(); });


$('#new_file_form_file').bind('change', function() {

    var size = this.files[0].size;
    var role = $('#role').val();

    if (role = 'ROLE_ASSESSOR') { var max_size = '15000000'; }
    else                        { var max_size = '4000000';  }

    if(size > max_size) {

        $('#alertFileSize').show();

        $('.sendTicket' ).attr("disabled", true );
        $('.closeTicket').attr("disabled", true );
    }
    else{
        $('#alertFileSize').hide();

        $('.sendTicket' ).attr("disabled", false);
        $('.closeTicket').attr("disabled", false);
    }
});

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
 * Busca un coche por Marca Modelo y Gama
 */
function search_by_bmv() {
    var brand    = $('#new_car_form_brand').val();
    var model    = $('#new_car_form_model').val();
    var version  = $('#new_car_form_version').val();
    var system   = $('#id_system').val();
    var subsystem= $('#new_car_form_subsystem').val();
    var num_rows = $('#slct_numRows').val();
    var alert_intro = $('#alert').val();

    if(brand == null) {
        brand = 0;
    }
    if(model == null) {
        model = 0;
        if(brand != 0){
            alert(alert_intro);
            event.preventDefault();
        }
    }
    if(version == null) {
        version = 0;
    }
    if(system == null) {
        system = 0;      
    }
    if(subsystem == null) {
        subsystem = 0;
        if(system != 0){
            alert(alert_intro);
            event.preventDefault();
        }
    }
    if(num_rows == null) { num_rows = 10; }

    var route = 'findTicketByBMV';
    var locale = $(document).find("#data_locale").val();
    var url = Routing.generate(route, {_locale: locale, page: 1, brand: brand, model: model, version: version, system: system, subsystem: subsystem, num_rows: num_rows });
    window.open(url, "_self");
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