<?php

namespace MyListing\Controllers\Promotions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Promotions_Admin_Controller extends \MyListing\Controllers\Base_Controller {

	protected function hooks() {
		$this->on( 'admin_menu', '@set_menu_location', 60 );
		$this->on( 'admin_footer-post.php', '@display_package_statuses' );
		$this->on( 'admin_footer-post-new.php', '@display_package_statuses' );
		$this->on( 'admin_footer-edit.php', '@display_package_statuses' );
		$this->on( 'edit_form_after_title', '@display_package_id_in_edit_screen' );
		$this->on( 'woocommerce_product_options_general_product_data', '@display_product_settings' );
		$this->on( 'manage_cts_promo_package_posts_custom_column', '@custom_table_column_contents', 5, 2 );
		$this->once( 'save_post', '@save_package_settings', 99, 2 );
		$this->filter( 'manage_cts_promo_package_posts_columns', '@custom_table_columns' );
		$this->filter( 'post_row_actions', '@remove_quick_edit_link', 10, 2 );
		$this->filter( 'bulk_actions-edit-cts_promo_package', '@remove_bulk_edit_setting' );
		$this->filter( 'woocommerce_process_product_meta_promotion_package', '@save_product_settings' );
	}

	protected function set_menu_location() {
		if ( $post_type = get_post_type_object( 'cts_promo_package' ) ) {
			add_submenu_page(
				'users.php',
				$post_type->labels->name,
				$post_type->labels->menu_name,
				$post_type->cap->edit_posts,
				'edit.php?post_type=cts_promo_package'
			);
		}
	}

	protected function display_product_settings() {
		global $post;
		require locate_template( 'templates/admin/single-product-screen/promotion-product-settings.php' );
	}

	protected function save_product_settings( $post_id ) {
		delete_post_meta( $post_id, '_promotion_duration' );
		delete_post_meta( $post_id, '_promotion_priority' );

		if ( ! empty( $_POST['_promotion_duration'] ) ) {
			update_post_meta( $post_id, '_promotion_duration', absint( $_POST['_promotion_duration'] ) );
		}

		if ( ! empty( $_POST['_promotion_priority'] ) ) {
			update_post_meta( $post_id, '_promotion_priority', absint( $_POST['_promotion_priority'] ) );
		}
	}

	protected function save_package_settings( $post_id, $post = null ) {
		if ( ! ( $post && $post->post_type === 'cts_promo_package' ) || defined('DOING_AJAX') && DOING_AJAX ) {
			return;
		}

		// current listing expiry date
		$expiry_date = get_post_meta( $post_id, '_expires', true );

		// Get proper post status.
		$status = $post->post_status;
		$old_status = ! empty( $_POST['original_post_status'] ) ? $_POST['original_post_status'] : false;
		$action = ! empty( $_GET['action'] ) ? $_GET['action'] : false;

		// on status change handle activating/expiring the package
		if ( $status !== $old_status ) {
			if ( $status === 'publish' || $action === 'untrash' ) {
				\MyListing\Src\Promotions\activate_package( $post->ID );
			}

			if ( $status === 'trash' || $action === 'trash' ) {
				\MyListing\Src\Promotions\expire_package( $post->ID );
			}
		}

		// re-apply the custom expiry date set by the admin ("Promoted Until" setting)
		// since it's reset by the `activate_package` function call
		if ( $expiry_date && strtotime( $expiry_date, current_time('timestamp') ) ) {
			update_post_meta( $post->ID, '_expires', $expiry_date );
		}

		// handle priority change
		if ( $status === 'publish' ) {
			$listing_id = get_post_meta( $post_id, '_listing_id', true );
			$listing_priority = get_post_meta( $listing_id, '_featured', true );
			$package_priority = get_post_meta( $post_id, '_priority', true );
			if ( $listing_id && $listing_priority && absint( $listing_priority ) !== absint( $package_priority ) ) {
				update_post_meta( $listing_id, '_featured', absint( $package_priority ) );
			}
		}
	}

	protected function display_package_statuses() {
		global $post, $post_type;
		if ( $post_type !== 'cts_promo_package' ) {
			return;
		}

		$statuses = [
			'publish' => esc_html( __( 'Active', 'my-listing' ) ),
			'trash' => esc_html( __( 'Expired', 'my-listing' ) ),
		];

		// get all non-builtin post status and add them as <option>
		$options = $display = '';
		if ( $post instanceof \WP_Post ) {
			foreach ( $statuses as $status => $name ) {
				$selected = selected( $post->post_status, $status, false );

				// if one of our custom post statuses is selected, remember it
				$selected AND $display = $name;
				$options .= "<option{$selected} value='{$status}'>{$name}</option>";
			}
		}
		?>
		<script type="text/javascript">
			jQuery( function($) {
				<?php if ( ! empty( $display ) ): ?>
					jQuery('#post-status-display').html( '<?php echo $display; ?>' );
				<?php endif; ?>

				var select = jQuery('#post-status-select').find('select').html( "<?php echo $options ?>" );
				if ( $('body.post-type-cts_promo_package .subsubsub .trash a').length ) {
					var counter = $('body.post-type-cts_promo_package .subsubsub .trash a span').detach();
					$('body.post-type-cts_promo_package .subsubsub .trash a').html(
						'<?php echo esc_attr( _x( 'Expired', 'Admin view promotions - Expired Packages', 'my-listing' ) ) ?> '
					).append( counter );
				}

				// promoted listing cannot be changed after the package has been created
				if ( $('.promote-listing-setting select').val() ) {
					$('.promote-listing-setting').addClass('ml-overlay-disabled').css('pointer-events', 'none');
				}
			} );
		</script>
		<?php
	}

