(function( $ ) {
	function initialize_code_field( $el ) {
		if ( $el.parents( ".acf-clone" ).length > 0 ) {
			return;
		}

		var $textarea = $el.find( '.acf-input>textarea' );
		var editor = window.CodeMirror.fromTextArea( $textarea[ 0 ], {
			lineNumbers: true,
			fixedGutter: false,
			mode: $textarea.attr( "mode" ),
			theme: $textarea.attr( "theme" ),
			extraKeys: { "Ctrl-Space": "autocomplete" },
			matchBrackets: true,
			styleSelectedText: true,
			autoRefresh: true,
			value: document.documentElement.innerHTML,
			viewportMargin: Infinity
		} );

		editor.refresh();
	}

	if ( typeof acf.add_action !== 'undefined' ) {
		acf.add_action( 'ready', function( $el ) {
			$el.find( ".acf-field-cts-code-field" ).each( function( index, field ) {
				initialize_code_field( $( field ) );
			} );
		} );

		acf.add_action( 'append_field', function( $el ) {
			if ( $el.attr( 'data-type' ) == "cts_code_field" ) {
				initialize_code_field( $el );
			}
		} );
	}
})( jQuery );
