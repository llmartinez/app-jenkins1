$(document).on('change','select[name*=country]',function(){
    //si clickamos el combobox de los paises repoblamos la de las regiones
       populate_region('regions_from_country');
});

$(document).ready(function(){

    var entity   = $('form').find('#entity').val();
    if(entity == 'edit') {
        // REGIONS FROM COUNTRY
        var region   = $('form').find('#adservice_workshopbundle_workshoptype_region').val();
        var city     = $('form').find('#adservice_workshopbundle_workshoptype_city').val();
        if(region == "") region = 'no-region';
        if(city   == "") city   = 'no-city';
        populate_region(region, city);
    }
    else{
        populate_region();
    }
});
