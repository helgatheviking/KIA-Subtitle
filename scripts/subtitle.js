jQuery(document).ready(function($){
	
	$('#the_subtitle').insertAfter('#title');

	//smart empty:
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
	})
})