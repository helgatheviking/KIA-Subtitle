;( function( $ ) {

	/*
	 * SINGLE POST SCREEN
	 * @since 1.0
	 */
	// move to just after the title
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


})( jQuery );

