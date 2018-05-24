
//CLICK ON FILTER BUTTONS
$("#filter_plate_number").on('click', function () {

    if(validatePlateNumber($("#new_car_form_plateNumber").val())) {
        findCarByPlateNumber();
    }
});

$("#filter_vin").on('click', function () {
    findCarByVin();
});

$("#filter_motor").on('click', function() {
    fillBrandSelect();
});

//CHANGE INPUTS
$("#new_car_form_brand").on('change', function() {
    fillModelSelect($("#new_car_form_brand").val());
});

$("#new_car_form_model").on('change', function() {
    fillVersionSelect($("#new_car_form_model").val());
});

$("#new_car_form_version").on('change', function() {
    fillVersionCarData();
});

$("#new_car_form_plateNumber").focusout(function () {
        if (validatePlateNumber($("#new_car_form_plateNumber").val())) {
            findCarByPlateNumber();
        }
    }).focusin(function() {
        if (!$("#new_car_form_plateNumber").prop('readonly')){
            clearInputs()
        }
});

//CLICK SAVE BUTTONS
$('#btn_create, #save_close').on('click', function(event) {
    if (validateVIN($("#new_car_form_vin").val()) == false) {
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
            $( "#new_car_form_motor" ).autocomplete({
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
    $('#new_car_form_plateNumber').val(plateNumber);

    if (plateNumber == ''){
        valid = false;
    }

    if ($("#new_car_form_plateNumber").prop('readonly')){
        valid = false;
    }

    return valid;
}

function validateVIN(vin)
{
    var valid = true;
    vin = string_to_slug(vin).replace(/\ /g, '').replace(/\-/g, '').toUpperCase();
    $("#new_car_form_vin").val(vin);

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

//FORM INPUTS
function clearInputs()
{
    $("#new_car_form_vin").val('').prop('readOnly', false);
    $("#new_car_form_motor").val('').prop('readOnly', false);
    $("#new_car_form_brand").val('').show();
    $("#new_car_form_model").val('').show();
    $("#new_car_form_version").val('').show();
    $("#new_car_form_year").val('');
    $("#new_car_form_kW").val('').prop('readOnly', false);
    $("#new_car_form_displacement").val('').prop('readOnly', false);

    $("#new_car_form_brand_read_only").hide();
    $("#new_car_form_model_read_only").hide();
    $("#new_car_form_version_read_only").hide();

    $("#filter_vin").show();
    $("#filter_motor").show();

    $("#new_car_form_origin").val('custom');
    $("#new_car_form_variants").val(1);
    $("#new_car_form_year").val('').prop('readOnly', false);
}

function setReadOnlyInputs(status)
{
    $("#new_car_form_vin").prop('readOnly', true);
    $("#new_car_form_motor").prop('readOnly', true);
    $("#new_car_form_brand").hide();
    $("#new_car_form_brand_read_only").show();
    $("#new_car_form_model").hide();
    $("#new_car_form_model_read_only").show();
    $("#new_car_form_version").hide();
    $("#new_car_form_version_read_only").show();
    $("#new_car_form_kW").prop('readOnly', true);
    $("#new_car_form_displacement").prop('readOnly', true);

    $("#filter_vin").hide();
    $("#filter_motor").hide();

    if(status == 'verified') {
        $("#new_car_form_year").prop('readOnly', true);
    }
}

function resetSelect(id)
{
    $(id).empty();
    $(id).append("<option></option>");
}

function updateTextCar()
{
    $('#car').text(
        $('#new_car_form_brand option:selected').text()+' '+
        $('#new_car_form_model option:selected').text()+' '+
        $('#new_car_form_version option:selected').text()
    );
}

//AJAX FUNCTIONS
function findCarByPlateNumber()
{
    $.ajax({
        type: "POST",
        url: Routing.generate('get_car_from_plate_number', {_locale: $(document).find("#data_locale").val(), idPlateNumber: $("#new_car_form_plateNumber").val()}),
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

                } else if (data['cars'].length > 1) {

                    $('#modal_webservice_select_options').html('');
                    for (var i = 0, len = data['cars'].length; i < len; i++) {
                        car = data['cars'][i];
                        if(data['carInfo']) { car = $.extend(car, data['carInfo']); }

                        $('#modal_webservice_select_options').append(
                            '<input type="radio" name="dgt_option"  data-car=\'' + JSON.stringify(car) + '\' id="' + i + '">' +
                            '<label for="' + i + '">' + data['cars'][i].carDescription + '</label><hr>'
                        );
                    }
                    $('#modal_webservice_select').modal();
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
        url: Routing.generate('get_car_from_vin', {_locale: $("#data_locale").val(), vin: $('#new_car_form_vin').val()}),
        dataType: "json",
        beforeSend: function () { $("body").css("cursor", "progress"); },
        complete: function () { $("body").css("cursor", "default"); },
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
        url: Routing.generate('car_by_motor', {_locale: $("#data_locale").val(), motor: $('#new_car_form_motor').val()}),
        data: {motor: $('#new_car_form_motor').val()},
        dataType: "json",
        async: false,
        beforeSend: function(){ $("body").css("cursor", "progress"); },
        complete: function(){ $("body").css("cursor", "default"); },
        success: function(data) {
            console.log(data);
            if (data['error'] != "No hay coincidencias") {

                if (data['error'] == "msg_bad_filter") {
                    msg_bad_filter = $('#msg_bad_filter').val();
                    alert(msg_bad_filter);
                } else {
                    resetSelect('#new_car_form_brand');
                    resetSelect('#new_car_form_model');
                    resetSelect('#new_car_form_version');

                    for (var i = 0, len = data.length; i < len; i++) {

                        $("#new_car_form_brand").append("<option value=" + data[i].id + ">" + data[i].name + "</option>")
                    }

                    $("#new_car_form_brand").val(selected);
                    $("#new_car_form_brand_read_only").val($("#new_car_form_brand option[value='"+selected+"']").text());
                    updateTextCar();
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

    if ($('#new_car_form_motor').val() != '') {
        filter = 'motor';
        filter_value = $('#new_car_form_motor').val();
    }

    $.ajax({
        type: "POST",
        url: Routing.generate('car_model', {_locale: $("#data_locale").val(), id_brand: brand, filter: filter, filter_value: filter_value}),
        data: {id_brand: brand, filter: filter, filter_value: filter_value},
        dataType: "json",
        async: false,
        beforeSend: function(){ $("body").css("cursor", "progress"); },
        complete: function(){ $("body").css("cursor", "default"); },
        success: function(data) {

            if (data['error'] != "No hay coincidencias") {

                resetSelect('#new_car_form_model');
                resetSelect('#new_car_form_version');

                for (var i = 0, len = data.length; i < len; i++) {

                    $("#new_car_form_model").append("<option value=" + data[i].id + ">" + data[i].name + "</option>")
                }

                $("#new_car_form_model").val(selected);
                $("#new_car_form_model_read_only").val($("#new_car_form_model option[value='"+selected+"']").text());
                updateTextCar();
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

    if ($('#new_car_form_motor').val() != '') {
        filter = 'motor';
        filter_value = $('#new_car_form_motor').val();
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

                resetSelect('#new_car_form_version');

                for (var i = 0, len = data.length; i < len; i++) {

                    $("#new_car_form_version").append(
                        "<option data-motor='"+data[i].motorName+"'"+" value="+data[i].id+" "+
                        "data-inicio='"+data[i].inicio+"' data-fin='"+data[i].fin+"' " +
                        "data-kw='"+data[i].kw+"' data-cm3='"+data[i].cm3+"'>"+ data[i].name + "</option>"
                    );
                }

                $("#new_car_form_version").val(selected);
                $("#new_car_form_version_read_only").val($("#new_car_form_version option[value='"+selected+"']").first().text());
                updateTextCar();
            }
        },
        error: function() {
            console.log("Error al cargar versiones...");
        }
    });
}

function fillVersionCarData()
{
    var selected = $("#new_car_form_version option:selected");

    var date = '';
    if (selected.data('inicio')) {
        date = selected.data('inicio').toString().slice(0,4);
    }
    if (selected.data('fin')) {
        date = date + ' - ' + selected.data('fin').toString().slice(0,4);
    }

    $('#new_car_form_year').val(date);

    $('#new_car_form_motor').val(selected.data('motor'));
    $('#new_car_form_kW').val(selected.data('kw'));
    $('#new_car_form_displacement').val(selected.data('cm3'));

    $( "#dis" ).attr("href", $( "#dis-url" ).val()+'/'+selected.val());
}

//Fill inputs
function fillCar(car)
{
    clearInputs();

    $("#new_car_form_plateNumber").val(car.plateNumber);
    $("#new_car_form_vin").val(car.vin);
    $("#new_car_form_origin").val(car.origin);
    $("#new_car_form_variants").val(car.variants);

    if (car.brandId != '' && $("#new_car_form_brand option[value='"+car.brandId+"']").length < 1) {

        fillBrandSelect(car.brandId);
    } else {
        $("#new_car_form_brand").val(car.brandId);
        $("#new_car_form_brand_read_only").val($("#new_car_form_brand option:selected").text());
    }

    if (car.modelId != '' && $("#new_car_form_model option[value='"+car.modelId+"']").length < 1) {

        fillModelSelect(car.brandId, car.modelId);
    } else {
        $("#new_car_form_model").val(car.modelId);
        $("#new_car_form_model_read_only").val($("#new_car_form_model option:selected").text());
    }

    if (car.versionId != '' && $("#new_car_form_version option[value='"+car.versionId+"']").length < 1) {

        fillVersionSelect(car.modelId, car.versionId);
    } else {
        $("#new_car_form_version").val(car.versionId);
        $("#new_car_form_version_read_only").val($("#new_car_form_version option:selected").first().text());
    }

    $("#new_car_form_motor").val(car.motor);
    $("#new_car_form_year").val(car.year);
    $("#new_car_form_kW").val(car.kw);
    $("#new_car_form_displacement").val(car.cm3);

    $( "#dis" ).attr("href", $( "#dis-url" ).val()+'/'+car.versionId);

    if(car.origin == 'DGT' || car.status == 'verified') {
        setReadOnlyInputs(car.status);
    }

    return true;
}