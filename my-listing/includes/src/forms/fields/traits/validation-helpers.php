<?php
/**
 * Helper functions for common validation rules for listings fields.
 *
 * @since 1.0
 */

namespace MyListing\Src\Forms\Fields\Traits;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait Validation_Helpers {

	/**
	 * Common validation rule among field with an option list,
	 * e.g. select, multiselect, checkbox, and radio fields.
	 *
	 * @since 2.1
	 */
	public function validateSelectedOption() {
		$value = $this->get_posted_value();
		$has_options = is_array( $this->props['options'] ) && ! empty( $this->props['options'] );

		foreach ( (array) $value as $option ) {
			if ( $has_options && ! in_array( $option, array_keys( $this->props['options'] ) ) ) {
				// translators: %s is the field label.
				throw new \Exception( sprintf( _x( 'Invalid value supplied for %s.', 'Add listing form', 'my-listing' ), $this->props['label'] ) );
			}
		}
	}

	public function validateMinLength( $strip_tags = false ) {
		$value = $this->get_posted_value();
		if ( $strip_tags ) {
			$value = wp_strip_all_tags( $value );
		}

		if ( is_numeric( $this->props['minlength'] ) && mb_strlen( $value ) < $this->props['minlength'] ) {
			// translators: %1$s is the field label; %2%s is the minimum characters allowed.
			throw new \Exception( sprintf(
				_x( '%1$s can\'t be shorter than %2$s characters.', 'Add listing form', 'my-listing' ),
				$this->props['label'],
				absint( $this->props['minlength'] )
			) );
		}
	}

	public function validateMaxLength( $strip_tags = false ) {
		$value = $this->get_posted_value();
		if ( $strip_tags ) {
			$value = wp_strip_all_tags( $value );
		}

		if ( is_numeric( $this->props['maxlength'] ) && mb_strlen( $value ) > $this->props['maxlength'] ) {
			// translators: %1$s is the field label; %2%s is the maximum characters allowed.
			throw new \Exception( sprintf(
				_x( '%1$s can\'t be longer than %2$s characters.', 'Add listing form', 'my-listing' ),
				$this->props['label'],
				absint( $this->props['maxlength'] )
			) );
		}
	}

}