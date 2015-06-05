
  $(document).ready(function() {

            $('#MainContent').find('.glyphicon-trash').click(function() {
                var partner_id = $(this).data('id');
                confirm_delete_partner_modal(partner_id);
            });

            //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
            $('#flt_country').change(function() {

                var country = $('#flt_country').val();
                var partner = $('#flt_partner').val();
                if(country == null) var country = 'none';
                if(partner == null) var partner = 'none';

                var route = 'shop_list';
                var locale = $(document).find("#data_locale").val();
                var url = Routing.generate(route, {_locale: locale, page: 1, country: country, partner: partner });

                window.open(url, "_self");
            });
            $('#flt_partner').change(function() {

                var partner = $('#flt_partner').val();
                var country = $('#flt_country').val();
                if(partner == null) var partner = 'none';
                if(country == null) var country = 'none';

                var route = 'shop_list';
                var locale = $(document).find("#data_locale").val();
                var url = Routing.generate(route, {_locale: locale, page: 1, country: country, partner: partner });

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