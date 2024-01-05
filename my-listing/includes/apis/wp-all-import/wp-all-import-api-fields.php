<?php

namespace MyListing\Apis\Wp_All_Import;

if ( ! defined('ABSPATH') ) {
	exit;
}

function render_field( $field, $values ) {
	// listing title and description are handled in the "Title & Content" section
	if ( in_array( $field->get_key(), ['job_title', 'job_description'], true ) ) {
		return;
	}

	// taxonomies are handled in the "Taxonomies, Categories, Tags" section
	if ( $field->get_type() === 'term-select' ) {
		return;
	}

	$field_name = sprintf('mylisting-addon[%s]', $field->get_key());
	$field_value = ! empty( $values[ $field->get_key() ] ) ? $values[ $field->get_key() ] : '';

	if ( $field->get_prop('is_ui') ) {
		return printf( '<h4>%s</h4>', $field->get_label() );
	}

	// prepare options array for select, multiselect, radio, and checkbox fields
	if ( is_array( $field->get_prop('options') ) ) {
		$enum_values = $field->get_prop('options');
		foreach ( $enum_values as $enum_key => $enum_label ) {
			if ( $enum_key !== $enum_label ) {
				$enum_values[ $enum_key ] = sprintf( '%s <em>(%s)</em>', $enum_label, $enum_key );
			}
		}
	}

	// prepare delimiter input markup for fields that support multiple values
	$delimiter_key = $field->get_key().'__wpai_delimiter';
	$delimiter_markup = sprintf(
		'<input type="text" value="%s" placeholder="," name="mylisting-addon[%s]" style="width:30px;height:30px;">',
		! empty( $values[ $delimiter_key ] ) ? $values[ $delimiter_key ] : ',',
		$delimiter_key
	);

	if ( $field->get_type() === 'text' ) {
		return \PMXI_API::add_field( 'simple', $field->get_label(), [
			'field_name' => $field_name,
			'field_value' => $field_value,
		] );
	}

	if ( $field->get_type() === 'number' ) {
		return \PMXI_API::add_field( 'simple', $field->get_label(), [
			'field_name' => $field_name,
			'field_value' => $field_value,
			'tooltip' => 'Enter a numeric value.',
		] );
	}

	if ( $field->get_type() === 'email' ) {
		return \PMXI_API::add_field( 'simple', $field->get_label(), [
			'field_name' => $field_name,
			'field_value' => $field_value,
			'tooltip' => 'Enter an email address.',
		] );
	}

	if ( $field->get_type() === 'url' ) {
		return \PMXI_API::add_field( 'simple', $field->get_label(), [
			'field_name' => $field_name,
			'field_value' => $field_value,
			'tooltip' => 'Enter a URL address.',
		] );
	}

	if ( $field->get_type() === 'password' ) {
		return \PMXI_API::add_field( 'simple', $field->get_label(), [
			'field_name' => $field_name,
			'field_value' => $field_value,
		] );
	}

	if ( $field->get_type() === 'date' ) {
		return \PMXI_API::add_field( 'simple', $field->get_label(), [
			'field_name' => $field_name,
			'field_value' => $field_value,
			'tooltip' => 'Use any date that can be interpreted with strototime()',
		] );
	}

	if ( $field->get_type() === 'textarea' ) {
		return \PMXI_API::add_field( 'textarea', $field->get_label(), [
			'field_name' => $field_name,
			'field_value' => $field_value,
		] );
	}

	if ( $field->get_type() === 'wp-editor' ) {
		return \PMXI_API::add_field( 'wp_editor', $field->get_label(), [
			'field_name' => $field_name,
			'field_value' => $field_value,
		] );
	}

	if ( $field->get_type() === 'file' ) {
		if ( is_string( $field_value ) ) {
			$field_value = [ 'value' => $field_value ];
		}

		if ( ! is_array( $field_value ) ) {
			$field_value = [];
		}

		return require locate_template('templates/admin/wp-all-import/wp-all-import-ui-file.php');
	}

	if ( $field->get_type() === 'select-product' ) {
		return \PMXI_API::add_field( 'simple', $field->get_label(), [
			'field_name' => $field_name,
			'field_value' => $field_value,
			'tooltip' => 'Enter the title, slug, SKU, or ID of the product.',
		] );
	}

	if ( $field->get_type() === 'select-products' ) {
		\PMXI_API::add_field( 'simple', $field->get_label(), [
			'field_name' => $field_name,
			'field_value' => $field_value,
			'tooltip' => 'Enter the title, slug, SKU, or ID of the product.',
		] );
		printf( '<p>Separate multiple values with %s</p>', $delimiter_markup );
		return;
	}

	if ( $field->get_type() === 'related-listing' ) {
		\PMXI_API::add_field( 'simple', $field->get_label(), [
			'field_name' => $field_name,
			'field_value' => $field_value,
			'tooltip' => 'Enter the title, slug, or ID of the listing.',
		] );

		if ( in_array( $field->get_prop('relation_type'), ['has_many', 'belongs_to_many'], true ) ) {
			printf( '<p>Separate multiple values with %s</p>', $delimiter_markup );
		}
		return;
	}

	if ( $field->get_type() === 'recurring-date' ) {
		if ( ! is_array( $field_value ) || empty( $field_value ) ) {
			$field_value = [
				[ 'start' => '', 'end' => '', ],
			];
		}

		return require locate_template('templates/admin/wp-all-import/wp-all-import-ui-recurring-dates.php');
	}

	if ( $field->get_type() === 'radio' ) {
		return \PMXI_API::add_field( 'enum', $field->get_label(), [
			'field_name' => $field_name,
			'field_value' => $field_value,
			'field_key' => $field->get_key(),
			'addon_prefix' => 'mylisting-addon',
			'enum_values' => $enum_values,
			'mapping' => true,
			'mapping_rules' => isset( $values['mapping'][ $field->get_key() ] ) ? $values['mapping'][ $field->get_key() ] : [],
			'xpath' => isset( $values['xpaths'][ $field->get_key() ] ) ? $values['xpaths'][ $field->get_key() ] : '',
		] );
	}

	if ( $field->get_type() === 'select' ) {
		return \PMXI_API::add_field( 'enum', $field->get_label(), [
			'field_name' => $field_name,
			'field_value' => $field_value,
			'field_key' => $field->get_key(),
			'addon_prefix' => 'mylisting-addon',
			'enum_values' => $enum_values,
			'mapping' => true,
			'mapping_rules' => isset( $values['mapping'][ $field->get_key() ] ) ? $values['mapping'][ $field->get_key() ] : [],
			'xpath' => isset( $values['xpaths'][ $field->get_key() ] ) ? $values['xpaths'][ $field->get_key() ] : '',
		] );
	}

	if ( $field->get_type() === 'multiselect' ) {
		\PMXI_API::add_field( 'simple', $field->get_label(), [
			'field_name' => $field_name,
			'field_value' => $field_value,
			'tooltip' => 'Allowed values: <br>&mdash; '.join( '<br>&mdash; ', $enum_values ),
		] );
		printf( '<p>Separate multiple values with %s</p>', $delimiter_markup );
		return;
	}

	if ( $field->get_type() === 'checkbox' ) {
		\PMXI_API::add_field( 'simple', $field->get_label(), [
			'field_name' => $field_name,
			'field_value' => $field_value,
			'tooltip' => 'Allowed values:<br>&mdash;  '.join( '<br>&mdash; ', $enum_values ),
		] );
		printf( '<p>Separate multiple values with %s</p>', $delimiter_markup );
		return;
	}

	if ( $field->get_type() === 'links' ) {
		if ( ! is_array( $field_value ) ) {
			$field_value = [];
		}

		$method = $field_value['method'] ?? 'default';
		$links = $field_value['links'] ?? [];
		return require locate_template('templates/admin/wp-all-import/wp-all-import-ui-links.php');
	}

	if ( $field->get_type() === 'work-hours' ) {
		if ( ! is_array( $field_value ) ) {
			$field_value = [];
		}

		$weekdays = [
			'mon' => 'Monday',
			'tue' => 'Tuesday',
			'wed' => 'Wednesday',
			'thu' => 'Thursday',
			'fri' => 'Friday',
			'sat' => 'Saturday',
			'sun' => 'Sunday',
		];

		$method = $field_value['method'] ?? 'default';
		return require locate_template('templates/admin/wp-all-import/wp-all-import-ui-work-hours.php');
	}

	if ( $field->get_type() === 'location' ) {
		if ( ! is_array( $field_value ) ) {
			$field_value = [];
		}

		$method = $field_value['method'] ?? 'address';
		return require locate_template('templates/admin/wp-all-import/wp-all-import-ui-location.php');
	}
}

