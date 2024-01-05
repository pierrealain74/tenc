<?php

namespace MyListing\Src\Forms\Fields;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Checkbox_Field extends Base_Field {

	public $modifiers = [
		'labels' => '%s Label(s)',
	];

	public function get_posted_value() {
		return isset( $_POST[ $this->key ] )
			? array_map( 'sanitize_text_field', $_POST[ $this->key ] )
			: [];
	}

	public function validate() {
		$value = $this->get_posted_value();

		// maintain backwards compatibility (checkboxes only had valud of 1 or 0).
		if ( empty( $this->props['options'] ) && ( count( $value ) !== 1 || (string) $value[0] !== '1' ) ) {
			// translators: %s is the field label.
			throw new \Exception( sprintf( _x( 'Invalid value supplied for %s.', 'Add listing form', 'my-listing' ), $this->props['label'] ) );
		} else {
			$this->validateSelectedOption();
		}
	}

	public function field_props() {
		$this->props['type'] = 'checkbox';
		$this->props['options'] = new \stdClass; // when encoded to json, it needs to be {} instead of [].
	}

	public function get_editor_options() {
		$this->getLabelField();
		$this->getKeyField();
		$this->getPlaceholderField();
		$this->getDescriptionField();
		$this->getRequiredField();
		$this->getShowInSubmitFormField();
		$this->getShowInAdminField();
		$this->getShowInCompareField();
		$this->getOptionsField();
	}

	public function string_value( $modifier = null ) {
		$selected = (array) $this->get_value();
		$options = $this->get_prop('options');
		$validated = [];

		// validate selected options and retrieve their labels
		foreach ( $selected as $value ) {
			if ( isset( $options[ $value ] ) ) {
				$validated[ $value ] = $options[ $value ];
			}
		}

		if ( $modifier === 'labels' ) {
			return join( ', ', $validated );
		}

		return join( ', ', array_keys( $validated ) );
	}
}
