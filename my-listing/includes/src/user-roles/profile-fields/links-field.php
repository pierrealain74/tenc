<?php

namespace MyListing\Src\User_Roles\Profile_Fields;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Links_Field extends Base_Profile_Field {

	protected function get_posted_value() {
		$value = ! empty( $_POST[ $this->get_form_key() ] ) ? (array) $_POST[ $this->get_form_key() ] : [];
		$links = array_map( function( $val ) {
			if ( ! is_array( $val ) || empty( $val['network'] ) || empty( $val['url'] ) ) {
				return false;
			}

			return [
				'network' => sanitize_text_field( stripslashes( $val['network'] ) ),
				'url' => esc_url_raw( $val['url'] ),
			];
		}, $value );

		return array_filter( $links );
	}

	protected function validate() {
		$value = $this->the_posted_value();
		//
	}

	protected function field_props() {
		$this->props['type'] = 'links';
	}

	protected function get_editor_options() {
		$this->get_label_option();
		$this->get_description_option();
		$this->get_required_option();
		$this->get_show_in_register_option();
		$this->get_show_in_account_details_option();
	}

	public function get_form_markup() {
		$allowed_networks = \MyListing\Src\Forms\Fields\Links_Field::allowed_networks(); ?>
		<div class="repeater social-networks-repeater"
			data-list="<?php echo c27()->encode_attr( $this->the_posted_value() ?: $this->get_value() ) ?>">
			<p><?php echo $this->get_label() ?></p>
			<div data-repeater-list="<?php echo esc_attr( $this->get_form_key() ) ?>">
				<div data-repeater-item>
					<select name="network" class="ignore-custom-select">
						<option value=""><?php _ex( 'Select Network', 'Listing social networks', 'my-listing' ) ?></option>
						<?php foreach ( $allowed_networks as $network ): ?>
							<option value="<?php echo esc_attr( $network['key'] ) ?>">
								<?php echo esc_attr( $network['name'] ) ?>
							</option>
						<?php endforeach ?>
					</select>
					<input type="text" name="url" placeholder="<?php esc_attr_e( 'Enter URL...', 'my-listing' ) ?>">
					<button data-repeater-delete type="button" class="buttons button-5 icon-only small">
						<i class="material-icons delete"></i>
					</button>
				</div>
			</div>
			<input data-repeater-create type="button" value="<?php esc_attr_e( 'Add', 'my-listing' ) ?>">
		</div>
		<?php if ( $desc = $this->get_description() ): ?>
			<p><?php echo $desc ?></p>
		<?php endif ?>
		<?php
	}
}
