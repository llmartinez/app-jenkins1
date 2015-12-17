
//Funciones para el autorellenado de los combos de campos de coches de los formularios

    $(document).ready(function() {

        var status = $('#status').val();
        $("#flt_status").val(status);

        //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
        $('#flt_partner_s').change(function() {

            var partner = $('#flt_partner_s').val();
            if(partner == null) partner = 'none';

            var status  = $('#flt_status').val();
            if(status  == null) status  = 0;

            var route = 'shopOrder_listShops';
            var locale = $(document).find("#data_locale").val();
            var url = Routing.generate(route, {_locale: locale, page: 1, partner: partner, status: status  });

            window.open(url, "_self");
        });

        //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
        $('#flt_partner').change(function() {

            var partner = $('#flt_partner').val();
            if(partner == null) partner = 'none';

            var status  = $('#flt_status').val();
            if(status  == null) status  = 0;

            var route = 'workshopOrder_listWorkshops';
            var locale = $(document).find("#data_locale").val();
            var url = Routing.generate(route, {_locale: locale, page: 1, partner: partner, status: status  });

            window.open(url, "_self");
        });

        //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
        $('#flt_country').change(function() {

            var country = $('#flt_country').val();
            var option = $('#option').val();

            if(country == null) country = '0';
            if(option == null) option = 'workshop_pending';

            var route = $('#route').val();
            var locale = $(document).find("#data_locale").val();
            var url = Routing.generate(route, {_locale: locale, page: 1, option: option, country: country});

            window.open(url, "_self");
        });

        $('#flt_status').change(function() {

            var status  = $('#flt_status').val();
            if(status  == null) status  = 0;

            var partner = $('#flt_partner').val();
            if(partner == null) partner = 'none';

            var route = 'workshopOrder_listWorkshops';
            var locale = $(document).find("#data_locale").val();
            var url = Routing.generate(route, {_locale: locale, page: 1, partner: partner, status: status });

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