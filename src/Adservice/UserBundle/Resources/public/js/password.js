
        $('#generate_password').click(function(){

            var id = $('#user_id').val();
            var route  = 'generate_password';
            var locale = $(document).find("#data_locale").val();

            var url = Routing.generate(route, {_locale: locale, id: id });;
            window.open(url,'_self');
        });

        $('#confirm_password').click(function(){

            var id = $('#user_id').val();
            var route  = 'change_password';
            var locale = $(document).find("#data_locale").val();

            var new_pass = $('#new_password1').val();
            var rep_pass = $('#new_password2').val();
            var old_pass = $('#old_password' ).val();

            if(new_pass == '') new_pass = 'none';
            if(rep_pass == '') rep_pass = 'none';
            if(old_pass == '') old_pass = 'none';

            var url = Routing.generate(route, {_locale: locale, id: id, new_pass: new_pass, rep_pass: rep_pass, old_pass: old_pass });
            window.open(url,'_self');
        });


        function check_password()
        {
            var password = $("input[id*='_type_password_password1']").val();
            var pass = password.toLowerCase();
            var PASS = password.toUpperCase();

            var numeros="0123456789";
            var letras="abcdefghyjklmnñopqrstuvwxyz";
            var find = 0;
            var error = 0;

            if(password.length < 8) error = 1;
            else {
                for(i=0; i<pass.length; i++){
                    if (numeros.indexOf(pass.charAt(i),0)!=-1){
                        find = 1;
                    }
                }
            }
            if (find == 0) error = 1;
            else {
                for(i=0; i<pass.length; i++){
                    if (letras.indexOf(pass.charAt(i),0)!=-1){
                        find = 1;
                    }
                }
            }
            if (find == 0) error = 1;
            else {
                for(i=0; i<PASS.length; i++){
                    if (letras.indexOf(PASS.charAt(i),0)!=-1){
                        find = 1;
                    }
                }
            }

            if (error == 1) {
                event.preventDefault();
                alert($("#badpass").val());
            }


            if ( isNaN($("input[id*='number_']").val())) {
                $("input[id*='number_']").css('border-color','#FF0000');
                alert($("#isNaN").val());
                return false;
            }
        }