	protected function display_package_id_in_edit_screen( $post ) {
		if ( ! ( $post && $post->ID && $post->post_type === 'cts_promo_package' ) ) {
			return;
		}

		if ( empty( $_GET['action'] ) || $_GET['action'] !== 'edit' ) {
			return;
		} ?>
		<h1 class="wp-heading-inline-package">
			<?php printf( __( 'Edit Package #%d', 'my-listing' ), $post->ID ); ?>
			<a href="<?php echo esc_url( add_query_arg( 'post_type','cts_promo_package', admin_url( 'post-new.php' ) ) ) ?>" class="page-title-action">
				<?php esc_html_e( 'Add New', 'my-listing' ); ?>
			</a>
		</h1>
		<style>.wrap h1.wp-heading-inline {display:none;} .wrap > .page-title-action {display:none;} #poststuff {margin-top: 30px;}</style>
		<?php
	}

	protected function custom_table_columns( $columns ) {
		unset( $columns['date'] );
		$columns['title'] = esc_html( __( 'Package ID', 'my-listing' ) );
		$columns['user'] = esc_html( __( 'User', 'my-listing' ) );
		$columns['duration'] = esc_html( __( 'Promoted Until', 'my-listing' ) );
		$columns['product'] = esc_html( __( 'Product', 'my-listing' ) );
		$columns['order'] = esc_html( __( 'Order ID', 'my-listing' ) );

		return $columns;
	}

	protected function custom_table_column_contents(  $column, $post_id  ) {
		switch ( $column ) {
			case 'user':
				$title = esc_html( __( 'n/a', 'my-listing' ) );
				$user_id = absint( get_post_meta( $post_id, '_user_id', true ) );
				if ( $user_id ) {
					$user = get_userdata( $user_id );
					if ( $user ) {
						$title = '<a target="_blank" href="' . esc_url( get_edit_user_link( $user_id ) ) . '">';
						$title .= $user->user_login;
						$title .= '</a>';
					}
				}

				echo $title;
			break;

			case 'duration':
				$expires = get_post_meta( $post_id, '_expires', true );
				$expiry_time = strtotime( $expires, current_time( 'timestamp' ) );
				echo $expiry_time ? date_i18n( 'F j, Y g:i a', $expiry_time ) : '&ndash;';
			break;

			case 'product':
				$link = esc_html__( 'n/a', 'my-listing' );
				$product_id = get_post_meta( $post_id, '_product_id', true );
				if ( $product_id ) {
					$product = wc_get_product( $product_id );
					if ( $product ) {
						$link = '<a target="_blank" href="'.esc_url( get_edit_post_link( $product_id ) ).'">'.$product->get_name().'</a>';
					}
				}
				echo $link;
			break;

			case 'order':
				$link = esc_html__( 'n/a', 'my-listing' );
				$order_id = absint( get_post_meta( $post_id, '_order_id', true ) );
				if ( $order_id ) {
					$link = '<a target="_blank" href="'.esc_url( get_edit_post_link( $order_id ) ).'">#'.$order_id.'</a>';
				}
				echo $link;
			break;
		}
	}

	protected function remove_quick_edit_link( $actions, $post ) {
		if ( $post->post_type !== 'cts_promo_package' ) {
			return $actions;
		}

		$actions['edit'] = sprintf(
			'<a href="%s">%s</a>',
			get_edit_post_link( $post ),
			_x( 'Edit Package', 'Promotions list in wp-admin', 'my-listing' )
		);

		if ( $listing_id = absint( $post->_listing_id ) ) {
			$actions['inline hide-if-no-js'] = sprintf(
				'<a href="%s">%s</a>',
				get_edit_post_link( $listing_id ),
				_x( 'Edit Listing', 'Promotions list in wp-admin', 'my-listing' )
			);
		} else {
			unset( $actions['inline hide-if-no-js'] );
		}

		return $actions;
	}

	protected function remove_bulk_edit_setting( $actions ) {
		unset( $actions['edit'] );
		return $actions;
	}
}