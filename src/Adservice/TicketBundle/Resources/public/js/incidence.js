/**
 * Rellena (fill) el combo de los tickets segun la opcion seleccionada por el usuario
 * @param {url de tipo {{ path('mi_path') }}} url_ajax
 */
function fill_incidences(url_ajax) {
    
    var option = $('select[id=slct_historyIncidences]').val();
    $.ajax({
        type: "POST",
        url: url_ajax,
        data: {option: option},
        dataType: "json",
        success: function(data) {
            // Limpiamos y llenamos el combo con las opciones del json
            $('#incidenceBody').empty();
            if(data.length==0){
                $('#incidenceBody').append("<th>You don't have any incidence..</th>");
            }
            $.each(data, function(idx, elm) {
                var route = $('#route').val(); 
                route = route.replace("PLACEHOLDER", elm.id );
                $('#incidenceBody').append("<tr><td><a class='btn btn-primary pull-right' href='" + route + "'>Ver</a>"
                                            + "<b style='color:black'>#" + elm.id + ": </b>"
                                            + elm.title
                                            + "Last Modification: " + elm.date + "(" + elm.status + ")</tr>");
            }); 
           
        },
        error: function() {
            console.log("Error al cargar incidencias...");
        }
    });
} 