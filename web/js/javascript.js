
$(document).ready( function ()
{
    //var route = Routing.generate('loadUsers');

    // Detectando eventos sobre los inputs
    $("input[id$='username']").on( "focusout", function()
    {
        slug = slugify($(this).val());
        $(this).val(slug);
    });

});

function slugify(text)
{
  return text.toString().toLowerCase()
    .replace(/\s+/g, '-')           // Replace spaces with -
    .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
    .replace(/\-\-+/g, '-')         // Replace multiple - with single -
    .replace(/^-+/, '')             // Trim - from start of text
    .replace(/-+$/, '');            // Trim - from end of text
}
/*
    // Detectando eventos de teclado sobre los inputs
    $("input").on('keyup', function (e)
    {
        // "Enter" busca y ejecuta su respectivo submit
        if (e.keyCode == 13)
        {
            e.preventDefault();
            var type = $(this).attr('type');
            var val = $(this).val();
            ok=true;

            if (type == "number")
            {
                ok = (val == "" || !isNaN(val));
            }
            else if (type == "email")
            {
                var re = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
                ok = (val == "" || re.test(val));
            }

            if(ok)
            {
                var next = $(this).parent().next().find('input');
                next.focus();
            }
            //$(this).parent().find('button:submit').click();
        }
    });
*/
// Validacion dinamica de emails
    /*
    $("input[id*='email']").on('input', function() {
        var input=$(this);
        var re = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
        var is_email=re.test(input.val());
        if(is_email){input.removeClass("invalid").addClass("valid");}
        else{input.removeClass("valid").addClass("invalid");}
    });
    */

/*

$("input").on('keyup', function (e)
{
    if (e.keyCode==13)
    {
        var next = $(this).parent().next().find('input');
        next.trigger('click');
        //next.focus();
         //$(this).parent('#working-space').children('.submit').trigger('click');

        return false;
    }
});
*/


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
