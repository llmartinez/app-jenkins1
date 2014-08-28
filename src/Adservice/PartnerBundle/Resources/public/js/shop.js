
  $(document).ready(function() {

            $('#MainContent').find('.glyphicon-trash').click(function() {
                var partner_id = $(this).data('id');
                confirm_delete_partner_modal(partner_id);
            });

            //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
            $('#flt_country').click(function() {

                var country = $('#flt_country').val();

                if(country == null) country = 'none';

                var select = document.querySelector('#new_car_form_brand');
                var data   = select.dataset;
                var url    = data.url;

                url = url.replace("plc_page", 1);
                url = url.replace("plc_country", country);

                window.open(url, "_self");
            });

            //util.js
            table_filter();
  });