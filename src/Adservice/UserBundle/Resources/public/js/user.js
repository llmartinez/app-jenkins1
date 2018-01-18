
    $(document).ready(function() {
        $( "input[id*='_password_password1']" ).addClass( "form-control" );
        $( "input[id*='_password_password2']" ).addClass( "form-control" );
        $( "label[for*='_password_password1']" ).text($('#pass_field1').val()+' *');
        $( "label[for*='_password_password2']" ).text($('#pass_field2').val()+' *');
        var partner =$('#partner_id').val();
        if (partner != ""){
            populate_user_partner(partner);
        }
        
        if ($('.active').text() == 'Nuevo Usuario' || $('.active').text() == 'New User' || $('.active').text() == 'Nouvel utilisateur' || $('.active').text() == 'Novo Utilizador')
        {
            $('#commercial_type_shop').empty();

            if($('#commercial_type_category_service').val() != undefined)
            {
                $('#commercial_type_partner').empty();
            }
        }          

        $('#slct_role').change(function() {
            var role = $(this).val();
            var country = $('#slct_country').val();
            var catserv = $('#slct_catserv').val();
            var route = 'user_list';
            var locale = $(document).find("#data_locale").val();
            var url = Routing.generate(route, {_locale: locale, page: 1, country: country, catserv: catserv, option: role });
            window.open(url, "_self");
        });
        $('#slct_country').change(function() {
            var country = $(this).val();
            var role = $('#slct_role').val();
            var catserv = $('#slct_catserv').val();
            var route = 'user_list';
            var locale = $(document).find("#data_locale").val();
            var url = Routing.generate(route, {_locale: locale, page: 1, country: country, catserv: catserv, option: role });
            window.open(url, "_self");
        });
        $('#slct_catserv').change(function() {
            var country = $('#slct_country').val();
            var role = $('#slct_role').val();
            var catserv = $(this).val();
            var route = 'user_list';
            var locale = $(document).find("#data_locale").val();
            var url = Routing.generate(route, {_locale: locale, page: 1, country: country, catserv: catserv, option: role });
            window.open(url, "_self");
        });
        $('#btn_search_field').click(function() {
            var route = 'user_list';
            var role = $('#slct_role').val();
            var country = $('#slct_country').val();
            var catserv = $('#slct_catserv').val();
            var term = $('#flt_search_term').val();
            var field = $('#flt_search_field').val();

            if(role == null || role == "") role = '0';
            if(country == null || country == "") country = '0';
            if(catserv == null || catserv == "") catserv = '0';
            if(term == null || term == "") term = '0';
            if(field == null || field == "") field = '0';

            var locale = $(document).find("#data_locale").val();
            var url = Routing.generate(route, {_locale: locale, page: 1, country: country, catserv: catserv, option: role, term: term, field: field });

                window.open(url, "_self");
        });

        //USER_PARTNER_LIST
        $('#slct_role_pt').change(function() {
            var role = $(this).val();
            var country = $('#slct_country_pt').val();
            var route = 'user_partner_list';
            var locale = $(document).find("#data_locale").val();
            var url = Routing.generate(route, {_locale: locale, page: 1, country: country, option: role });
            window.open(url, "_self");
        });
        $('#slct_country_pt').change(function() {
            var country = $(this).val();
            var role = $('#slct_role_pt').val();
            var route = 'user_partner_list';
            var locale = $(document).find("#data_locale").val();
            var url = Routing.generate(route, {_locale: locale, page: 1, country: country, option: role });
            window.open(url, "_self");
        });
        $('#btn_search_field_pt').click(function() {
            var route = 'user_partner_list';
            var role = $('#slct_role_pt').val();
            var country = $('#slct_country_pt').val();
            var term = $('#flt_search_term').val();
            var field = $('#flt_search_field').val();

            if(role == null || role == "") role = '0';
            if(country == null || country == "") country = '0';
            if(term == null || term == "") term = '0';
            if(field == null || field == "") field = '0';

            var locale = $(document).find("#data_locale").val();
            var url = Routing.generate(route, {_locale: locale, page: 1, country: country, option: role, term: term, field: field });

                window.open(url, "_self");
        });
        $('#commercial_type_partner').change(function() {
            var id_shop = $(this).val();
            populate_shop(id_shop);
        });

        $('#btn_create').click(function() {
            check_password();
            if ( isNaN($("input[id*='number_']").val())) {
                $("input[id*='number_']").css('border-color','#FF0000');
                alert($("#isNaN").val());
                return false;
            }
            return false;
        });
    });
