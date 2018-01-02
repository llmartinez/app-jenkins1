
        $('#generate_password').click(function(){

            var id = $('#user_id').val();
            var route  = 'generate_password';
            var locale = $(document).find("#data_locale").val();

            var url = Routing.generate(route, {_locale: locale, id: id });
            window.open(url,'_self');
        });

        $('#confirm_password').click(function() {
            var error = check_password();

            if (error == 0) {
                var id = $('#user_id').val();
                var locale = $(document).find("#data_locale").val();

                $.ajax({
                    type: "POST",
                    url: Routing.generate('change_password', {'_locale': locale, id: id }),
                    data: {
                        'new_pass': $('#new_password1').val(),
                        'rep_pass': $('#new_password2').val(),
                        'old_pass': $('#old_password' ).val()
                    },
                    success: function (data) {
                        location.reload();
                    }
                });
            }
        });


        function check_password()
        {
            var password = $("input[id*='_password1']").val();

            var numeros="0123456789";
            var letras="abcdefghyjklmnñopqrstuvwxyz";
            var LETRAS="ABCDEFGHYJKLMNÑOPQRSTUVWXYZ";
            var forbiddenChars = ["\"", "'"];
            var find = 0;
            var error = 0;

            if (password.length < 8) {
                error = 1;
            }else if(password != $("input[id*='_password2']").val()) {
                error = 2;
            } else {

                for(i=0; i<password.length; i++){
                    if (numeros.indexOf(password.charAt(i),0)!=-1){
                        find = 1;
                    }
                }

                if (find == 0) error = 1;
                else {
                    find = 0;
                    for(i=0; i<password.length; i++){
                        if (letras.indexOf(password.charAt(i),0)!=-1){
                            find = 1;
                        }
                    }
                }

                if (find == 0) error = 1;
                else {
                    find = 0;
                    for(i=0; i<password.length; i++){
                        if (LETRAS.indexOf(password.charAt(i),0)!=-1){
                            find = 1;
                        }
                    }
                }

                if (find == 0) error = 1;
                else {
                    find = 0;
                    for(i=0; i<password.length; i++){
                        if(forbiddenChars.indexOf(password.charAt(i))!=-1) {
                            find = 1;
                        }
                    }

                    if(find == 1) {
                        error = 3;
                    }
                }
            }

            if (error == 1) {
                alert($("#badpass").val());
            } else if (error == 2) {
                alert($("#samepass").val());
            } else if (error == 3) {
                alert($("#forbiddenchars").val()+' ('+ forbiddenChars.join()+')');
            }

            return error;
        }