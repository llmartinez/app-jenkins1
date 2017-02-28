
$(document).ready( function ()
{
    // Catch change in Partner select
    $("#workshop_Partner").on( "change", function()
    {
        getMaxIdWorkshopByPartner($(this).val());
    });

});

function getMaxIdWorkshopByPartner(partner)
{
    $.ajax({
        url         : Routing.generate('getMaxIdWorkshopByPartner', {'partner': partner}),
        dataType    : "json",
        type        : "POST",
        beforeSend: function(){ $("body").css("cursor", "progress"); },
        complete: function(){ $("body").css("cursor", "default"); },
        success : function(data)
        {
            $('#workshop_id').empty();
        	$('#workshop_id').val(data.id);
        },
        error : function(){
            console.log("Error obtaining MaxIdWorkshop By Partner...");
        }
    });
}
