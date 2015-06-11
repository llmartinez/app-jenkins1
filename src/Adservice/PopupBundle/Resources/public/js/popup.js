
/**
 * De la href del modal que envia al delete, se le cambia el "foo" por el id que queremos borrar
 * @param int user_id
 */
// function confirm_delete_popup_modal(id){
//     var custom_href = $('.modal-footer').find('a').attr('href');
//     custom_href = custom_href.replace('foo', id);
//     $('.modal-footer').find('a').attr('href',custom_href);
// }

	$(document).ready(function() {

        $('select[id*=_at_date_').addClass('btn-date');
        $('select[id*=_at_time_').addClass('btn-date');

        // SET DE FECHA INICIAL
        var d = new Date();
        if ($('select[id*=_at_date_year').val() < d.getFullYear() ) {
            $('select[id*=_at_date_day'  ).val(d.getDate());
            $('select[id*=_at_date_month').val(d.getMonth()+1);
            $('select[id*=_at_date_year' ).val(d.getFullYear());
            $('select[id*=_startdate_at_time_hour' ).val(d.getHours());
            $('select[id*=_startdate_at_time_minute' ).val(d.getMinutes());
            $('select[id*=_enddate_at_time_hour' ).val(23);
            $('select[id*=_enddate_at_time_minute' ).val(59);
        };

        // $('#MainContent').find('.delete_popup').click(function() {
        //     var popup_id = $(this).data('id');
        //     confirm_delete_popup_modal(popup_id);
        // });

        //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
        $('#flt_country').change(function() {

            var country = $('#flt_country').val();

            if(country == null) country = 'none';

            var route = 'popup_list';
            var locale = $(document).find("#data_locale").val();
            var url = Routing.generate(route, {_locale: locale, page: 1, country: country });

            window.open(url, "_self");
        });
    });
