
$(document).ready(function() {

    var new_ticket = $('#newTicket');
    if (typeof new_ticket != 'undefined') {
        new_ticket.focus();
    }

    if ($('#open_newTicket').val() == 1){
        var ticket_brand = $('#ticket_brand').val();
        var ticket_model = $('#ticket_model').val();
        var ticket_version = $('#ticket_version').val();
        var ticket_system  = $('#ticket_system').val();
        var ticket_subsystem  = $('#ticket_subsystem').val();
        var ticket_importance = $('#ticket_importance').val();

        if(ticket_brand != '')
            $('#new_car_form_brand').val(ticket_brand);

        if(ticket_model != '')
            fill_model(ticket_model);

        if(ticket_importance != '')
            $('#new_car_form_importance').val(ticket_importance);
    }
});

    //vacia tbl_similar
    $(document).on('change','#new_car_form_brand'   ,function(){ clear_tbl_similar('brand' ); clear_tbl_repeated('brand' ); });
    $(document).on('change','#new_car_form_model'   ,function(){ clear_tbl_similar('model' ); clear_tbl_repeated('model' ); });
    $(document).on('change','#id_system'            ,function(){ clear_tbl_similar('system'); clear_tbl_repeated('system'); });
    //llena tbl_similar
    $(document).on('change','#ticket_form_subsystem',function(){ fill_tbl_similar(); fill_tbl_repeated(); });
    // $('#btn_search_by_bmv').click(function() {

        // var flt_id = $('#btn_searchbyid').val();

        // if(flt_id != "") {
        //     $('#flt_id').val(flt_id);
        //     $('#findTicketById').submit();
        // }
        // else
            // search_by_bmv();

    // });

    $('#newTicket').click(function() {

        $('#n_id_brand').val( $('#new_car_form_brand').val());
        $('#n_id_model').val( $('#new_car_form_model').val());
        $('#n_id_version').val( $('#new_car_form_version').val());
        $('#n_id_subsystem').val( $('#new_car_form_subsystem').val());
        $('#n_id_importance').val( $('#new_car_form_importance').val());
        $('#n_id_vin').val( $('#new_car_form_vin').val());
        $('#n_id_plateNumber').val( $('#new_car_form_plateNumber').val());

    });

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

    var similar = $( "#txt_similar" ).val();
    $( "#tbl_similar" ).empty();
    $( "#tbl_similar" ).append("<p>"+similar+"</p>");
}

/**
 * vacia tbl_repeated
 * @return AjaxFunction
 */
function clear_tbl_repeated(parent) {

    var similar = $( "#txt_repeated" ).val();
    $( "#tbl_repeated" ).empty();
    $( "#tbl_repeated" ).append("<p>"+similar+"</p>");
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

    $('.ticketRow').click( function() {

       var id         = $(this).find("#ticket_id").val();
       var brand      = $(this).find("#ticket_brand").val();
       var model      = $(this).find("#ticket_model").val();
       var system     = $(this).find("#ticket_system").val();
       var subsystem  = $(this).find("#ticket_subsystem").val();
       var importance = $(this).find("#ticket_importance").val();
       var desc       = $(this).find("#ticket_description").val();
       var sol        = $(this).find("#ticket_solution").val();

        $('#flt_id').empty();
        $('#id_system').val(system);
        $('#new_car_form_model').val('');
        $('#new_car_form_version').val('');
        $('#new_car_form_subsystem').val('');
        $('#new_car_form_importance').val('');

        $('#flt_id').val(id);
        $('#new_car_form_brand').val(brand);
        fill_model(model);
        fill_subsystem(subsystem);
        $('#new_car_form_subsystem').val(subsystem);
        $('#new_car_form_importance').val(importance);


        $('#list_description').text('');
        $('#list_description').text(desc);
        $('#list_solution').text('');
        $('#list_solution').text(sol);

    });

/**
 * De la href del modal que envia al delete, se le cambia el "foo" por el id que queremos borrar
 * @param int user_id
 */
function confirm_delete_ticket_modal(id_ticket) {
    var custom_href = $('.modal-footer').find('a').attr('href');
    custom_href = custom_href.replace('foo', id_ticket);
    $('.modal-footer').find('a').attr('href', custom_href);
}