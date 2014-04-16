/**
 * Funcion que rellena (populate) el combo de las regiones segun el país seleccionado por el usuario
 * @param {url de tipo {{ path('mi_path') }}} url_ajax
 */
function populate_region(url_ajax, region){
   var id_country = $('form').find('select[name*=country]').val();

   $.ajax({
        type        : "POST",
        url         : url_ajax,
        data        : {id_country : id_country},
        dataType    : "json",
        success : function(data) {
            // Limpiamos y llenamos el combo con las opciones del json
            $('form').find('select[name*=region]').empty();
            $.each(data, function(idx, elm) {

                if(elm.region == region) $('form').find('select[name*=region]').append("<option value="+elm.id+" selected>"+elm.region+"</option>");
                else                     $('form').find('select[name*=region]').append("<option value="+elm.id+">"+elm.region+"</option>");
            });
        },
        error : function(){
            console.log("Error al cargar las regiones...");
        }
    });
}

/**
 * Funcion que rellena (populate) el combo de las regiones segun el país seleccionado por el usuario
 * @param {url de tipo {{ path('mi_path') }}} url_ajax_partner
 */
function populate_shop(url_ajax_partner, shop){
   var id_partner = $('form').find('select[name*=partner]').val();

   $.ajax({
        type        : "POST",
        url         : url_ajax_partner,
        data        : {id_partner : id_partner},
        dataType    : "json",
        success : function(data) {
            // Limpiamos y llenamos el combo con las opciones del json
            $('form').find('select#workshopOrder_newOrder_shop').empty();
            $.each(data, function(idx, elm) {

                if(elm.shop == shop) $('form').find('select#workshopOrder_newOrder_shop').append("<option value="+elm.id+" selected>"+elm.shop+"</option>");
                else                     $('form').find('select#workshopOrder_newOrder_shop').append("<option value="+elm.id+">"+elm.shop+"</option>");
            });
        },
        error : function(){
            console.log("Error al cargar las tiendas...");
        }
    });

}
