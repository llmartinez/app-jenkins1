
$(document).ready( function ()
{
    //var route = Routing.generate('loadUsers');

} );


/** Funcion Ajax para generar un DataTable de usuarios
*/
/*
function fillDataTable(route) {
    $.ajax({
        url         : Routing.generate('loadUsers'),
        dataType    : "json",
        type        : "POST",
        beforeSend: function(){ $("body").css("cursor", "progress"); },
        complete: function(){ $("body").css("cursor", "default"); },
        success : function(data)
        {
            $.each(data, function(idx, elm) {
                $('#datatable-body').append("<tr><td>"+elm.id+"</td><td>"+elm.username+"</td><td>"+elm.email+"</td><td>GO</td></tr>");
            });
            $('#list').empty();
        },
        error : function(){
            console.log("Error al cargar registros...");
        }
    });
}
*/
