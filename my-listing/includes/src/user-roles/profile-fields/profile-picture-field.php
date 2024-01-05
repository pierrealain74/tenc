<?php

namespace MyListing\Src\User_Roles\Profile_Fields;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Profile_Picture_Field extends Base_Profile_Field {

	protected function get_posted_value() {
		if ( $this->form === static::FORM_REGISTER ) {
			return $this->get_registration_posted_value();
		}

		$value = isset( $_POST[ 'current_picture' ] ) ? $_POST[ 'current_picture' ] : '';
		if ( substr( $value, 0, 4 ) === 'b64:' ) {
			$value = base64_decode( str_replace( 'b64:', '', $value ), true );
		}

		if ( ! is_string( $value ) || empty( $value ) ) {
			return null;
		}

		return esc_url_raw( $value, [ 'http', 'https' ] );
	}

	protected function get_registration_posted_value() {
		return isset( $_FILES['profile_picture'] ) ? (array) $_FILES['profile_picture'] : false;
	}

	protected function validate() {
		if ( $this->form === static::FORM_REGISTER ) {
			return $this->validate_registration_form();
		}
		$value = $this->the_posted_value();
		$file_info = wp_check_filetype( current( explode( '?', $value ) ) );
		if ( ! ( $file_info && in_array( $file_info['ext'], [ 'jpg', 'gif', 'png', 'jpeg', 'jpe' ], true ) ) ) {
			throw new \Exception( sprintf(
				_x( '"%1$s" must be an image', 'Add listing form', 'my-listing' ),
				$this->props['label'],
				$file_info['ext']
			) );
		}
	}

	protected function validate_registration_form() {
		$value = $this->the_posted_value();
		if ( ! $value || empty( $value['tmp_name'] ) ) {
			if ( $this->is_required() ) {
				throw new \Exception( sprintf(
					_x( '%s is a required field.', 'User details', 'my-listing' ),
					$this->get_label()
				) );
			}
			return;
		}

		$file_info = wp_check_filetype( $value['name'] );
		if ( ! ( $file_info && in_array( $file_info['ext'], [ 'jpg', 'gif', 'png', 'jpeg', 'jpe' ], true ) ) ) {
			throw new \Exception( sprintf(
				_x( '"%1$s" must be an image', 'Add listing form', 'my-listing' ),
				$this->props['label'],
				$file_info['ext']
			) );
		}
	}

