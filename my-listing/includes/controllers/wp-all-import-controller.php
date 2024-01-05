<?php

namespace MyListing\Controllers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Wp_All_Import_Controller extends Base_Controller {

	protected function is_active() {
		$all_import_active = defined('PMXI_VERSION');
		$old_addon_active = defined('WPAI_MYLISTING_ROOT_DIR');
		return $all_import_active && ! $old_addon_active && apply_filters( 'mylisting/wp-all-import/enabled', true ) !== false;
	}

	protected function dependencies() {
		require_once locate_template('includes/apis/wp-all-import/wp-all-import-api.php');
	}

	protected function hooks() {
		$this->on( 'pmxi_extend_options_featured', '@add_import_metabox', 10, 2 );
		$this->on( 'admin_footer', '@fix_switcher_target_js_error' );
		$this->filter( 'pmxi_addons', '@register_addon' );
		$this->filter( 'wp_all_import_addon_parse', '@register_parse_function' );
		$this->filter( 'wp_all_import_addon_import', '@register_import_function' );
		$this->filter( 'pmxi_custom_types', '@include_listing_types_in_import_dropdown' );
		$this->filter( 'pmxi_custom_types', '@exclude_post_types_from_import_dropdown' );
		$this->filter( 'wpai_custom_selected_post', '@set_selected_listing_type_in_import_dropdown', 50, 3 );
		$this->filter( 'wp_all_import_post_type_image', '@set_listing_type_icon_in_import_dropdown' );
		$this->filter( 'pmxi_options_options', '@save_selected_listing_type_to_import_settings', 50 );
		$this->filter( 'pmxi_save_options', '@save_selected_listing_type_to_import_settings', 50 );
		$this->filter( 'pmxi_options_options', '@load_addon_settings', 70 );
		$this->filter( 'wp_all_import_is_images_section_enabled', '@is_images_section_enabled', 50, 2 );
	}

	protected function register_addon( $addons ) {
		$addons['mylisting-addon'] = 1;
		return $addons;
	}

	protected function register_parse_function( $functions ) {
		$functions['mylisting-addon'] = '\MyListing\Apis\Wp_All_Import\parser';
		return $functions;
	}

	protected function register_import_function( $functions ) {
		$functions['mylisting-addon'] = '\MyListing\Apis\Wp_All_Import\importer';
		return $functions;
	}

	protected function add_import_metabox( $post_type, $data ) {
		if ( $post_type !== 'job_listing' || empty( $data['listing_type'] ) ) {
			return;
		}

		$type = \MyListing\Src\Listing_Type::get_by_name( $data['listing_type'] );
		if ( ! $type ) {
			return;
		}

		$values = ! empty( $data['mylisting-addon'] ) ? (array) $data['mylisting-addon'] : [];
		require locate_template('templates/admin/wp-all-import/wp-all-import-ui-metabox.php');
	}

	protected function include_listing_types_in_import_dropdown( $post_types ) {
		foreach( \MyListing\get_listing_types() as $type ) {
			$key = '_listing_type_'.$type->get_slug();
			$post_types[ $key ] = (object) [
				'name' => $key,
				'label' => 'Listing Type: '.$type->get_plural_name(),
				'labels' => (object) [
					'name' => 'Listing Type: '.$type->get_plural_name(),
				],
			];
		}

		return $post_types;
	}

	protected function exclude_post_types_from_import_dropdown( $post_types ) {
		unset( $post_types['job_listing'] );
		unset( $post_types['case27_listing_type'] );
		unset( $post_types['case27_user_package'] );
		unset( $post_types['case27_report'] );
		unset( $post_types['cts_promo_package'] );
		unset( $post_types['claim'] );

		return $post_types;
	}

	protected function set_selected_listing_type_in_import_dropdown( $selected, $post, $post_type ) {
		if ( ! empty( $post['listing_type'] ) && $post_type === '_listing_type_'.$post['listing_type'] ) {
			return true;
		}

		return $selected;
	}

	protected function set_listing_type_icon_in_import_dropdown( $icons ) {
		$icons = is_array( $icons ) ? $icons : [];
		foreach( \MyListing\get_listing_types() as $listing_type ) {
			$icons[ '_listing_type_'.$listing_type->get_slug() ] = [
				'image' => 'dashicons-location',
			];
		}

		return $icons;
	}

	protected function save_selected_listing_type_to_import_settings( $options ) {
		if ( ! \MyListing\str_contains( $options['custom_type'], '_listing_type_' ) ) {
			return $options;
		}

		$slug = str_replace( '_listing_type_', '', $options['custom_type'] );
		$options['custom_type'] = 'job_listing';
		if ( $type = \MyListing\Src\Listing_Type::get_by_name( $slug ) ) {
			$options['listing_type'] = $type->get_slug();
		}

		return $options;
	}

	protected function load_addon_settings( $options ) {
		if ( isset( $options['listing_type'] ) && $type = \MyListing\Src\Listing_Type::get_by_name( $options['listing_type'] ) ) {
			if ( ! isset( $options['mylisting-addon'] ) ) {
				$options['mylisting-addon'] = [];
			}
		}

		return $options;
	}

	protected function is_images_section_enabled( $is_enabled, $post_type ) {
		if ( $post_type === 'job_listing' ) {
			return false;
		}

		return $is_enabled;
	}

	/**
	 * Fix: select/radio fields with non-latin characters causing js error
	 *
	 * @since 2.6
	 */
	protected function fix_switcher_target_js_error() {
		if ( ! \MyListing\Apis\Wp_All_Import\is_import_screen() ) {
			return;
		} ?>
		<script type="text/javascript">
			jQuery('.wpallimport-section.mylisting-addon input.switcher').each( function() {
				var label = jQuery(this).siblings('label');
				if ( label.length ) {
					var uniqid = ( Date.now() + Math.random() ).toString().replace('.', '');
					jQuery(this).attr('id', 'rand-'+uniqid);
					label.attr('for', 'rand-'+uniqid);
				}
			} );
		</script>
	<?php }
}
