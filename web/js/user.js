
$(document).ready( function ()
{
    // Set Max CodePartner in Partner Form
    if($("#partner_codePartner").val() != undefined)
        setMaxCodePartner();

    // Set Max CodeWorkshop by catching a Partner change in Workshop Form
    if($("#workshop_codeWorkshop").val() != undefined)
    {
        $("#workshop_Partner").on( "change", function()
        {
            setMaxCodeWorkshopByPartner($(this).val());
        });
    }

});

function setMaxCodePartner()
{
    $.ajax({
        url         : Routing.generate('getMaxCodePartner'),
        dataType    : "json",
        type        : "POST",
        beforeSend: function(){ $("body").css("cursor", "progress"); },
        complete: function(){ $("body").css("cursor", "default"); },
        success : function(data)
        {
            $('#partner_codePartner').empty();
            $('#partner_codePartner').val(data.codePartner);
        },
        error : function(){
            console.log("Error obtaining MaxCodePartner...");
        }
    });
}

function setMaxCodeWorkshopByPartner(partner)
{
    $.ajax({
        url         : Routing.generate('getMaxCodeWorkshopByPartner', {'partner': partner}),
        dataType    : "json",
        type        : "POST",
        beforeSend: function(){ $("body").css("cursor", "progress"); },
        complete: function(){ $("body").css("cursor", "default"); },
        success : function(data)
        {
            $('#workshop_codeWorkshop').empty();
            $('#workshop_codeWorkshop').val(data.codeWorkshop);
        },
        error : function(){
            console.log("Error obtaining MaxCodeWorkshop By Partner...");
        }
    });
}
