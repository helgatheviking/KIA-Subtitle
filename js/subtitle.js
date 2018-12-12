;( function( $ ) {

	/*
	 * SINGLE POST SCREEN
	 * @since 1.0
	 */
	// Move to just after the title
	$( '#the_subtitle' ).insertAfter( '#title' );

	// props to Giuseppe Mazzapica for this tabbing script
	$(document).on( 'keydown', '#title, #the_subtitle', function( e ) {
		var keyCode = e.keyCode || e.which;
		if ( 9 == keyCode){
			e.preventDefault();
			var target = $(this).attr('id') == 'title' ? '#the_subtitle' : 'textarea#content';
			if ( (target === '#the_subtitle') || $('#wp-content-wrap').hasClass('html-active') ) {
				$(target).focus();
			} else {
				tinymce.execCommand('mceFocus',false,'content');
			}
		}
	});

	/*
	 * EDIT SCREEN
	 * @since 1.1
	 */

	$( '#the-list' ).on( 'click', '.editinline', function(){

		// Revert Quick Edit menu so that it refreshes properly
		inlineEditPost.revert();

		tag_id = $( this ).parents( 'tr' ).attr( 'id' );
		posttitlelabel = $( ':input[name="post_title"]', '.inline-edit-row' ).parents( 'label' );
		subtitle = $( 'div.kia-subtitle-value', '#' + tag_id ).text();

		// Move the subtitle input and set its value from the hidden field
		$( 'input.kia-subtitle-input', '.inline-edit-row' ).val( subtitle ).parents( 'label' ).insertAfter( posttitlelabel );

	});

})( jQuery );

