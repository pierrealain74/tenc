<?php

namespace MyListing\Src\Forms\Fields;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Number_Field extends Base_Field {

	public $modifiers = [
		'format' => '%s Formatted',
	];

	public function init() {
		if ( $this->props['content_lock'] ) {
			$key = $this->get_key();

			add_filter( 'mylisting/compile-string/unescaped-fields', function( $fields ) use ( $key ) {
				$fields[] = $key;
				return $fields;
			} );

			add_filter( 'mylisting/compile-string-field/'.$key, function( $value, $field ) {
				return sprintf( '<div class="c27-display-button" data-listing-id="%1$s" data-field-id="%2$s">%3$s</div>', $field->listing->get_id(), $field->get_key(), esc_html__( 'Show Number', 'my-listing' ) );
			}, 50, 2 );
		}
	}

	public function get_posted_value() {
		return isset( $_POST[ $this->key ] )
			? sanitize_text_field( stripslashes( $_POST[ $this->key ] ) )
			: '';
	}

	public function validate() {
		$value = $this->get_posted_value();

		// validate it's a number
		if ( ! is_numeric( $value ) ) {
			// translators: %s is the field label.
			throw new \Exception( sprintf( _x( '%s must be a number.', 'Add listing form', 'my-listing' ), $this->props['label'] ) );
		}

		$val  = (float) $value;
		$min  = is_numeric( $this->props['min'] ) ? (float) $this->props['min'] : false;
		$max  = is_numeric( $this->props['max'] ) ? (float) $this->props['max'] : false;
		$step = is_numeric( $this->props['step'] ) ? (float) $this->props['step'] : false;

		if ( $min !== false && $val < $min ) {
			// translators: %1$s is the field label; %2%s is the minimum allowed value.
			throw new \Exception( sprintf( _x( '%1$s can\'t be smaller than %2$s.', 'Add listing form', 'my-listing' ), $this->props['label'], $min ) );
		}

		if ( $max !== false && $val > $max ) {
			// translators: %1$s is the field label; %2%s is the maximum allowed value.
			throw new \Exception( sprintf( _x( '%1$s can\'t be bigger than %2$s.', 'Add listing form', 'my-listing' ), $this->props['label'], $min ) );
		}
	}

	public function field_props() {
		$this->props['type'] = 'number';
		$this->props['min']  = '';
		$this->props['max']  = '';
		$this->props['step'] = 1;
		$this->props['content_lock'] = false;
	}

	public function get_editor_options() {
		$this->getLabelField();
		$this->getKeyField();
		$this->getPlaceholderField();
		$this->getDescriptionField();

		$this->getMinField();
		$this->getMaxField();
		$this->getStepField();

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

	public function string_value( $modifier = null ) {
		if ( $modifier === 'format' ) {
			return number_format_i18n( $this->get_value() );
		}

		return $this->get_value();
	}
}