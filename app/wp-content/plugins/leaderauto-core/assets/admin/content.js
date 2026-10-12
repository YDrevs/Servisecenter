/**
 * «Контент сайту» screens: image fields pick from the media library.
 * The hidden input holds an attachment ID; empty means the theme's default image.
 */
( function ( $ ) {
	$( document ).on( 'click', '.la-image__pick', function () {
		var $box = $( this ).closest( '.la-image' );
		var frame = wp.media( {
			title: 'Оберіть зображення',
			button: { text: 'Використати' },
			library: { type: 'image' },
			multiple: false,
		} );

		frame.on( 'select', function () {
			var file = frame.state().get( 'selection' ).first().toJSON();
			var url = file.sizes && file.sizes.medium ? file.sizes.medium.url : file.url;
			$box.find( 'input' ).val( file.id );
			$box.find( '.la-image__preview' ).attr( 'src', url );
			$box.find( '.la-image__reset' ).prop( 'hidden', false );
		} );

		frame.open();
	} );

	$( document ).on( 'click', '.la-image__reset', function () {
		var $box = $( this ).closest( '.la-image' );
		$box.find( 'input' ).val( '' );
		$box.find( '.la-image__preview' ).attr( 'src', $box.data( 'default-url' ) );
		$( this ).prop( 'hidden', true );
	} );
} )( jQuery );
