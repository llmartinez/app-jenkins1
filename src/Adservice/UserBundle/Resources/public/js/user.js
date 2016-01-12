
    $(document).ready(function() {
        $( "input[id*='_password_password1']" ).addClass( "form-control" );
        $( "input[id*='_password_password2']" ).addClass( "form-control" );
        $( "label[for*='_password_password1']" ).text($('#pass_field1').val()+' *');
        $( "label[for*='_password_password2']" ).text($('#pass_field2').val()+' *');

        $('#slct_role').change(function() {
            var role = $(this).val();
            var country = $('#slct_country').val();
            var route = 'user_list';
            var locale = $(document).find("#data_locale").val();
            var url = Routing.generate(route, {_locale: locale, page: 1, country: country, option: role });
            window.open(url, "_self");
        });
        $('#slct_country').change(function() {
            var country = $(this).val();
            var role = $('#slct_role').val()
            var route = 'user_list';
            var locale = $(document).find("#data_locale").val();
            var url = Routing.generate(route, {_locale: locale, page: 1, country: country, option: role });
            window.open(url, "_self");
        });

        $('#btn_create').click(function() {
            check_password();

            if ( isNaN($("input[id*='number_']").val())) {
                $("input[id*='number_']").css('border-color','#FF0000');
                alert($("#isNaN").val());
                return false;
            }
        });
    });

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