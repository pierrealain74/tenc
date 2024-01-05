<?php

namespace MyListing\Src\Forms\Fields;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Url_Field extends Base_Field {

	public function get_posted_value() {
		return ! empty( $_POST[ $this->key ] )
			? esc_url_raw( $_POST[ $this->key ] )
			: '';
	}

	public function validate() {
		$value = $this->get_posted_value();
		if ( preg_match( '@^(https?|ftp)://[^\s/$.?#].[^\s]*$@iS', $value ) !== 1 ) {
			// translators: Placeholder %s is the label for the required field.
			throw new \Exception( sprintf( _x( '%s must be a valid url address.', 'Add listing form', 'my-listing' ), $this->props['label'] ) );
		}
	}

	public function field_props() {
		$this->props['type'] = 'url';
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
	}
}