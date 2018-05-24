$("#filter_motor").on('click', function() {
    fillBrandSelect();
});

//CHANGE INPUTS
$("#filter_car_form_brand").on('change', function() {
    fillModelSelect($("#filter_car_form_brand").val());
});

$("#filter_car_form_model").on('change', function() {
    fillVersionSelect($("#filter_car_form_model").val());
});

$("#filter_car_form_version").on('change', function() {
    fillVersionCarData();
});

$("#filter_car_form_plateNumber").focusout(function () {
    if (validatePlateNumber($("#filter_car_form_plateNumber").val())) {
        findCarByPlateNumber();
    }
}).focusin(function() {
    if (!$("#filter_car_form_plateNumber").prop('readonly')){
        clearInputs()
    }
});

//CLICK SAVE BUTTONS
$('#btn_create, #save_close').on('click', function(event) {
    if (validateVIN($("#filter_car_form_vin").val()) == false) {
        event.preventDefault();
        validateVIN();
    }
});

//MODAL CLICK BUTTON
$('#modal-btn-accept').click(function(){
    $('#modal_webservice_select').modal('toggle');
    fillCar($('#modal_webservice_select_options > input:checked').data('car'));
});

$(function() {
    $.ajax({
        type: "POST",
        url: Routing.generate('car_motors', {_locale: $(document).find("#data_locale").val()}),
        dataType: "json",
        success : function(data) {
            $( "#filter_car_form_motor" ).autocomplete({
                source: data
            });

            $('#MainBody').append('<div id="autocomplete-hider" style="height:0;margin-left:11px;position:absolute;top:0;"></div>');
            $('.ui-autocomplete').appendTo('#autocomplete-hider');
        },
        error : function(){
            console.log("Error al cargar los motores...");
        }
    });
});

//FILTERS FUNNEL NEW/EDIT TICKET
function checkEnterInFilterFields(event, id)
{
    if(event.keyCode == 13)
    {
        if (id.endsWith("_plateNumber")) {
            $('#'+id).blur();
            event.preventDefault();
        }
        else if (id.endsWith("_vin")) {
            $('#'+id).blur();
            findCarByVin();
            event.preventDefault();
        }
        else if (id.endsWith("_motor")) {
            fillBrand();
            event.preventDefault();
        }
    }
}

//VALIDATORS
function validatePlateNumber(plateNumber)
{
    var valid = true;

    plateNumber = string_to_slug(plateNumber).replace(/\ /g, '').replace(/\-/g, '').toUpperCase();
    $('#filter_car_form_plateNumber').val(plateNumber);

    if (plateNumber == ''){
        valid = false;
    }

    if ($("#filter_car_form_plateNumber").prop('readonly')){
        valid = false;
    }

    return valid;
}

function validateVIN(vin)
{
    var valid = true;
    vin = string_to_slug(vin).replace(/\ /g, '').replace(/\-/g, '').toUpperCase();
    $("#filter_car_form_vin").val(vin);

    if (vin.length != 17) {
        valid = false;
        alert($('#msg_bad_cif_length').val());
    }
    if(vin.toLowerCase().indexOf("o") >= 0) {
        valid = false;
        alert($('#msg_bad_cif_o').val());
    }

    return valid;
}

function resetSelect(id)
{
    $(id).empty();
    $(id).append("<option></option>");
}

//AJAX FUNCTIONS
function findCarByPlateNumber()
{
    $.ajax({
        type: "POST",
        url: Routing.generate('get_car_from_plate_number_db', {_locale: $(document).find("#data_locale").val(), plateNumber: $("#filter_car_form_plateNumber").val()}),
        dataType: "json",
        beforeSend: function () {
            $("body").css("cursor", "progress");
        },
        complete: function () {
            $("body").css("cursor", "default");
        },
        success: function (data) {
            if (data['error'] !== "No hay coincidencias") {

                var car = [];

                if (data['cars'].length == 1) {

                    car = data['cars'][0];
                    if(data['carInfo']) { car = $.extend(car, data['carInfo']); }
                    fillCar(car);
                }
            }
        },
        error: function () {
            console.log("Error loading models...");
        }
    });
}

