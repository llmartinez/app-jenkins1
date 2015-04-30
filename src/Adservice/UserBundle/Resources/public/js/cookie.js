
    $(document).ready(function() {

        $(window).load(function(){
            //a traves de la cookie solo mostramos el popup una vez
            //mostrar si no tiene una coockie creada

            //mostrar si ya tiene una coockie creada
            if ($.cookie('visited') != 1){
                $.cookie('visited', '1');
                find_popup();
            }
        });
    });