/**
 * Funcion que rellena (populate) el combo de las provincias segun la comunidad autonoma seleccionada por el usuario
 * @param {url de tipo {{ path('mi_path') }}} url_ajax
 */
function populate_province(url_ajax){
    var id_region = $('form[name=adservice_userbundle_usertype]').find('select[name*=region]').val();

    $.ajax({ 
        type        : "POST",
        url         : url_ajax,
        data        : {id_region : id_region},
        dataType    : "json",
        success : function(data) {
            // Limpiamos y llenamos el combo con las opciones del json
            $('form[name=adservice_userbundle_usertype]').find('select[name*=province]').empty();
            $.each(data, function(idx, elm) {
               $('form[name=adservice_userbundle_usertype]').find('select[name*=province]').append("<option value="+elm.id+">"+elm.province+"</option>");
           });     
       },
       error : function(){
         console.log("Error al cargar las provincias...");  
       }
   });
}