function findCarByVin()
{
    $.ajax({
        type: "POST",
        url: Routing.generate('get_car_from_vin', {_locale: $("#data_locale").val(), vin: $('#filter_car_form_vin').val()}),
        dataType: "json",
        beforeSend: function () {
            $("body").css("cursor", "progress");
        },
        complete: function () {
            $("body").css("cursor", "default");
        },
        success: function (data) {
            if (data['error'] !== "No hay coincidencias") {
                fillCar(data);
            }
            else {
                alert($("#msg_vin_not_found").val());
            }
        },
        error: function () {
            console.log("Error loading models...");
        }
    });
}

function fillBrandSelect(selected)
{
    selected = selected || '';

    $.ajax({
        type: "POST",
        url: Routing.generate('car_by_motor', {_locale: $("#data_locale").val(), motor: $('#filter_car_form_motor').val()}),
        data: {motor: $('#filter_car_form_motor').val()},
        dataType: "json",
        async: false,
        beforeSend: function(){
            $("body").css("cursor", "progress");
        },
        complete: function(){
            $("body").css("cursor", "default");
        },
        success: function(data) {

            if (data['error'] != "No hay coincidencias") {

                if (data['error'] == "msg_bad_filter") {
                    msg_bad_filter = $('#msg_bad_filter').val();
                    alert(msg_bad_filter);
                } else {
                    resetSelect('#filter_car_form_brand');
                    resetSelect('#filter_car_form_model');
                    resetSelect('#filter_car_form_version');

                    for (var i = 0, len = data.length; i < len; i++) {

                        $("#filter_car_form_brand").append("<option value=" + data[i].id + ">" + data[i].name + "</option>")
                    }

                    $("#filter_car_form_brand").val(selected);
                    $("#filter_car_form_brand_read_only").val($("#filter_car_form_brand option[value='"+selected+"']").text());
                }
            } else {
                msg_motor_not_found = $('#msg_motor_not_found').val();
                alert(msg_motor_not_found);
            }
        },
        error: function() {
            console.log("Error al filtrar por motor...");
        }
    });
}

function fillModelSelect(brand, selected)
{
    selected = selected || '';

    var filter = '';
    var filter_value = '';

    if ($('#filter_car_form_motor').val() != '') {
        filter = 'motor';
        filter_value = $('#filter_car_form_motor').val();
    }

    $.ajax({
        type: "POST",
        url: Routing.generate('car_model', {_locale: $("#data_locale").val(), id_brand: brand, filter: filter, filter_value: filter_value}),
        data: {id_brand: brand, filter: filter, filter_value: filter_value},
        dataType: "json",
        async: false,
        beforeSend: function(){
            $("body").css("cursor", "progress");
        },
        complete: function(){
            $("body").css("cursor", "default");
        },
        success: function(data) {

            if (data['error'] != "No hay coincidencias") {

                resetSelect('#filter_car_form_model');
                resetSelect('#filter_car_form_version');

                for (var i = 0, len = data.length; i < len; i++) {

                    $("#filter_car_form_model").append("<option value=" + data[i].id + ">" + data[i].name + "</option>")
                }

                $("#filter_car_form_model").val(selected);
                $("#filter_car_form_model_read_only").val($("#filter_car_form_model option[value='"+selected+"']").text());
            }
        },
        error: function() {
            console.log("Error al cargar modelos...");
        }
    });
}

function fillVersionSelect(model, selected)
{
    var filter = '';
    var filter_value = '';

    if ($('#filter_car_form_motor').val() != '') {
        filter = 'motor';
        filter_value = $('#filter_car_form_motor').val();
    }

    selected = selected || '';

    $.ajax({
        type: "POST",
        url: Routing.generate('car_version', {_locale: $("#data_locale").val(), id_model: model, filter: filter, filter_value: filter_value}),
        data: {id_model: model, filter: filter, filter_value: filter_value},
        dataType: "json",
        async: false,
        beforeSend: function(){ $("body").css("cursor", "progress"); },
        complete: function(){ $("body").css("cursor", "default"); },
        success: function(data) {

            if (data['error'] != "No hay coincidencias") {

                resetSelect('#filter_car_form_version');

                for (var i = 0, len = data.length; i < len; i++) {

                    $("#filter_car_form_version").append(
                        "<option data-motor='"+data[i].motorName+"'"+" value="+data[i].id+" "+
                        "data-inicio='"+data[i].inicio+"' data-fin='"+data[i].fin+"' " +
                        "data-kw='"+data[i].kw+"' data-cm3='"+data[i].cm3+"'>"+ data[i].name + "</option>"
                    );
                }

                $("#filter_car_form_version").val(selected);
                $("#filter_car_form_version_read_only").val($("#filter_car_form_version option[value='"+selected+"']").first().text());
            }
        },
        error: function() {
            console.log("Error al cargar versiones...");
        }
    });
}

