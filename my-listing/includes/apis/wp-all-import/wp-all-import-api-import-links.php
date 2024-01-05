<?php

namespace MyListing\Apis\Wp_All_Import;

if ( ! defined('ABSPATH') ) {
	exit;
}

function import_links( $field, $field_value, $log ) {
	$method = $field_value['method'] ?? 'default';

	if ( $method === 'default' ) {
		$links = $field_value['links'] ?? [];
		$valid_links = [];
		foreach ( $links as $key => $url ) {
			if ( ! empty( $key ) && ! empty( $url ) ) {
				$valid_links[] = [
					'network' => $key,
					'url' => $url,
				];
			}
		}

		return update_post_meta( $field->listing->get_id(), '_'.$field->get_key(), $valid_links );
	}

	if ( $method === 'serialized' ) {
		$links = @unserialize( $field_value['serialized'] ?? null );
		if ( ! ( $links && is_array( $links ) ) ) {
			$log( sprintf(
				'<strong>WARNING:</strong> Invalid serialized value supplied for "%s", skipping.',
				$field->get_label()
			) );
			return;
		}

		return update_post_meta( $field->listing->get_id(), '_'.$field->get_key(), $links );
	}
}
