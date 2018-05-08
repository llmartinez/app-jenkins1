function initializeModalCar(title, subtitle, closeText)
{
    closeText = closeText || 'close';

    var modal = '<div class="modal fade" id="modal_car" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">';
    modal += '<div class="modal-dialog modal-dialog-centered" role="document">';
    modal += '<div class="modal-content">';
    modal += '<div class="modal-header">';
    modal += '<h5 class="modal-title" id="exampleModalLongTitle">'+title+'</h5>';
    modal += '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
    modal += '</div>';
    modal += '<div class="modal-body">';
    modal += '<h5>'+subtitle+'</h5>';
    modal += '<div id="modal_webservice_select_options"></div>';
    modal += '<div class="modal-footer">';
    modal += '<button type="button" class="btn btn-secondary" data-dismiss="modal">'+closeText+'</button>';
    modal += '</div></div></div></div>';

    if ($('#modal_car').length <= 0) {
        $('body').append(modal);
    }
}