/**
 * Funcion que rellena (populate) el combo de las socios segun la CatServ seleccionada por el usuario
 */
function populate_user_partner(partner){
    var id_catserv = $('form').find('select[name*=category_service]').val();
    if (id_catserv == undefined) { id_catserv = $('#id_catserv').val(); }

    if (id_catserv != undefined && id_catserv != "") { 
        var route  = 'partners_from_catserv';
        var locale = $(document).find("#data_locale").val();

        $('form').find('select[id$=_partner]').empty();

        $.ajax({
            type        : "POST",
            url         : Routing.generate(route, {_locale: locale }),
            data        : {id_catserv : id_catserv},
            dataType    : "json",
            beforeSend: function(){ $("body").css("cursor", "progress"); },
            complete: function(){ $("body").css("cursor", "default"); },
            success : function(data) {
                // Limpiamos y llenamos el combo con las opciones del json
                if (data['error'] != "No hay coincidencias") {

                    $('form').find('select[id$=e_partner]').append("<option value=></option>");
                    $.each(data, function(idx, elm) {
                        $('form').find('select[id$=e_partner]').append("<option value="+elm.id+">"+elm.name+"</option>");
                    });
                    $('form').find('select[id$=e_partner]').val(partner);
                   
                }
            },
            error : function(){
                console.log("Error al cargar los socios...");
            }
        });
    }else{

        if ($('.active').text() == 'Nuevo Usuario' || $('.active').text() == 'New User' || $('.active').text() == 'Nouvel utilisateur' || $('.active').text() == 'Novo Utilizador')
        {
            if($('#id_catserv').val() != undefined)
            {
                $('form').find('select[id$=_partner]').empty();
            }
        }   
    }
}
/**
 * Rellena (populate) el combo de las provincias segun la comunidad autonoma seleccionada por el usuario
 * @param {url de tipo {{ path('mi_path') }}} url_ajax
 */
function populate_province(url_ajax, form_type) {
    var id_region = $('form[name='+form_type+']').find('select[name*=region]').val();

    $.ajax({
        type: "POST",
        url: url_ajax,
        data: {id_region: id_region},
        dataType: "json",
        success: function(data) {
            // Limpiamos y llenamos el combo con las opciones del json
            $('form[name='+form_type+']').find('select[name*=province]').empty();
            $.each(data, function(idx, elm) {
                $('form[name='+form_type+']').find('select[name*=province]').append("<option value=" + elm.id + ">" + elm.province + "</option>");
            });
        },
        error: function() {
            console.log("Error al cargar las provincias...");
        }
    });
}

/**
 * De la href del modal que envia al delete, se le cambia el "foo" por el id que queremos borrar
 * @param int user_id
 */
function confirm_delete_user_modal(user_id) {
    var custom_href = $('.modal-footer').find('a').attr('href');
    custom_href = custom_href.replace('foo', user_id);
    $('.modal-footer').find('a').attr('href', custom_href);
}

/**
 * Busca si se tiene que mostrar un POP UP nada mas entrar en la aplicación
 * Si es asi, lanza un modal con la informacion del popup
 */
function find_popup() {

    // var route  = 'popup_get';
    // var locale = $(document).find("#data_locale").val();

    // $.ajax({
    //     type: "POST",
    //     url: Routing.generate(route, {_locale: locale}),
    //     dataType: "json",
    //     success: function(data) {
    //         //solo mostramos el modal, si tenemos un popup que mostrar
    //         if (data.length > 0) {
    //             $.each(data, function(idx, elm) {
    //                 $('#popup_modal_title').html(elm.name);
    //                 $('#popup_modal_description').html(elm.description);

    //                 $('#myModal').modal('show');
    //             });
    //         }
    //     },
    //     error: function() {
    //         console.log("Error al cargar el popup...");
    //     }
    // });
}