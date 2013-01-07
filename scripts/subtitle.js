( function( $ ) {

	/*
	 * SINGLE POST SCREEN
	 * @since 1.0
	 */
	 //@todo: not needed b/c wp3.5 has hook in right place - remove in next version
	$( '#the_subtitle' ).insertAfter( '#title' );

	//smart empty:
	$( '#titlewrap' ).on( 'focus', '#the_subtitle', function(){
		if( $( this ).val() == KIA_Subtitle.subtitle){
			$( this ).val( '' ).removeClass( 'prompt' );
		}

	});

	$('#titlewrap').on('blur', '#the_subtitle', function(){
		if( $( this ).val() == '' ){
			$( this ).val( KIA_Subtitle.subtitle ).addClass( 'prompt' );
		}

	});

})( jQuery );

( function( $ ) {

	/*
	 * EDIT SCREEN
	 * @since 1.1
	 */

	$( '#the-list' ).on( 'click', '.editinline', function(){
		
		// revert Quick Edit menu so that it refreshes properly
		inlineEditPost.revert();
		
		tag_id = $( this ).parents( 'tr' ).attr( 'id' );
		posttitlelabel = $( ':input[name="post_title"]', '.inline-edit-row' ).parents( 'label' ); 			
		subtitle = $( 'div.kia-subtitle-value', '#' + tag_id ).text();  

		//move the subtitle input and set its value from the hidden field
		$( 'input.kia-subtitle-input', '.inline-edit-row' ).val( subtitle ).parents( 'label' ).insertAfter( posttitlelabel );
	});

})( jQuery );




