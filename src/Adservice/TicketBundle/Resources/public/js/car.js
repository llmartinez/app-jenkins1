
//Funciones para el autorellenado de los combos de campos de coches de los formularios

    /*
        FILTERS FUNNEL NEW/EDIT TICKET
    */
    function checkEnterInFilterFields(e, id)
    {
        if(e.keyCode == 13)
        {
            if (id.endsWith("_plateNumber")) {
                fill_car_from_plate_number();
                event.preventDefault();
            }
            else if (id.endsWith("_vin")) {
                fill_car_from_vin();
                event.preventDefault();
            }
            else if (id.endsWith("_motor")) {
                fill_car_by_motor();
                event.preventDefault();
            }
            else if (id.endsWith("_year")) {
                fill_car_by_year();
                event.preventDefault();
            }
        }
    }

    $("#filter_plate_number").on('click', function ()
    {
        fill_car_from_plate_number();
    });
    
    $("#filter_vin").on('click', function ()
    {
        fill_car_from_vin();
    });

    $(document).on('click','#btn_create',function(){
        checkVIN()
    });

    $(document).on('click','#save_close',function(){
        checkVIN()
    });

    function checkVIN(){
        var str = $('#new_car_form_vin').val()
        var len = str.length;
        if (len != 17){
            event.preventDefault();
            var err = $('#msg_bad_cif_length').val();
            alert(err);
        }
        if(str.toLowerCase().indexOf("o") >= 0){
            event.preventDefault();
            var err = $('#msg_bad_cif_o').val();
            alert(err);
        }
    }

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

    $(document).on('change','#id_system',function(){
       fill_subsystem();
    });

    $(document).on('click','#filter_year',function(){
        fill_car_by_year();
    });

    $(document).on('click','#filter_motor',function(){
        fill_car_by_motor();
    });

    $(document).on('click','.closeTicket',function(){
        //if ($('#year_assessor').val() != undefined)
        //    checkYearLength();
    });
    $(document).on('click','.sendTicket',function(){
        //if ($('#year_assessor').val() != undefined)
        //    checkYearLength();
    });

    function checkYearLength(){

        var len = $('#new_car_form_year').val().length;
        if (len != 4) {
            event.preventDefault();
            var err = $('#msg_bad_year').val();
            alert(err);
        }
    }

    // AUTOCOMPLETAR CAMPO MOTOR
    $(function() {
        var availableTags = '';
        var route = 'car_motors';
        var locale = $(document).find("#data_locale").val();
        $.ajax({
            type        : "POST",
            url         : Routing.generate(route, {_locale: locale}),
            dataType    : "json",
            success : function(data) {
                $( "#new_car_form_motor" ).autocomplete({
                   source: data
                 });

                // Ocultamos el autocomplete en un div para no mostrar errores de visualizacion
                //  (el tamaño de la lista afecta al tamaño de pantalla)

                $('#MainBody').append('<div id="autocomplete-hider" style="height:0;margin-left:11px;position:absolute;top:0;"></div>');
                $('.ui-autocomplete').appendTo('#autocomplete-hider');
            },
            error : function(){
                console.log("Error al cargar los motores...");
            }
        });
    
   

    

    });