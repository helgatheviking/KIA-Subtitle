jQuery(document).ready(function($){
	
	$('#the_subtitle').insertAfter('#title');

	//smart empty:
	$('#titlewrap').on('focus', '#the_subtitle', function(){
		if($(this).val() == KIA_Subtitle.subtitle){
			$(this).val('').removeClass('prompt');
		}

	});

	$('#titlewrap').on('blur', '#the_subtitle', function(){
		if($(this).val() == ''){
			$(this).val(KIA_Subtitle.subtitle).addClass('prompt');
		}

	});

	/*
	$('#the_subtitle').click(function(){
		if($(this).val() == 'Subtitle'){
			$(this).val('');
			$(this).focus();
			$(this).bind('blur', function(){
				if($(this).val() == ''){
					$(this).val('Subtitle');
				}
			})
		}
#BBB
*/
});




