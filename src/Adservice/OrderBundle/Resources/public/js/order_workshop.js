
//Funciones para el autorellenado de los combos de campos de coches de los formularios

    $(document).ready(function() {

        var code = $('form').find('#code').val();

        if(code != 0){ $('form').find('select[name*=code_workshop]').val(code);
                       $('#workshopOrder_newOrder_code_workshop').val(code);
                     }

        var id_partner = $('form').find('#id_partner').val();
        var partners   = $('form').find('#select[id=partners]').val();

        if(id_partner == 0 || id_partner == undefined){

            $('#workshopOrder_newOrder_partner').empty();

            $('#partners option').appendTo("#workshopOrder_newOrder_partner");
        }else{
            $('form').find('select[name*=partner]').val(id_partner);
        }

        //si clickamos el combobox de los socios rellenamos el de tiendas
        $('form').find('select[name*=partner]').change(function() {
            populate_shop();
            // fill_code_partner($(this).val());

            fill_code_workshop($(this).val());
        });

        // CHECKS
        enable_checks($('#workshopOrder_newOrder_haschecks').is(':checked'));
        enable_checks($('#workshopOrder_editOrder_haschecks').is(':checked'));

        $('#workshopOrder_newOrder_haschecks').click(function(){

            var checked = $(this).is(':checked');
            $('#workshopOrder_newOrder_numchecks' ).val('');
            enable_checks(checked);
        });

        $('#workshopOrder_editOrder_haschecks').click(function(){

            var checked = $(this).is(':checked');
            $('#workshopOrder_editOrder_numchecks' ).val('');
            enable_checks(checked);
        });

        function enable_checks(bool) {
            if(bool == true) {
                $('#workshopOrder_newOrder_numchecks' ).removeAttr("disabled");
                $('#workshopOrder_editOrder_numchecks' ).removeAttr("disabled");
            }else{
                $('#workshopOrder_newOrder_numchecks' ).attr("disabled", "disabled");
                $('#workshopOrder_editOrder_numchecks' ).attr("disabled", "disabled");
            }
        }
        // END CHECKS

        $('#btn_create').click(function() {
            $("input[id*='number_']").each(function() {
                if ( isNaN($(this).val())) {
                    $(this).css('border-color','#FF0000');
                    alert($("#isNaN").val());
                    event.preventDefault();
                }else{
                    $(this).css('border-color','#ccc');
                }
            });
        });
        $('#btn_edit').click(function() {
            $("input[id*='number_']").each(function() {
                if ( isNaN($(this).val())) {
                    $(this).css('border-color','#FF0000');
                    alert($("#isNaN").val());
                    event.preventDefault();
                }else{
                    $(this).css('border-color','#ccc');
                }
            });
        });

        $('#workshopOrder_newOrder_cif').blur(function() {
            var cif = $('#workshopOrder_newOrder_cif').val();
            var text_error = $('#exist_cif').val();
             $.ajax({
                type: "POST",
                url: Routing.generate('search_cif', {'cif' : cif ,'_locale':'{{ app.session.locale }} ' }),
                dataType: "json",
                success: function (data) {
                    var find = JSON.parse(data);
                    if(find == true){
                        $('.error_cif').empty();
                        $('.error_cif').append('<p id="lbl_error">'+ text_error +'</p>');
                    }
                    else{
                        $('.error_cif').empty();
                    }

                },
                error: function () {
                    console.log("Error loading cifs...");
                }
            });
        });


        $('#workshopOrder_editOrder_cif').blur(function() {
            var cif = $('#workshopOrder_editOrder_cif').val();
            var text_error = $('#exist_cif').val();
             $.ajax({
                type: "POST",
                url: Routing.generate('search_cif', {'cif' : cif ,'_locale':'{{ app.session.locale }} ' }),
                dataType: "json",
                success: function (data) {
                    var find = JSON.parse(data);
                    if(find == true){
                        $('.error_cif').empty();
                        $('.error_cif').append('<p id="lbl_error">'+ text_error +'</p>');
                    }
                    else{
                        $('.error_cif').empty();
                    }

                },
                error: function () {
                    console.log("Error loading cifs...");
                }
            });
        });

    });