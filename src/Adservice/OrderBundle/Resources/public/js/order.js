
//Funciones para el autorellenado de los combos de campos de coches de los formularios

    $(document).ready(function() {

        //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
        $('#flt_partner_s').change(function() {

            var partner = $('#flt_partner_s').val();

            if(partner == null) partner = 'none';

            var route = 'shopOrder_listShops';
            var locale = $(document).find("#data_locale").val();
            var url = Routing.generate(route, {_locale: locale, page: 1, partner: partner });

            window.open(url, "_self");
        });

        //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
        $('#flt_partner_w').change(function() {

            var partner = $('#flt_partner_w').val();

            if(partner == null) partner = 'none';

            var route = 'workshopOrder_listWorkshops';
            var locale = $(document).find("#data_locale").val();
            var url = Routing.generate(route, {_locale: locale, page: 1, partner: partner });

            window.open(url, "_self");
        });

        //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
        $('#flt_country').change(function() {

            var country = $('#flt_country').val();
            var option = $('#option').val();

            if(country == null) country = 'none';
            if(option == null) option = 'workshop_pending';

            var route = $('#route').val();
            var locale = $(document).find("#data_locale").val();
            var url = Routing.generate(route, {_locale: locale, page: 1, option: option, country: country});

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
    });