
//Funciones para el autorellenado de los combos de campos de coches de los formularios

    //cambiar model en funcion de brand
    $(document).on('change','#new_car_form_brand',function(){
    // $('#new_car_form_brand').on('change', function(){
        fill_model();
    });

    $(document).on('change','#new_car_form_model',function(){
        fill_version();
    });

    $(document).on('change','#new_car_form_version',function(){
        fill_car_data();
    });