<?php

if ( ! defined('ABSPATH') ) {
	exit;
}

/**
 * Autoload classes.
 *
 * MyListing\Src      -> my-listing/includes/src
 * MyListing\Ext      -> my-listing/includes/extensions
 * MyListing\Utils    -> my-listing/includes/utils
 * MyListing\Includes -> my-listing/includes
 */
spl_autoload_register( function( $classname ) {
	$parts = explode( '\\', $classname );

	if ( $parts[0] !== 'MyListing' ) {
		return false;
	}

	$parts[0] = 'Includes';

	if ( $parts[1] === 'Ext' ) {
		$parts[1] = 'Extensions';
	}

	$path_parts = array_map( function( $part ) {
		return strtolower( str_replace( '_', '-', $part ) );
	}, $parts );

	$path = join( DIRECTORY_SEPARATOR, $path_parts ) . '.php';

	if ( locate_template( $path ) ) {
		require_once locate_template( $path );
	}
} );

require_once locate_template( 'includes/util.php' );
require_once locate_template( 'includes/init.php' );
require_once locate_template( 'includes/utils/plugin-activator.php' );
