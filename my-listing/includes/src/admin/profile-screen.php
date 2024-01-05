<?php

namespace MyListing\Src\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Profile_Screen {
	use \MyListing\Src\Traits\Instantiatable;

	public function __construct() {
        // default avatar
		add_filter( 'pre_get_avatar_data', [ $this, 'set_initials_avatar' ], 20, 2 );
		add_filter( 'bp_core_avatar_default', [ $this, 'set_initials_avatar_bp' ], 20, 2 );
        add_filter( 'avatar_defaults', function( $avatars ) {
        	$avatars['mylisting_user_initials'] = sprintf( __( 'User Initials (Generated through %s)', 'my-listing' ), '<a href="https://ui-avatars.com/" target="_blank">UI Avatars</a>' );
        	return $avatars;
        } );

        if ( apply_filters( 'mylisting/enable-user-avatars', true ) !== false ) {
	        add_filter( 'user_profile_picture_description', [ $this, 'admin_add_avatar_setting' ], 30, 2 );
	        add_action( 'user_profile_update_errors', [ $this, 'admin_update_avatar' ], 30, 3 );
		}
	}

	/**
	 * Add avatar setting markup in WP Admin > Users > User Profile.
	 *
	 * @since 2.1
	 */
	public function admin_add_avatar_setting( $description, $user ) {
        wp_enqueue_media();
        $photo = get_user_meta( $user->ID, '_mylisting_profile_photo', true );
        $photo_url = get_user_meta( $user->ID, '_mylisting_profile_photo_url', true );
        ?>
        <div class="update-profile-photo">
            <button class="button change-photo"><?php _e( 'Change photo', 'my-listing' ) ?></button>
            <button class="button remove-photo"><i class="material-icons">delete_outline</i></button>
            <input type="hidden" name="mylisting_profile_photo" value="<?php echo ! empty( $photo ) ? absint( $photo ) : '' ?>"
            	data-url="<?php echo esc_url( $photo_url ) ?>" data-default="<?php echo esc_url( get_avatar_url( $user->ID, [ 'force_default' => true ] ) ) ?>">
            <div class="uploaded-image-preview"></div>
        </div>
    <?php }

	/**
	 * Handles the avatar setting in WP Admin > Users > User Profile.
	 *
	 * @since 2.1
	 */
    public function admin_update_avatar( $errors, $update, $user ) {
        if ( ! $update ) {
            return;
        }

        $photo = ! empty( $_POST['mylisting_profile_photo'] ) ? absint( $_POST['mylisting_profile_photo'] ) : '';
        $photo_url = wp_get_attachment_image_url( $photo, 'thumbnail' );
        if ( $photo && $photo_url ) {
	        update_user_meta( $user->ID, '_mylisting_profile_photo', $photo );
	        update_user_meta( $user->ID, '_mylisting_profile_photo_url', $photo_url );
        } else {
	        delete_user_meta( $user->ID, '_mylisting_profile_photo' );
	        delete_user_meta( $user->ID, '_mylisting_profile_photo_url' );
        }
    }

	/**
	 * Sets the default avatar of user initials, using ui-avatars.
	 *
	 * @since 2.1
	 */
	public function set_initials_avatar( $args, $id_or_email ) {
		if ( $args['default'] !== 'mylisting_user_initials' ) {
			 return $args;
		}

		if ( ! ( $user = c27()->get_user_by_id_or_email( $id_or_email ) ) ) {
			if ( $id_or_email instanceof \WP_Comment ) {
				$user = (object) [
					'ID' => strlen( $id_or_email->comment_author_email ),
					'user_login' => $id_or_email->comment_author_email,
					'display_name' => $id_or_email->comment_author,
				];
			} else {
				$args['default'] = 'mm';
				return $args;
			}
		}

		$args['default'] = $this->generate_ui_avatar( $user );
		return $args;
	}

	/**
	 * Sets the default avatar of user initials in BuddyPress.
	 *
	 * @since 2.1
	 */
	public function set_initials_avatar_bp( $default, $args ) {
		if ( $default !== 'mylisting_user_initials' ) {
			 return $default;
		}

		if ( ! ( $args['object'] === 'user' && ( $user = c27()->get_user_by_id_or_email( $args['item_id'] ) ) ) ) {
			return 'mm';
		}

		return $this->generate_ui_avatar( $user );
	}

	/**
	 * Generates the ui-avatars url for a given user.
	 *
	 * The Gravatar request can't handle non-latin characters in the "d=" parameter
	 * for the default image. If the user's display name only has non-latin
	 * characters, it will result in an empty string and no image will be returned.
	 *
	 * To avoid that, we append the user login as fallback.
	 * The user login will always contain only latin characters.
	 *
	 * @since 2.1
	 */
	private function generate_ui_avatar( $user ) {
		$colors = [
			'f19066', 'f5cd79', '546de5', 'e15f41', 'c44569',
			'574b90', 'f78fb3', '3dc1d3', 'e66767', '303952',
		];

		$query_args = [
			'name' => $user->display_name . $user->user_login,
			'size' => 96,
			'background' => $colors[ $user->ID % ( count( $colors ) - 1 ) ],
			'color' => 'fff',
			'length' => 1,
			'font-size' => 0.4,
			'rounded' => false,
			'uppercase' => true,
			'bold' => true,
		];

		return 'https://ui-avatars.com/api/' . join( '/', $query_args );
	}
}
