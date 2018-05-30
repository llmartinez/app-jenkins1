
$(document).ready(function() {

    var new_ticket = $('#newTicket');
    if (typeof new_ticket != 'undefined') {
        new_ticket.focus();
    }

    if ($('#open_newTicket').val() == 1){
        var new_einatech = $('#new_einatech').val();
        if(new_einatech == 1) {
            popUpEinatech();
        }
        var ticket_system  = $('#ticket_system').val();
        var ticket_subsystem  = $('#ticket_subsystem').val();
        var ticket_importance = $('#ticket_importance').val();
        var ticket_description  = $('#ticket_description').val();

        var car = {
            'brandId': $('#ticket_brand').val(),
            'modelId': $('#ticket_model').val(),
            'versionId': $('#ticket_version').val(),
            'plateNumber': $('#ticket_plateNumber').val(),
            'vin': $('#ticket_vin').val(),
            'motor': $('#ticket_motor').val(),
            'cm3': $('#ticket_displacement').val(),
            'kw': $('#ticket_kw').val(),
            'year': $('#ticket_year').val(),
            'origin': $('#ticket_origin').val(),
            'status': $('#ticket_status').val(),
            'variants': $('#ticket_variants').val()
        };

        fillCar(car);

        if(ticket_importance != '')
            $('#ticket_form_importance').val(ticket_importance);
        
        if(ticket_description != '')
            $('#ticket_form_description').val(ticket_description);
        
        if(ticket_system != '')
            $('#id_system').val(ticket_system);
                
        if(ticket_subsystem != '')
            $('#ticket_form_subsystem').val(ticket_subsystem);

    }

});
function popUpEinatech() {
    var msg_einatech = $('#msg_einatech').val();
    alert(msg_einatech);
}

function normalizeForm(str) {
    str = string_to_slug(str);
    str = str.replace(/\ /g, '');
    str = str.replace(/\-/g, '');
    return str;
}

//vacia tbl_similar
    $(document).on('change','#new_car_form_brand'   ,function(){ clear_tbl_similar('brand' ); clear_tbl_repeated('brand' ); });
    $(document).on('change','#new_car_form_model'   ,function(){ clear_tbl_similar('model' ); clear_tbl_repeated('model' ); });
    $(document).on('change','#id_system'            ,function(){ clear_tbl_similar('system'); clear_tbl_repeated('system'); });
    //llena tbl_similar
    $(document).on('change','#ticket_form_subsystem',function(){ fill_tbl_similar(); fill_tbl_repeated(); });

    $('#newTicket').click(function() {

        $('#n_id_brand').val( $('#filter_car_form_brand').val());
        $('#n_id_model').val( $('#filter_car_form_model').val());
        $('#n_id_version').val( $('#filter_car_form_version').val());
        $('#n_id_year').val( $('#filter_car_form_year').val());
        $('#n_id_motor').val( $('#filter_car_form_motor').val());
        $('#n_id_kw').val( $('#filter_car_form_kW').val());
        $('#n_id_displacement').val( $('#filter_car_form_displacement').val());
        $('#n_id_vin').val( $('#filter_car_form_vin').val());
        $('#n_id_plateNumber').val( $('#filter_car_form_plateNumber').val());
        $('#n_id_subsystem').val( $('#filter_car_form_subsystem').val());
        $('#n_id_importance').val( $('#filter_car_form_importance').val());
        $('#n_id_origin').val( $('#filter_car_form_origin').val());
        $('#n_id_status').val( $('#filter_car_form_status').val());
        $('#n_id_variants').val( $('#filter_car_form_variants').val());
    });

// $('.sendTicket').click(function() {
    //     var model = $('#new_car_form_model').val();

    //     var subsystem = $('#ticket_form_subsystem').val();

    //     if (model == '' || model == "0") {
    //         var error_ticket = $('#error_ticket').val();
    //         var field = $("label[for='new_car_form_model']").text();
    //         alert(error_ticket+ ' ('+field+')');
    //         event.preventDefault();
    //     }
    //     if (subsystem == '' || subsystem == "0" ) {
    //         var error_ticket = $('#error_ticket').val();
    //         var field = $("label[for='new_ticket_form_subsystem']").text();
    //         alert(error_ticket+ ' ('+field+')');
    //         event.preventDefault();
    //     }
// });


