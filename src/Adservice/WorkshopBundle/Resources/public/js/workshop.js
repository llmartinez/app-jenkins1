/**
 * De la href del modal que envia al delete, se le cambia el "foo" por el id que queremos borrar
 * @param int user_id
 */
function confirm_delete_workshop_modal(workshop_id){
    var custom_href = $('.modal-footer').find('a').attr('href');
    custom_href = custom_href.replace('foo', workshop_id);
    $('.modal-footer').find('a').attr('href',custom_href);
}