function fillVersionCarData()
{
    var selected = $("#filter_car_form_version option:selected");

    var date = '';
    if (selected.data('inicio')) {
        date = selected.data('inicio').toString().slice(0,4);
    }
    if (selected.data('fin')) {
        date = date + ' - ' + selected.data('fin').toString().slice(0,4);
    }

    $('#filter_car_form_year').val(date);

    $('#filter_car_form_motor').val(selected.data('motor'));
    $('#filter_car_form_kW').val(selected.data('kw'));
    $('#filter_car_form_displacement').val(selected.data('cm3'));

    $( "#dis" ).attr("href", $( "#dis-url" ).val()+'/'+selected.val());
}

//Fill inputs
function fillCar(car)
{
    clearInputs();

    $("#filter_car_form_motor").val('');
    $("#filter_car_form_plateNumber").val(car.plateNumber);
    $("#filter_car_form_vin").val(car.vin);
    $("#filter_car_form_origin").val(car.origin);
    $("#filter_car_form_status").val(car.status);
    $("#filter_car_form_variants").val(car.variants);

    if ($("#filter_car_form_brand option[value='"+car.brandId+"']").length < 1) {

        fillBrandSelect(car.brandId);
    } else {
        $("#filter_car_form_brand").val(car.brandId);
        $("#filter_car_form_brand_read_only").val($("#filter_car_form_brand option:selected").text());
    }

    if ($("#filter_car_form_model option[value='"+car.modelId+"']").length < 1) {

        fillModelSelect(car.brandId, car.modelId);
    } else {
        $("#filter_car_form_model").val(car.modelId);
        $("#filter_car_form_model_read_only").val($("#filter_car_form_model option:selected").text());
    }

    if ($("#filter_car_form_version option[value='"+car.versionId+"']").length < 1) {

        fillVersionSelect(car.modelId, car.versionId);
    } else {
        $("#filter_car_form_version").val(car.versionId);
        $("#filter_car_form_version_read_only").val($("#filter_car_form_version option:selected").first().text());
    }

    $("#filter_car_form_motor").val(car.motor);
    $("#filter_car_form_year").val(car.year);
    $("#filter_car_form_kW").val(car.kw);
    $("#filter_car_form_displacement").val(car.cm3);

    if(car.origin == 'DGT' || car.status == 'verified') {
        setReadOnlyInputs(car.status);
    }

    return true;
}

//FORM INPUTS
function clearInputs()
{
    $("#filter_car_form_vin").val('').prop('readOnly', false);
    $("#filter_car_form_motor").val('').prop('readOnly', false);
    $("#filter_car_form_brand").val('').show();
    $("#filter_car_form_model").val('').show();
    $("#filter_car_form_version").val('').show();
    $("#filter_car_form_kW").val('').prop('readOnly', false);
    $("#filter_car_form_displacement").val('').prop('readOnly', false);
    $("#filter_car_form_year").val('').prop('readOnly', false);

    $("#filter_car_form_brand_read_only").hide();
    $("#filter_car_form_model_read_only").hide();
    $("#filter_car_form_version_read_only").hide();

    $("#filter_motor").show();
}

function setReadOnlyInputs(status)
{
    $("#filter_car_form_vin").prop('readOnly', true);
    $("#filter_car_form_motor").prop('readOnly', true);
    $("#filter_car_form_brand").hide();
    $("#filter_car_form_brand_read_only").show();
    $("#filter_car_form_model").hide();
    $("#filter_car_form_model_read_only").show();
    $("#filter_car_form_version").hide();
    $("#filter_car_form_version_read_only").show();
    $("#filter_car_form_kW").prop('readOnly', true);
    $("#filter_car_form_displacement").prop('readOnly', true);

    $("#filter_motor").hide();

    if(status == 'verified') {
        $("#filter_car_form_year").prop('readOnly', true);
    }
}
