
/**
 * De la href del modal que envia al delete, se le cambia el "foo" por el id que queremos borrar
 * @param int user_id
 */
function confirm_delete_popup_modal(id){
    var custom_href = $('.modal-footer').find('a').attr('href');
    custom_href = custom_href.replace('foo', id);
    $('.modal-footer').find('a').attr('href',custom_href);
}

	$(document).ready(function() {

	    $('#logout').click(function() {
	        if ($.cookie('visited') == 1){
	            $.cookie('visited', '0');
	        }
	    });

        $('#MainContent').find('.delete_popup').click(function() {
            var popup_id = $(this).data('id');
            confirm_delete_popup_modal(popup_id);
        });

        //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
        $('#flt_country').click(function() {

            var country = $('#flt_country').val();

            if(country == null) country = 'none';

            var route = 'regions_from_country';
            var locale = $(document).find("#data_locale").val();
            var url = Routing.generate(route, {_locale: locale, page: 1, country: country });

            window.open(url, "_self");
        });
    });
