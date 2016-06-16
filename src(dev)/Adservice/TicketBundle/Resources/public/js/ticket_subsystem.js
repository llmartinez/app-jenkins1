
$(document).ready(function() {
    // Limpiamos el combo de subsystem del formulario y cargamos subsistemas

    var ticket_system  = $('#ticket_system').val();
    var ticket_subsystem  = $('#ticket_subsystem').val();

    if(ticket_system != '') {
        $('#id_system').val(ticket_system);
    	fill_subsystem(ticket_subsystem);
    }
    else fill_subsystem();
});

//cambiar model en funcion de brand
$(document).on('change','#id_system',function(){ fill_subsystem(); });