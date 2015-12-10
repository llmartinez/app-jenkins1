
$(document).ready(function() {

    // $('select[id*=endtest_at').addClass('btn-date');
    // $('select[id*=endtest_at').addClass('btn-date');

    $('#slct_typology').val($('select[name*=typology]').val());

    $('#slct_typology').change(function() {
        $('select[name*=typology]').val($('#slct_typology').val());
    });

    $('#adservice_workshopbundle_workshoptype_diagnosis_machines option').each(function() {
        var id = $(this).val();
        var lan = $('#lan_diag_machines input#lan'+id).val();
        var text = $('#adservice_workshopbundle_workshoptype_diagnosis_machines option[value="'+id+'"]').text();
        if(text != '...' && lan != undefined){
            $('#adservice_workshopbundle_workshoptype_diagnosis_machines option[value="'+id+'"]').text(text+' ('+lan+')');
        }
    });
    $('#slct_diagnosis_machine').val($('select[name*=diagnosis_machine]').val());

    $('#slct_diagnosis_machine').change(function() {
        $('select[name*=diagnosis_machine]').val($('#slct_diagnosis_machine').val());
    });

    enable_endtest($('#adservice_workshopbundle_workshoptype_test').is(':checked'));

    // DATE TEST
    $('#adservice_workshopbundle_workshoptype_test').click(function(){

        var checked = $('#adservice_workshopbundle_workshoptype_test').is(':checked');

        if(checked) {
            var d = new Date();
            $('#adservice_workshopbundle_workshoptype_endtest_at_month' ).val(d.getMonth()+2);
            if(d.getMonth()>=11){
                $('#adservice_workshopbundle_workshoptype_endtest_at_month' ).val(1);
                $('#adservice_workshopbundle_workshoptype_endtest_at_year'  ).val(d.getFullYear()+1);
            }
            else{
                $('#adservice_workshopbundle_workshoptype_endtest_at_year'  ).val(d.getFullYear());
            }
            $('#adservice_workshopbundle_workshoptype_endtest_at_day'   ).val(d.getDate());
        }
        else{
            var d = new Date();
            $('#adservice_workshopbundle_workshoptype_endtest_at_month' ).val(d.getMonth()+1);

            $('#adservice_workshopbundle_workshoptype_endtest_at_day'   ).val(d.getDate());
            $('#adservice_workshopbundle_workshoptype_endtest_at_year'  ).val(d.getFullYear());
        }
        enable_endtest(checked);
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

/**
 * De la href del modal que envia al delete, se le cambia el "foo" por el id que queremos borrar
 * @param int user_id
 */
function confirm_delete_workshop_modal(workshop_id){
    var custom_href = $('.modal-footer').find('a').attr('href');
    custom_href = custom_href.replace('foo', workshop_id);
    $('.modal-footer').find('a').attr('href',custom_href);
}

function enable_endtest(bool) {
    if(bool == true) {
        $('#adservice_workshopbundle_workshoptype_endtest_at_month' ).removeAttr("disabled");
        $('#adservice_workshopbundle_workshoptype_endtest_at_day'   ).removeAttr("disabled");
        $('#adservice_workshopbundle_workshoptype_endtest_at_year'  ).removeAttr("disabled");
    }else{
        $('#adservice_workshopbundle_workshoptype_endtest_at_month' ).attr("disabled", "disabled");
        $('#adservice_workshopbundle_workshoptype_endtest_at_day'   ).attr("disabled", "disabled");
        $('#adservice_workshopbundle_workshoptype_endtest_at_year'  ).attr("disabled", "disabled");
    }
}
