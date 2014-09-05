
    $(document).ready(function() {

        $(window).load(function(){
            //a traves de la cookie solo mostramos el popup una vez
            if ($.cookie('visited') != 1){
                $.cookie('visited', '1');
                find_popup();
            }
        });
    });