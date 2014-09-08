
  $(document).ready(function() {
  //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
        $('#flt_country').click(function() {

            var country = $('#flt_country').val();

            if(country == null) country = 'none';

            var select = document.querySelector('#flt_country');
            var data   = select.dataset;
            var url    = data.url;

            url = url.replace("plc_page", 1);
            url = url.replace("plc_country", country);

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

