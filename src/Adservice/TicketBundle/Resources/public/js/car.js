
//Funciones para el autorellenado de los combos de campos de coches de los formularios

        //cambiar model en funcion de brand
        $(document).on('change','#new_car_form_brand',function(){
        // $('#new_car_form_brand').on('change', function(){
            var route = 'car_model';
            fill_model(route);
        });

    $(document).ready(function() {
        //cambiar version en funcion de model
        $('#new_car_form_model').change(function() {
            var route = 'car_version';
            fill_version(route);
        });
    });