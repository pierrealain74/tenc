<?php

namespace MyListing\Src\Forms\Fields;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Email_Field extends Base_Field {

	public function init() {
		if ( $this->props['content_lock'] ) {
			$key = $this->get_key();
			if ( $key == 'job_email' ) {
				$key = 'email';
			}

			add_filter( 'mylisting/compile-string/unescaped-fields', function( $fields ) use ( $key ) {
				$fields[] = $key;
				return $fields;
			} );

			add_filter( 'mylisting/compile-string-field/'.$key, function( $value, $field ) {
				return sprintf( '<div class="c27-display-button" data-listing-id="%1$s" data-field-id="%2$s">%3$s</div>', $field->listing->get_id(), $field->get_key(), esc_html__( 'Show Email', 'my-listing' ) );
			}, 50, 2 );
		}
	}

	public function get_posted_value() {
		return isset( $_POST[ $this->key ] )
			? sanitize_email( $_POST[ $this->key ] )
			: '';
	}

	public function validate() {
		$value = $this->get_posted_value();
		if ( ! filter_var( $value, FILTER_VALIDATE_EMAIL ) ) {
			// translators: Placeholder %s is the label for the required field.
			throw new \Exception( sprintf( _x( '%s must be a valid email address.', 'Add listing form', 'my-listing' ), $this->props['label'] ) );
		}
	}

	public function field_props() {
		$this->props['type'] = 'email';
		$this->props['content_lock'] = false;
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
		$this->getContentLockField();
	}

	protected function getContentLockField() { ?>
		<div class="form-group">
			<label>Content Lock</label>
			<label class="form-switch mb0">
				<input type="checkbox" v-model="field.content_lock">
				<span class="switch-slider"></span>
			</label>
		</div>
	<?php }

}