
    $(document).ready(function() {
    //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
        $('#flt_catserv').change(function() {

            var route   = $('#route').val();
            var catserv = $('#flt_catserv').val();
            var country = $('#flt_country').val();
            if(catserv == null) catserv = 'none';
            if(country == null) country = 'none';

            var locale = $(document).find("#data_locale").val();
            var url = Routing.generate(route, {_locale: locale, page: 1, country: country, catserv: catserv });

            window.open(url, "_self");
        });
        $('#flt_country').change(function() {

            var route   = $('#route').val();
            var country = $('#flt_country').val();
            var catserv = $('#flt_catserv').val();
            if(country == null) country = 'none';
            if(catserv == null) catserv = 'none';

            var locale = $(document).find("#data_locale").val();
            var url = Routing.generate(route, {_locale: locale, page: 1, country: country, catserv: catserv });

            window.open(url, "_self");
        });
    });