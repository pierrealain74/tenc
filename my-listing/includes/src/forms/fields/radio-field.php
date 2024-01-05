<?php

namespace MyListing\Src\Forms\Fields;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Radio_Field extends Base_Field {

	public $modifiers = [
		'label' => '%s Label',
	];

	public function get_posted_value() {
		return isset( $_POST[ $this->key ] )
			? sanitize_text_field( stripslashes( $_POST[ $this->key ] ) )
			: '';
	}

	public function validate() {
		$value = $this->get_posted_value();
		$this->validateSelectedOption();
	}

	public function field_props() {
		$this->props['type'] = 'radio';
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
		$value = $this->get_value();
		$options = $this->get_prop('options');
		$selected = [ 'value' => '', 'label' => '' ];

		if ( isset( $options[ $value ] ) ) {
			$selected['value'] = $value;
			$selected['label'] = $options[ $value ];
		}

		if ( $modifier === 'label' ) {
			return $selected['label'];
		}

		return $selected['value'];
	}
}