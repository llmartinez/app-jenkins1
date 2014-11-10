
$(document).ready(function() {

    //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
    $('#flt_country').click(function() {

            var route   = $('#route').val();
            var country = $('#flt_country').val();
            if(country == null) country = 'none';

            var locale = $(document).find("#data_locale").val();
            var url = Routing.generate(route, {_locale: locale, page: 1, country: country });

        window.open(url, "_self");
    });
});
