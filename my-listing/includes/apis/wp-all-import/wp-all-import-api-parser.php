<?php

namespace MyListing\Apis\Wp_All_Import;

if ( ! defined('ABSPATH') ) {
	exit;
}

function parser( $data ) {
	$parsed = [];
	$import = $data['import'];
	$addon = $import->options['mylisting-addon'] ?? [];
	$file = $data['file'] ?? null;
	$xml = $data['xml'];
	$prefix = $data['xpath_prefix'].$import->xpath;
	$type = \MyListing\Src\Listing_Type::get_by_name( $import->options['listing_type'] ?? null );

	if ( ! $type ) {
		return;
	}

	// retrieve indexes of parsed rows so we can sanely organize all parsed
	// field data into groups based on the row index, despite the performance penalty
	$indexes = array_keys( \XmlImportParser::factory( $xml, $prefix, '__dummy', $file )->parse() );
	unlink( $file );

	foreach ( $type->get_fields() as $field ) {
		if ( empty( $addon[ $field->get_key() ] ) ) {
			continue;
		}

		$is_xpath = $addon[ $field->get_key() ] === 'xpath' && ! empty( $addon['xpaths'][ $field->get_key() ] );
		$value = $is_xpath ? $addon['xpaths'][ $field->get_key() ] : $addon[ $field->get_key() ];
		$delimiter_key = $field->get_key().'__wpai_delimiter';

		foreach ( $indexes as $index ) {
			$parsed[ $index ][ $field->get_key() ] = parse_recursively( $xml, $prefix, $value, $file, $index );
			unlink( $file );

			if ( isset( $addon[ $delimiter_key ] ) ) {
				$parsed[ $index ][ $delimiter_key ] = $addon[ $delimiter_key ];
			}
		}
	}

	$settings = [ '_setting_expiry', '_setting_payment_package', '_setting_claimed', '_setting_priority' ];
	foreach ( $settings as $setting_key ) {
		if ( empty( $addon[ $setting_key ] ) ) {
			continue;
		}

		$is_xpath = $addon[ $setting_key ] === 'xpath' && ! empty( $addon['xpaths'][ $setting_key ] );
		$value = $is_xpath ? $addon['xpaths'][ $setting_key ] : $addon[ $setting_key ];

		foreach ( $indexes as $index ) {
			$parsed[ $index ][ $setting_key ] = parse_recursively( $xml, $prefix, $value, $file, $index );
			unlink( $file );
		}
	}

	return $parsed;
}

function parse_recursively( $xml, $prefix, $value, &$file, $index ) {
	if ( is_array( $value ) ) {
		$output = [];
		foreach ( $value as $key => $subvalue ) {
			if ( ! empty( $subvalue ) ) {
				$output[ $key ] = parse_recursively( $xml, $prefix, $subvalue, $file, $index );
			}
		}

		return $output;
	}

	$result = \XmlImportParser::factory( $xml, $prefix, $value, $file )->parse();
	return $result[ $index ] ?? null;
}