$('#new_file_form_file').bind('change', function() {

    var size = this.files[0].size;
    var role = $('#role').val();

    var max_size = '10240000';

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

    $('.img_icon.open').click(function(){
        $(this).data('clicked', true);
    });
    
    $(document).on('change','#ticket_form_importance',function(){
       var importance = $(document).find("#ticket_form_importance").val();
       var textarea_text_default  = $(document).find("#textarea_text_default").val();
       if(importance == 5){
           $(document).find("#ticket_form_description").val(textarea_text_default);
       }
    });
    
    $('.ticketRow').click( function() {

        var is_clicked = $(this).find('.img_icon.open').data('clicked');

        if(is_clicked == true) {
            $(this).find('.img_icon.open').data('clicked', false);
        }else {
            var code_partner  = $(this).find(".workshop_code_partner").val();
            var code_workshop = $(this).find(".workshop_code_workshop").val();
            var name          = $(this).find(".workshop_name").val();
            var tel           = $(this).find(".workshop_tel").val();
            var mail          = $(this).find(".workshop_mail").val();
            var contact       = $(this).find(".workshop_contact").val();

            var id           = $(this).find(".ticket_id").val();
            var id_brand     = $(this).find(".ticket_id_brand").val();
            var id_model     = $(this).find(".ticket_id_model").val();
            var model        = $(this).find(".ticket_model").val();
            var id_version   = $(this).find(".ticket_id_version").val();
            var version      = $(this).find(".ticket_version").val();
            var id_system    = $(this).find(".ticket_id_system").val();
            var id_subsystem = $(this).find(".ticket_id_subsystem").val();
            var subsystem    = $(this).find(".ticket_subsystem").val();
            var desc         = $(this).find(".ticket_description").val();
            var date         = $(this).find(".ticket_date").val();
            var sol          = $(this).find(".ticket_solution").val();

            var dis_url = $( "#dis-url" ).val();
            var vts_url = $( "#vts-url" ).val();

            $('#flt_id').empty();
            $('#flt_id').val(id);

            $('#w_idpartner').val(code_partner);
            $('#w_id'       ).val(code_workshop);
            $('#w_name'     ).val(name);
            $('#w_tel'      ).val(tel);
            $('#w_email'    ).val(mail);
            $('#w_contact'  ).val(contact);

            $('#filter_car_form_model'     ).empty();
            $('#filter_car_form_model'     ).append('<option value="'+id_model     +'" selected>'+model+'</option>');
            $('#filter_car_form_version'   ).empty();
            $('#filter_car_form_version'   ).append('<option value="'+id_version   +'" selected>'+version+'</option>');
            $('#id_system').val(id_system);
            $('#filter_car_form_subsystem' ).empty();
            $('#filter_car_form_subsystem' ).append('<option value="'+id_subsystem +'" selected>'+subsystem+'</option>');

            $('#list_date').text('');
            $('#list_date').text(date);
            $('#list_description').text('');
            $('#list_description').text(desc);
            $('#list_solution').text('');
            $('#list_solution').text(sol);

            var car = {
                'brandId': $(this).find('.ticket_id_brand').val(),
                'modelId': $(this).find('.ticket_id_model').val(),
                'versionId': $(this).find('.ticket_id_version').val(),
                'plateNumber': $(this).find('.ticket_plateNumber').val(),
                'vin': $(this).find('.ticket_vin').val(),
                'motor': $(this).find('.ticket_motor').val(),
                'cm3': $(this).find('.ticket_displacement').val(),
                'kw': $(this).find('.ticket_kw').val(),
                'year': $(this).find('.ticket_year').val(),
                'origin': $(this).find('.ticket_origin').val(),
                'status': $(this).find('.ticket_status').val(),
                'variants': $(this).find('.ticket_variants').val()
            };

            fillCar(car);

            if(id_version == '0'){
                $( "#dis" ).attr("href", dis_url+'/model-'+id_model);
                $( "#vts" ).attr("href", vts_url+'/'+id_brand+'/'+id_model);
            }else{
                $( "#dis" ).attr("href", dis_url+'/'+id_version);
                $( "#vts" ).attr("href", vts_url+'/'+id_brand+'/'+id_model+'/'+id_version);
            }
        }
    });

$("#id_system").on('change', function() {
    fill_subsystem();
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