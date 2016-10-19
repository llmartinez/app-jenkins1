
$(document).ready(function() {

    $('#MainContent').find('.glyphicon-trash').click(function() {
        var partner_id = $(this).data('id');
        confirm_delete_partner_modal(partner_id);
    });

    //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
    $('#flt_country').change(function() {

        var country = $('#flt_country').val();
        var partner = $('#flt_partner').val();
        var catserv = $('#flt_catserv').val();
        if(country == null) var country = '0';
        if(partner == null) var partner = '0';
        if(catserv == null) var catserv = '0';

        var route = 'shop_list';
        var locale = $(document).find("#data_locale").val();
        var url = Routing.generate(route, {_locale: locale, page: 1, country: country, catserv: catserv, partner: partner });

        window.open(url, "_self");
    });
    $('#flt_catserv').change(function() {

        var country = $('#flt_country').val();
        var partner = 0;
        var catserv = $(this).val();
        if(country == null) var country = '0';
        if(catserv == null) var catserv = '0';

        var route = 'shop_list';
        var locale = $(document).find("#data_locale").val();
        var url = Routing.generate(route, {_locale: locale, page: 1, country: country, catserv: catserv, partner: partner });
        window.open(url, "_self");
    });
    $('#flt_partner').change(function() {

        var partner = $('#flt_partner').val();
        var country = $('#flt_country').val();
        var catserv = $('#flt_catserv').val();
        if(partner == null) var partner = '0';
        if(country == null) var country = '0';
        if(catserv == null) var catserv = '0';

        var route = 'shop_list';
        var locale = $(document).find("#data_locale").val();
        var url = Routing.generate(route, {_locale: locale, page: 1, country: country, catserv: catserv, partner: partner });

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

    //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
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