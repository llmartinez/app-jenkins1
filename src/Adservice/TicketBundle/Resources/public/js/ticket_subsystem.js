
$(document).ready(function() {
    // Limpiamos el combo de subsystem del formulario y cargamos subsistemas

    var ticket_system  = $('#ticket_system').val();
    var ticket_subsystem  = $('#ticket_subsystem').val();

    if(ticket_system != '') {
        $('#id_system').val(ticket_system);
    	fillSubsystem(ticket_subsystem);
    }
});

//cambiar model en funcion de brand
$(document).on('change','#id_system',function(){ fillSubsystem(); });

function fillSubsystem(subsystem) {

    var id_system = $('form[id=contact]').find('select[id=id_system]').val();

    if(id_system == '0') $("#ticket_system").val();

    var route  = 'ticket_system';
    var locale = $(document).find("#data_locale").val();

    //Valor del subsistema del ticket al cerrar
    var id_subsystem = ($('select[id*=_subsystem]').val());
    if (id_subsystem == null) $('select[id*=_subsystem]').empty();

    $.ajax({
        type: "POST",
        url: Routing.generate(route, {_locale: locale }),
        data: {id_system: id_system},
        dataType: "json",
        beforeSend: function(){ $("body").css("cursor", "progress"); },
        complete: function(){ $("body").css("cursor", "default"); },
        success: function(data) {
            if (data['error'] != "No hay coincidencias") {

                // Limpiamos y llenamos el combo con las opciones del json
                $('select[id*=_subsystem]').empty();

                //Primer campo vacío
                $('form[id=contact]').find('select[id*=_subsystem]').append("<option></option>");

                $.each(data, function(idx, elm) {
                    if (elm.id == id_subsystem || elm.id == subsystem)
                        $('form[id=contact]').find('select[id*=_subsystem]').append("<option value=" + elm.id + " selected>" + elm.name + "</option>");
                    else
                        $('form[id=contact]').find('select[id*=_subsystem]').append("<option value=" + elm.id + ">" + elm.name + "</option>");
                });

                var subsys_id = $('#subsystem_id').val();
                if(subsys_id != undefined && subsys_id != '0' ) $('#edit_ticket_form_subsystem').val(subsys_id);
            }else{
                $('select[id*=_subsystem]').empty();
            }
        },
        error: function() {
            console.log("Error al cargar subsistemas...");
        }
    });
}