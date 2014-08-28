
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

            var select = document.querySelector('#flt_country');
            var data   = select.dataset;
            var url    = data.url;

            url = url.replace("plc_page", 1);
            url = url.replace("plc_country", country);

            window.open(url, "_self");
        });
    });
