<script>
	$(document).ready(function() {

		$("#slct_region").change(function() {
			$(':text[id*=region]').val( $("#s2id_slct_region .select2-chosen").text() );
			var choices = $('.select2-choice');

			populate_city();
		});

		$("#slct_city").change(function(){
			$(':text[id*=city]').val( $("#s2id_slct_city .select2-chosen").text() );
		});

		$("#slct_city").select2();
            if($(':text[id*=city]').val()   != '') $("#s2id_slct_city .select2-chosen").text($(':text[id*=city]').val());
            else                                   $(':text[id*=city]').val( $("#s2id_slct_city .select2-chosen").text($('#no-city').val()));

            var entity_region = $('form').find('#entity_region').val();
            var entity_city   = $('form').find('#entity_city').val();

            if (entity_region != '' ){
                                        $("#s2id_slct_region .select2-chosen").text(entity_region);
                                        $(':text[id*=region]').val(entity_region);
            }
            if (entity_city   != '' ){
            							$("#s2id_slct_city .select2-chosen"  ).text(entity_city);
                                        $(':text[id*=city]'  ).val(entity_city);
            }
		$('#btn_create').click(function(){

			if ($(':text[id*=region]').val() == "[object Object]"){

				alert('Debes introducir la region');
		        event.preventDefault();
		        event.stopPropagation();
			}
			else{

				if ($(':text[id*=city]').val() == "[object Object]"){

				alert('Debes introducir la ciudad');
			        event.preventDefault();
			        event.stopPropagation();
			    }
			}
		});
	});
</script>