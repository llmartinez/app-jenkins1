
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