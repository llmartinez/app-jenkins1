
//Funciones para el autorellenado de los combos de campos de coches de los formularios

    $(document).ready(function() {
        $('#MainContent').find('.glyphicon-trash').click(function() {
            var partner_id = $(this).data('id');
            confirm_delete_partner_modal(partner_id);
        });

        //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
        $('#flt_partner').click(function() {

            var partner = $('#flt_partner').val();

            if(partner == null) partner = 'none';

            var select = document.querySelector('#flt_partner');
            var data   = select.dataset;
            var url    = data.url;

            url = url.replace("plc_page", 1);
            url = url.replace("plc_partner", partner);

            window.open(url, "_self");
        });
    });