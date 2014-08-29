
$(document).ready(function() {
    $('select[id*=_at_date_').addClass('btn-date');
    $('select[id*=_at_time_').addClass('btn-date');

    $('#slct_typology').val($('select[name*=typology]').val());

    $('#slct_typology').change(function() {
        $('select[name*=typology]').val($('#slct_typology').val());
    });

    $('#slct_diagnosis_machine').val($('select[name*=diagnosis_machine]').val());

    $('#slct_diagnosis_machine').change(function() {
        $('select[name*=diagnosis_machine]').val($('#slct_diagnosis_machine').val());
    });

    // DATE TEST
    $('#adservice_workshopbundle_workshoptype_test').change(function(){

        if($('#adservice_workshopbundle_workshoptype_test').is(':checked')) {
            var d = new Date();
            $('#adservice_workshopbundle_workshoptype_endtest_at_date_month' ).val(d.getMonth()+1);
            $('#adservice_workshopbundle_workshoptype_endtest_at_date_day'   ).val(d.getDay());
            $('#adservice_workshopbundle_workshoptype_endtest_at_date_year'  ).val(d.getFullYear());
            $('#adservice_workshopbundle_workshoptype_endtest_at_time_hour'  ).val(d.getHours());
            $('#adservice_workshopbundle_workshoptype_endtest_at_time_minute').val(d.getMinutes());
        }
        else{
            var d = new Date();
            $('#adservice_workshopbundle_workshoptype_endtest_at_date_month' ).val(d.getMonth());
            $('#adservice_workshopbundle_workshoptype_endtest_at_date_day'   ).val(d.getDay());
            $('#adservice_workshopbundle_workshoptype_endtest_at_date_year'  ).val(d.getFullYear());
            $('#adservice_workshopbundle_workshoptype_endtest_at_time_hour'  ).val(d.getHours());
            $('#adservice_workshopbundle_workshoptype_endtest_at_time_minute').val(d.getMinutes());
        }
    });
});

/**
 * De la href del modal que envia al delete, se le cambia el "foo" por el id que queremos borrar
 * @param int user_id
 */
function confirm_delete_workshop_modal(workshop_id){
    var custom_href = $('.modal-footer').find('a').attr('href');
    custom_href = custom_href.replace('foo', workshop_id);
    $('.modal-footer').find('a').attr('href',custom_href);
}
