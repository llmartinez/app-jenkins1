
  $(document).ready(function() {
  //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
        $('#flt_country').change(function() {

            var country = $('#flt_country').val();
            if(country == null) country = '0';

            var route = 'partner_list';
            var locale = $(document).find("#data_locale").val();
            var url = Routing.generate(route, {_locale: locale, page: 1, country: country });

            window.open(url, "_self");
        });

        $('#btn_create').click(function() {
            $("input[id*='number_']").each(function() {
                if ( isNaN($(this).val())) {
                    $(this).css('border-color','#FF0000');
                    alert($("#isNaN").val());
                    event.preventDefault();
                }else{
                    $(this).css('border-color','#ccc');
                }
            });
        });
        $('#btn_edit').click(function() {
            $("input[id*='number_']").each(function() {
                if ( isNaN($(this).val())) {
                    $(this).css('border-color','#FF0000');
                    alert($("#isNaN").val());
                    event.preventDefault();
                }else{
                    $(this).css('border-color','#ccc');
                }
            });
        });

        //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
        $('#btn_search_field').click(function() {
                
                var route   = $('#route').val();
                var term = $('#flt_search_term').val();
                if(term == null || term == "") term = '0';

                var field = $('#flt_search_field').val();
                if(field == null || field == "") field = '0';

                var country = $('#flt_country').val();
                if(country == null || country == "") country = '0';

                var partner = $('#flt_partner').val();
                if(partner == null || partner == "") partner = '0';

                var status = $('#flt_status').val();
                if(status == null || status == "") status = '0';

                var locale = $(document).find("#data_locale").val();
                var url = Routing.generate(route, {_locale: locale, page: 1, w_idpartner: '0', w_id: '0', country: country, partner: partner, status: status, term: term, field: field });
                
                window.open(url, "_self");
        });
  });

/**
 * De la href del modal que envia al delete, se le cambia el "foo" por el id que queremos borrar
 * @param int user_id
 */
function confirm_delete_partner_modal(partner_id) {
    var custom_href = $('.modal-footer').find('a').attr('href');
    custom_href = custom_href.replace('foo', partner_id);
    $('.modal-footer').find('a').attr('href', custom_href);
}

/**
 * Funcion que rellena (populate) el combo de las provincias segun la comunidad autonoma seleccionada por el usuario
 * @param {url de tipo {{ path('mi_path') }}} url_ajax
 */
function populate_province(url_ajax){
    var id_region = $('form[name=adservice_partnerbundle_partnertype]').find('select[name*=region]').val();

    $.ajax({
        type        : "POST",
        url         : url_ajax,
        data        : {id_region : id_region},
        dataType    : "json",
        success : function(data) {
            // Limpiamos y llenamos el combo con las opciones del json
            $('form[name=adservice_partnerbundle_partnertype]').find('select[name*=province]').empty();
            $.each(data, function(idx, elm) {
               $('form[name=adservice_partnerbundle_partnertype]').find('select[name*=province]').append("<option value="+elm.id+">"+elm.province+"</option>");
           });
       },
       error : function(){
         console.log("Error al cargar las provincias...");
       }
   });
}

// function populate_province(url_ajax, form_type){
//     var id_region = $('form[name='+form_type+']').find('select[name*=region]').val();

//     $.ajax({
//         type        : "POST",
//         url         : url_ajax,
//         data        : {id_region : id_region},
//         dataType    : "json",
//         success : function(data) {
//             // Limpiamos y llenamos el combo con las opciones del json
//             $('form[name='+form_type+']').find('select[name*=province]').empty();
//             $.each(data, function(idx, elm) {
//                $('form[name='+form_type+']').find('select[name*=province]').append("<option value="+elm.id+">"+elm.province+"</option>");
//            });
//        },
//        error : function(){
//          console.log("Error al cargar las provincias...");
//        }
//    });
// }

