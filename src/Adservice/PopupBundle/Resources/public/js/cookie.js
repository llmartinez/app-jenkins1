
/**
 * De la href del modal que envia al delete, se le cambia el "foo" por el id que queremos borrar
 * @param int user_id
 */

	$(document).ready(function() {

	    $('#logout').click(function() {
	        if ($.cookie('visited') == 1){
	            $.cookie('visited', '0');
	        }
	    });
    });
