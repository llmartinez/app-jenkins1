
$(document).ready(function() {

    //REDIRIGEN AL LISTADO DE COCHES

    $('#btn_search_field').click(function(){

        var matricula = $('#flt_search_field').val();
        if(matricula === "") matricula = null;

        redirect(matricula);
    });

    $('#btn_clear').click(function(){

        redirect(null);
    });

    function redirect(matricula){

        var url = Routing.generate('car_list', {'page': 1, 'matricula': matricula });
        window.open(url, "_self");
    }

});
