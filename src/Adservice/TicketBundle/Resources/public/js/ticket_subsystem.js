
$(document).ready(function() {
    // Limpiamos el combo de subsystem del formulario y cargamos subsistemas
    //fill_subsystem();
    $('select[id*=_subsystem]').empty();
});

//cambiar model en funcion de brand
$(document).on('change','#id_system',function(){ fill_subsystem(); });