function render_settings( $values ) {
	$values['mapping'] = $values['mapping'] ?? [];
	$values['xpaths'] = $values['xpaths'] ?? [];
	$before = '<div class="field-group">';
	$after = '</div>';

	echo $before;
	\PMXI_API::add_field( 'simple', 'Listing Expiry Date', [
		'field_name' => 'mylisting-addon[_setting_expiry]',
		'field_value' => $values['_setting_expiry'] ?? '',
		'tooltip' => 'Use any date that can be interpreted with strototime()',
	] );
	echo $after;

	echo $before;
	\PMXI_API::add_field( 'simple', 'Paid Listing Package ID', [
		'field_name' => 'mylisting-addon[_setting_payment_package]',
		'field_value' => $values['_setting_payment_package'] ?? '',
		'tooltip' => 'You can view all active packages in <strong>WP Admin > Users > Paid Listing Packages</strong>.',
	] );
	echo $after;

	echo $before;
	\PMXI_API::add_field( 'enum', 'Is listing verified?', [
		'field_name' => 'mylisting-addon[_setting_claimed]',
		'field_key' => '_setting_claimed',
		'field_value' => $values['_setting_claimed'] ?? '',
		'addon_prefix' => 'mylisting-addon',
		'enum_values' => [
			'no' => 'No',
			'yes' => 'Yes',
		],
		'mapping' => true,
		'mapping_rules' => $values['mapping']['_setting_claimed'] ?? [],
		'xpath' => $values['xpaths']['_setting_claimed'] ?? '',
	] );
	echo $after;

	echo $before;
	\PMXI_API::add_field( 'enum', 'Priority', [
		'field_name' => 'mylisting-addon[_setting_priority]',
		'field_key' => '_setting_priority',
		'field_value' => $values['_setting_priority'] ?? '',
		'addon_prefix' => 'mylisting-addon',
		'enum_values' => [
			'0' => 'Normal (0)',
			'1' => 'Featured (1)',
			'2' => 'Promoted (2)',
		],
		'mapping' => true,
		'mapping_rules' => $values['mapping']['_setting_priority'] ?? [],
		'xpath' => $values['xpaths']['_setting_priority'] ?? '',
	] );
	echo $after;
}
