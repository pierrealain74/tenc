<?php

namespace MyListing\Src\Forms\Fields;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Wp_Editor_Field extends Base_Field {

	public function init() {
		if ( $this->get_type() === 'wp-editor' || ( $this->get_type() === 'texteditor' && $this->get_prop('editor-type') !== 'textarea' ) ) {
			$key = $this->get_key() === 'job_description' ? 'description' : $this->get_key();
			add_filter( 'mylisting/compile-string/unescaped-fields', function( $fields ) use ( $key ) {
				$fields[] = $key;
				return $fields;
			} );

			add_filter( 'mylisting/compile-string-field/'.$key, function( $value ) {
				return wpautop( $value );
			} );
		}
	}

	public function get_posted_value() {
		return isset( $_POST[ $this->key ] )
			? wp_kses_post( trim( stripslashes( $_POST[ $this->key ] ) ) )
			: '';
	}

	public function validate() {
		$value = $this->get_posted_value();
		$this->validateBlackListWords();
		$this->validateMinLength( true );
		$this->validateMaxLength( true );
	}

	public function validateBlackListWords() {
		$value = $this->get_posted_value();
		$message = $this->ml_blacklist_check( $value );
		if ( $message ) {
    		throw new \Exception( esc_html__( 'Disallowed words are used.', 'my-listing' ) );
		}
	}

	function ml_blacklist_check( $value ) {
		$mod_keys = trim( get_option( 'disallowed_keys' ) );
	    if ( '' === $mod_keys ) {
	        return false; // If moderation keys are empty.
	    }

		$words = explode( "\n", $mod_keys );
		foreach ( (array) $words as $word ) {
			$word = trim( $word );

			if ( empty( $word )
			or 256 < strlen( $word ) ) {
				continue;
			}

			$pattern = sprintf( '/\b%s\b/iu', preg_quote( $word, '#' ) );
			if ( preg_match( $pattern, $value ) ) {
				return true;
			}
		}

		return false;
	}

	public function field_props() {
		$this->props['type'] = 'wp-editor';
		$this->props['editor-controls'] = 'basic';
		$this->props['allow-shortcodes'] = false;
		$this->props['minlength'] = '';
		$this->props['maxlength'] = '';
	}

	public function get_editor_options() {
		$this->getLabelField();
		$this->getKeyField();
		$this->getPlaceholderField();
		$this->getDescriptionField();
		$this->getEditorControlsField();
		$this->getAllowShortcodesField();

		$this->getMinLengthField();
		$this->getMaxLengthField();

		$this->getRequiredField();
		$this->getShowInSubmitFormField();
		$this->getShowInAdminField();
		$this->getShowInCompareField();
	}

	protected function getEditorControlsField() { ?>
		<div class="form-group">
			<label class="mb10">Editor Controls</label>
			<label><input type="radio" v-model="field['editor-controls']" value="basic" class="form-radio mb5"> Basic Controls</label>
			<label><input type="radio" v-model="field['editor-controls']" value="advanced" class="form-radio mb5"> Advanced Controls</label>
			<label><input type="radio" v-model="field['editor-controls']" value="all" class="form-radio"> All Controls</label>
		</div>
	<?php }

	protected function getAllowShortcodesField() { ?>
		<div class="form-group">
			<div class="mb5"></div>
			<label><input type="checkbox" v-model="field['allow-shortcodes']" class="form-checkbox"> Allow shortcodes in the editor?</label>
		</div>
	<?php }
}