	public function update() {
		if ( $this->form === static::FORM_REGISTER ) {
			return $this->update_registration_form();
		}

    	global $wpdb;

		$value = $this->the_posted_value();

    	// remove avatar if empty
    	if ( empty( $value ) ) {
	        delete_user_meta( $this->user->ID, '_mylisting_profile_photo' );
	        delete_user_meta( $this->user->ID, '_mylisting_profile_photo_url' );
    		return;
    	}

		// validate attachment
		$attachment = $wpdb->get_row( $wpdb->prepare( "
			SELECT ID, post_parent, post_status FROM {$wpdb->posts}
				WHERE post_type = 'attachment' AND guid = %s
				AND post_author = %d LIMIT 1
		", $value, get_current_user_id() ) );

		if ( ! is_object( $attachment ) || empty( $attachment->ID ) ) {
			return;
		}

		// validate image exists
        $photo_url = wp_get_attachment_image_url( $attachment->ID, 'thumbnail' );
        if ( ! $photo_url ) {
        	return;
        }

        // update attachment status from preview to inherit
		wp_update_post( [
			'ID' => $attachment->ID,
			'post_status' => 'inherit',
		] );

		// update user avatar metadata
        update_user_meta( $this->user->ID, '_mylisting_profile_photo', $attachment->ID );
        update_user_meta( $this->user->ID, '_mylisting_profile_photo_url', $photo_url );
	}

	protected function update_registration_form() {
		$value = $this->the_posted_value();
		if ( empty( $value ) ) {
			return;
		}

		try {
			$file_uploader = \MyListing\Utils\File_Uploader::instance();
			// $p = $file_uploader->prepare( 'profile_picture' )[0];
			// dd($p['type'], in_array($p['type'], ['image/jpeg', 'image/png', 'image/gif']));
			$file = $file_uploader->upload( $file_uploader->prepare( 'profile_picture' )[0], [
				'allowed_mime_types' => ['image/jpeg', 'image/png', 'image/gif'],
			] );
		} catch ( \Exception $e ) {
			return;
		}

		$attachment_id = wp_insert_attachment( [
			'post_title' => $file->name,
			'post_mime_type' => $file->type,
			'guid' => $file->url,
		], $file->file );

		if ( ! $attachment_id || is_wp_error( $attachment_id ) ) {
			return;
		}

		// generate attachment
		wp_update_attachment_metadata(
			$attachment_id,
			wp_generate_attachment_metadata( $attachment_id, $file->file )
		);

        $photo_url = wp_get_attachment_image_url( $attachment_id, 'thumbnail' );

		// update user avatar metadata
        update_user_meta( $this->user->ID, '_mylisting_profile_photo', $attachment_id );
        update_user_meta( $this->user->ID, '_mylisting_profile_photo_url', $photo_url );
	}

	protected function field_props() {
		$this->props['type'] = 'profile-picture';
		$this->props['allowed_mime_types'] = new \stdClass;
	}

	protected function get_editor_options() {
		$this->get_label_option();
		$this->get_description_option();
		$this->get_required_option();
		$this->get_show_in_register_option();
		$this->get_show_in_account_details_option();
	}

	public function get_form_markup() {
		if ( $this->form === static::FORM_REGISTER ) {
			return $this->get_registration_form_markup();
		}

		wp_enqueue_script( 'mylisting-ajax-file-upload' );

		$photo = get_user_meta( get_current_user_id(), '_mylisting_profile_photo', true );
    	$photo_url = get_user_meta( get_current_user_id(), '_mylisting_profile_photo_url', true );
		$allowed_mime_types = [ 'jpg', 'gif', 'png', 'jpeg', 'jpe' ];
		?>

		<?php do_action( 'mylisting/account-details/before-profile-picture' ) ?>

		<fieldset id="change-avatar-fieldset">
			<legend><?php _e( 'Change profile picture', 'my-listing' ) ?></legend>
			<div class="file-upload-field single-upload form-group-review-gallery ajax-upload">
				<div class="uploaded-files-list review-gallery-images">
					<label class="upload-file review-gallery-add" for="mylisting_profile_photo">
						<i class="mi file_upload"></i>
						<div class="content">
							<input
								type="file"
								class="input-text review-gallery-input wp-job-manager-file-upload"
								data-file_types="<?php echo esc_attr( implode( '|', $allowed_mime_types ) ) ?>"
								name="picture"
								id="mylisting_profile_photo"
							>
						</div>
					</label>

					<div class="job-manager-uploaded-files">
						<?php if ( $photo && ! empty( $photo_url ) ): ?>
							<?php mylisting_locate_template( 'templates/add-listing/form-fields/uploaded-file-html.php', [
								'key' => 'mylisting_profile_photo',
								'name' => 'current_picture',
								'value' => $photo_url,
							] ) ?>
						<?php endif ?>
					</div>
				</div>

				<small class="description">
					<?php printf( _x( 'Maximum file size: %s.', 'Add listing form', 'my-listing' ), size_format( wp_max_upload_size() ) ); ?>
				</small>
			</div>
		</fieldset>
	<?php }

	private function get_registration_form_markup() { ?>
		<div class="form-group picture-field">
        	<label><?php echo $this->get_label() ?></label>
			<div class="review-gallery-images">
				<label class="review-gallery-add">
					<i class="material-icons file_upload"></i>
					<input type="file" name="profile_picture" accept=".jpg,.jpeg,.png,.gif">
				</label>
				<div class="picture-preview"></div>
			</div>
			<?php if ( $desc = $this->get_description() ): ?>
				<p><?php echo $desc ?></p>
			<?php endif ?>
		</div>
	<?php }
}
