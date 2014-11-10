
  $(document).ready(function() {

            $('#MainContent').find('.glyphicon-trash').click(function() {
                var partner_id = $(this).data('id');
                confirm_delete_partner_modal(partner_id);
            });

            //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
            $('#flt_country').click(function() {

                var country = $('#flt_country').val();
                if(country == null) country = 'none';

                var route = 'shop_list';
                var locale = $(document).find("#data_locale").val();
                var url = Routing.generate(route, {_locale: locale, page: 1, country: country });

                window.open(url, "_self");
            });
  });