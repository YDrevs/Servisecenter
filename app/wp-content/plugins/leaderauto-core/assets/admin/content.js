/**
 * «Контент сайту» screens: image fields pick from the media library, and
 * repeaters add, remove and reorder rows.
 * An image's hidden input holds an attachment ID; empty means the default image
 * (or none, inside a repeater row).
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

	// Repeaters. Row indexes only need to be unique; the server renumbers on save.
	$( '.la-rows__list' ).sortable( {
		handle: '.la-row__handle',
		axis: 'y',
	} );

	$( document ).on( 'click', '.la-row__handle', function ( e ) {
		e.preventDefault(); // a drag handle, not the <summary> toggle
	} );

	$( document ).on( 'click', '.la-rows__add', function () {
		var $rows = $( this ).closest( '.la-rows' );
		var html = $rows.find( '.la-rows__tpl' ).html().replace( /__i__/g, 'n' + Date.now() );
		$rows.find( '.la-rows__list' ).append( html );
		$rows.find( '.la-row' ).last().find( 'input:not([type=hidden]), textarea' ).first().trigger( 'focus' );
	} );

	$( document ).on( 'click', '.la-row__remove', function ( e ) {
		e.preventDefault();
		var $row = $( this ).closest( '.la-row' );
		if ( window.confirm( 'Видалити «' + $row.find( '.la-row__title' ).text() + '»?' ) ) {
			$row.remove();
		}
	} );

	$( document ).on( 'input', '.la-row__field--title input, .la-row__field--title textarea', function () {
		var $title = $( this ).closest( '.la-row' ).find( '.la-row__title' );
		$title.text( this.value || $title.data( 'placeholder' ) );
	} );
} )( jQuery );
