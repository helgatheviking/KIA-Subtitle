(function($) {

	/*
	 * SINGLE POST SCREEN
	 * @since 1.0
	 */

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

})(jQuery);

(function($) {

	/*
	 * EDIT SCREEN
	 * @since 1.1
	 */

	$( '.editinline' ).on( 'click', function(){
		// revert Quick Edit menu so that it refreshes properly
		inlineEditPost.revert();
		
		posttitlelabel = $( ':input[name="post_title"]', '.inline-edit-row' ).parents( 'label' ); 
		var tag_id = $( this ).parents( 'tr' ).attr( 'id' );	
		var subtitle = $( '.kia-subtitle', '#' + tag_id ).text();  
		$( ':input[name="subtitle"]', '.inline-edit-row' ).val( subtitle ).parents( 'label' ).insertAfter( posttitlelabel );
	});

})(jQuery);




