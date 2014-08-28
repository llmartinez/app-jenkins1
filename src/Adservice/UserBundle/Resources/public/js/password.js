
    $(document).ready(function() {
        $('#confirm_password').click(function(){

            var button = document.querySelector('#confirm_password');
            var data   = button.dataset;
            var url    = data.url;

            new_pass = $('#new_password1').val();
            rep_pass = $('#new_password2').val();
            old_pass = $('#old_password' ).val();

            if(new_pass == '') new_pass = 'none';
            if(rep_pass == '') rep_pass = 'none';
            if(old_pass == '') old_pass = 'none';

            url = url.replace('plc_new_pass', new_pass);
            url = url.replace('plc_rep_pass', rep_pass);
            url = url.replace('plc_old_pass', old_pass);
            window.open(url,'_self');
        });
    });