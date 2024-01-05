<?php

namespace MyListing\Onboarding\Demo_Importer;

if ( ! defined('ABSPATH') ) {
	exit;
}

function import_site_config() {
	$config = get_demo_file('site-config.json');

	// import theme options
	if ( isset( $config['theme-options'] ) && is_array( $config['theme-options'] ) ) {
		$opt = $config['theme-options'];

		// general
		if ( ! empty( $opt['logo'] ) && ( $attachment_id = get_imported_post_id( $opt['logo'] ) ) ) {
			update_option( 'options_general_site_logo', $attachment_id );
			update_option( '_options_general_site_logo', 'field_595b7eda34dc9' );
		}

		if ( ! empty( $opt['accent-color'] ) ) {
			update_option( 'options_general_brand_color', $opt['accent-color'] );
			update_option( '_options_general_brand_color', 'field_5998c6c12e783' );
		}

		if ( ! empty( $opt['bg-color'] ) ) {
			update_option( 'options_general_background_color', $opt['bg-color'] );
			update_option( '_options_general_background_color', 'field_5cc24c4ebc50a' );
		}

		if ( ! empty( $opt['loading-screen'] ) ) {
			update_option( 'options_general_loading_overlay', $opt['loading-screen'] );
			update_option( '_options_general_loading_overlay', 'field_598dd43d705fa' );
		}

		if ( ! empty( $opt['loading-screen-color'] ) ) {
			update_option( 'options_general_loading_overlay_color', $opt['loading-screen-color'] );
			update_option( '_options_general_loading_overlay_color', 'field_59ba134da1abd' );
		}

		if ( ! empty( $opt['loading-screen-bg'] ) ) {
			update_option( 'options_general_loading_overlay_background_color', $opt['loading-screen-bg'] );
			update_option( '_options_general_loading_overlay_background_color', 'field_59ba138ca1abe' );
		}

		// header
		if ( ! empty( $opt['header-height'] ) ) {
			update_option( 'options_header_style', $opt['header-height'] );
			update_option( '_options_header_style', 'field_595b7d8981914' );
		}

		if ( ! empty( $opt['header-text-color'] ) ) {
			update_option( 'options_header_skin', $opt['header-text-color'] );
			update_option( '_options_header_skin', 'field_59a1982a24d8f' );
		}

		if ( ! empty( $opt['header-bg-color'] ) ) {
			update_option( 'options_header_background_color', $opt['header-bg-color'] );
			update_option( '_options_header_background_color', 'field_595b7e899d6ac' );
		}

		if ( ! empty( $opt['header-border-color'] ) ) {
			update_option( 'options_header_border_color', $opt['header-border-color'] );
			update_option( '_options_header_border_color', 'field_59a3566469433' );
		}

		if ( ! empty( $opt['sticky-header'] ) ) {
			update_option( 'options_header_fixed', '1' );
			update_option( '_options_header_fixed', 'field_595b7dd181915' );
		}

		if ( ! empty( $opt['header-menu-location'] ) ) {
			update_option( 'options_header_menu_location', $opt['header-menu-location'] );
			update_option( '_options_header_menu_location', 'field_595b80b1a931a' );
		}

		if ( ! empty( $opt['header-logo-height'] ) ) {
			update_option( 'options_header_logo_height', $opt['header-logo-height'] );
			update_option( '_options_header_logo_height', 'field_59eeaac62c1c5' );
		}

		if ( ! empty( $opt['header-show-search'] ) ) {
			update_option( 'options_header_show_search_form', '1' );
			update_option( '_options_header_show_search_form', 'field_595b8055a9318' );
		}

		if ( ! empty( $opt['header-search-placeholder'] ) ) {
			update_option( 'options_header_search_form_placeholder', $opt['header-search-placeholder'] );
			update_option( '_options_header_search_form_placeholder', 'field_595b8071a9319' );
		}

		if ( ! empty( $opt['header-search-featured'] ) ) {
			update_option( 'options_header_search_form_featured_categories', $opt['header-search-featured'] );
			update_option( '_options_header_search_form_featured_categories', 'field_5964e0d3bbed9' );
		}

		if ( ! empty( $opt['header-show-cta'] ) ) {
			update_option( 'options_header_show_call_to_action_button', '1' );
			update_option( '_options_header_show_call_to_action_button', 'field_595b820157999' );
		}

		if ( ! empty( $opt['header-cta-link'] ) ) {
			update_option( 'options_header_call_to_action_links_to', 'map:'.$opt['header-cta-link'] );
			update_option( '_options_header_call_to_action_links_to', 'field_595b82555799a' );
		}

		if ( ! empty( $opt['header-cta-label'] ) ) {
			update_option( 'options_header_call_to_action_label', $opt['header-cta-label'] );
			update_option( '_options_header_call_to_action_label', 'field_595b82b95799b' );
		}

		if ( ! empty( $opt['header-show-cart'] ) ) {
			update_option( 'options_header_show_cart', '1' );
			update_option( '_options_header_show_cart', 'field_5c0490b2397ec' );
		}

		if ( ! empty( $opt['header-show-title-bar'] ) ) {
			update_option( 'options_header_show_title_bar', '1' );
			update_option( '_options_header_show_title_bar', 'field_59a3660f98ace' );
		}

		if ( ! empty( $opt['header-scroll-logo'] ) && ( $attachment_id = get_imported_post_id( $opt['header-scroll-logo'] ) ) ) {
			update_option( 'options_header_scroll_logo', $attachment_id );
			update_option( '_options_header_scroll_logo', 'field_59ac724a6000a' );
		}

		if ( ! empty( $opt['header-scroll-text-color'] ) ) {
			update_option( 'options_header_scroll_skin', $opt['header-scroll-text-color'] );
			update_option( '_options_header_scroll_skin', 'field_59a350150bddf' );
		}

		if ( ! empty( $opt['header-scroll-bg-color'] ) ) {
			update_option( 'options_header_scroll_background_color', $opt['header-scroll-bg-color'] );
			update_option( '_options_header_scroll_background_color', 'field_59a34ff80bdde' );
		}

		if ( ! empty( $opt['header-scroll-border-color'] ) ) {
			update_option( 'options_header_scroll_border_color', $opt['header-scroll-border-color'] );
			update_option( '_options_header_scroll_border_color', 'field_59ac71706c392' );
		}

		// footer
		if ( ! empty( $opt['show-footer'] ) ) {
			update_option( 'options_footer_show', '1' );
			update_option( '_options_footer_show', 'field_5c0b1d9b0092e' );
		}

		if ( ! empty( $opt['show-footer-widgets'] ) ) {
			update_option( 'options_footer_show_widgets', '1' );
			update_option( '_options_footer_show_widgets', 'field_595b85b15dbec' );
		}

		if ( ! empty( $opt['show-footer-menu'] ) ) {
			update_option( 'options_footer_show_menu', '1' );
			update_option( '_options_footer_show_menu', 'field_595b85cc5dbed' );
		}

		if ( ! empty( $opt['footer-text'] ) ) {
			update_option( 'options_footer_text', $opt['footer-text'] );
			update_option( '_options_footer_text', 'field_595b85e35dbee' );
		}

		if ( ! empty( $opt['show-back-to-top'] ) ) {
			update_option( 'options_footer_show_back_to_top_button', '1' );
			update_option( '_options_footer_show_back_to_top_button', 'field_598719cf8d4c3' );
		}

		// explore
		if ( ! empty( $opt['explore-page'] ) ) {
			update_option( 'options_general_explore_listings_page', 'map:'.$opt['explore-page'] );
			update_option( '_options_general_explore_listings_page', 'field_595bd2fffffff' );
		}

		if ( ! empty( $opt['explore-per-page'] ) ) {
			update_option( 'options_general_explore_listings_per_page', $opt['explore-per-page'] );
			update_option( '_options_general_explore_listings_per_page', 'field_59770a24cb27d' );
		}

		if ( ! empty( $opt['add-listing-page'] ) ) {
			update_option( 'options_general_add_listing_page', 'map:'.$opt['add-listing-page'] );
			update_option( '_options_general_add_listing_page', 'field_59a455e61eccc' );
		}

		// single-listing
		if ( ! empty( $opt['single-header-style'] ) ) {
			update_option( 'options_single_listing_header_style', $opt['single-header-style'] );
			update_option( '_options_single_listing_header_style', 'field_595b7d8981914' );
		}

		if ( ! empty( $opt['single-header-text-color'] ) ) {
			update_option( 'options_single_listing_header_skin', $opt['single-header-text-color'] );
			update_option( '_options_single_listing_header_skin', 'field_59a1982a24d8f' );
		}

		if ( ! empty( $opt['single-header-bg-color'] ) ) {
			update_option( 'options_single_listing_header_background_color', $opt['single-header-bg-color'] );
			update_option( '_options_single_listing_header_background_color', 'field_595b7e899d6ac' );
		}

		if ( ! empty( $opt['single-header-border-color'] ) ) {
			update_option( 'options_single_listing_header_border_color', $opt['single-header-border-color'] );
			update_option( '_options_single_listing_header_border_color', 'field_59a3566469433' );
		}

		if ( ! empty( $opt['single-header-preset'] ) ) {
			update_option( 'options_single_listing_header_preset', $opt['single-header-preset'] );
			update_option( '_options_single_listing_header_preset', 'field_5963dbc3f9cbe' );
		}

		if ( ! empty( $opt['single-header-blend'] ) ) {
			update_option( 'options_single_listing_blend_header', '1' );
			update_option( '_options_single_listing_blend_header', 'field_5e7b36643fc52' );
		}

		if ( ! empty( $opt['single-cover-height'] ) ) {
			update_option( 'options_single_listing_cover_height', $opt['single-cover-height'] );
			update_option( '_options_single_listing_cover_height', 'field_5e858a4202b77' );
		}

		if ( ! empty( $opt['single-cover-overlay-color'] ) ) {
			update_option( 'options_single_listing_cover_overlay_color', $opt['single-cover-overlay-color'] );
			update_option( '_options_single_listing_cover_overlay_color', 'field_59a056ca65404' );
		}

		if ( ! empty( $opt['single-cover-overlay-opacity'] ) ) {
			update_option( 'options_single_listing_cover_overlay_opacity', $opt['single-cover-overlay-opacity'] );
			update_option( '_options_single_listing_cover_overlay_opacity', 'field_59a056ef65405' );
		}

		if ( ! empty( $opt['preview-overlay-color'] ) ) {
			update_option( 'options_listing_preview_overlay_color', $opt['preview-overlay-color'] );
			update_option( '_options_listing_preview_overlay_color', 'field_59a169755eeef' );
		}

		if ( ! empty( $opt['preview-overlay-opacity'] ) ) {
			update_option( 'options_listing_preview_overlay_opacity', $opt['preview-overlay-opacity'] );
			update_option( '_options_listing_preview_overlay_opacity', 'field_59a1697b5eef0' );
		}

		// blog
		if ( ! empty( $opt['default-blogpost-image'] ) && ( $attachment_id = get_imported_post_id( $opt['default-blogpost-image'] ) ) ) {
			update_option( 'options_blog_default_post_image', $attachment_id );
			update_option( '_options_blog_default_post_image', 'field_5971331211e6c' );
		}

		// shop
		if ( ! empty( $opt['shop-columns'] ) ) {
			update_option( 'options_shop_page_product_columns', $opt['shop-columns'] );
			update_option( '_options_shop_page_product_columns', 'field_5af19f2bd8eed' );
		}

		if ( ! empty( $opt['shop-sidebar'] ) ) {
			update_option( 'options_shop_page_sidebar', $opt['shop-sidebar'] );
			update_option( '_options_shop_page_sidebar', 'field_5af1a04387483' );
		}
	}

	// import typography
	if ( isset( $config['typography'] ) && is_array( $config['typography'] ) ) {
		update_option( 'mylisting_typography', wp_json_encode( $config['typography'] ) );
	}

	// import permalinks
	if ( isset( $config['permalinks'] ) && is_array( $config['permalinks'] ) ) {
		if ( ! empty( $config['permalinks']['wordpress'] ) ) {
			update_option( 'permalink_structure', $config['permalinks']['wordpress'] );
		}

		if ( ! empty( $config['permalinks']['mylisting'] ) ) {
			update_option( 'mylisting_permalinks', $config['permalinks']['mylisting'] );
		}

		if ( ! empty( $config['permalinks']['woocommerce'] ) ) {
			update_option( 'woocommerce_permalinks', $config['permalinks']['woocommerce'] );
		}
	}

	// import preview card config
	if ( isset( $config['preview-cards'] ) && is_array( $config['preview-cards'] ) ) {
		update_option( 'mylisting_preview_cards', wp_json_encode( $config['preview-cards'] ) );
	}

	// import listing stats config
	if ( isset( $config['listing-stats'] ) && is_array( $config['listing-stats'] ) ) {
		update_option( 'mylisting_userdash', wp_json_encode( $config['listing-stats'] ) );
	}

	// import custom taxonomies
	if ( isset( $config['custom-taxonomies'] ) && is_array( $config['custom-taxonomies'] ) ) {
		$custom_taxonomies = [];
		foreach ( $config['custom-taxonomies'] as $tax_slug => $tax_label ) {
			$custom_taxonomies[] = [
				'label' => $tax_label,
				'slug' => $tax_slug,
			];
		}

		update_option( 'job_manager_custom_taxonomy', $custom_taxonomies );
	}

	// import listings > settings
	if ( isset( $config['listing-settings'] ) && is_array( $config['listing-settings'] ) ) {
		$opt = $config['listing-settings'];
		if ( ! empty( $opt['paid_listings_enabled'] ) ) {
			update_option( 'case27_paid_listings', '1' );
		}

		if ( ! empty( $opt['submission_requires_account'] ) ) {
			update_option( 'job_manager_user_requires_account', '1' );
		}

		if ( ! empty( $opt['submission_requires_approval'] ) ) {
			update_option( 'job_manager_submission_requires_approval', '1' );
		}

		if ( ! empty( $opt['submission_default_duration'] ) ) {
			update_option( 'job_manager_submission_duration', $opt['submission_default_duration'] );
		}

		if ( ! empty( $opt['user_can_edit_pending_submissions'] ) ) {
			update_option( 'job_manager_user_can_edit_pending_submissions', '1' );
		}

		if ( ! empty( $opt['user_can_edit_published_submissions'] ) ) {
			update_option( 'job_manager_user_edit_published_submissions', $opt['user_can_edit_published_submissions'] );
		}

		if ( ! empty( $opt['claims_enabled'] ) ) {
			update_option( 'case27_claim_listings', '1' );
		}

		if ( ! empty( $opt['claims_require_approval'] ) ) {
			update_option( 'case27_claim_requires_approval', '1' );
		}

		if ( ! empty( $opt['claims_page_id'] ) ) {
			update_option( 'job_manager_claim_listing_page_id', 'map:'.$opt['claims_page_id'] );
		}

		if ( ! empty( $opt['claims_mark_verified'] ) ) {
			update_option( 'mylisting_claims_mark_verified', '1' );
		}
	}

	// import elementor settings
	if ( isset( $config['elementor'] ) && is_array( $config['elementor'] ) ) {
		if ( ! empty( $config['elementor']['scheme-color'] ) ) {
			update_option( 'elementor_scheme_color', $config['elementor']['scheme-color'] );
		}

		if ( ! empty( $config['elementor']['scheme-color-picker'] ) ) {
			update_option( 'elementor_scheme_color-picker', $config['elementor']['scheme-color-picker'] );
		}

		if ( ! empty( $config['elementor']['general-settings'] ) ) {
			update_option( '_elementor_general_settings', $config['elementor']['general-settings'] );
		}

		if ( ! empty( $config['elementor']['scheme-typography'] ) ) {
			update_option( 'elementor_scheme_typography', $config['elementor']['scheme-typography'] );
		}
	}

	// import wordpress settings
	if ( isset( $config['wordpress'] ) && is_array( $config['wordpress'] ) ) {
		if ( ! empty( $config['wordpress']['show_on_front'] ) ) {
			update_option( 'show_on_front', $config['wordpress']['show_on_front'] );
		}

		if ( ! empty( $config['wordpress']['page_on_front'] ) ) {
			update_option( '__page_on_front', 'map:'.$config['wordpress']['page_on_front'] );
		}

		if ( ! empty( $config['wordpress']['page_for_posts'] ) ) {
			update_option( '__page_for_posts', 'map:'.$config['wordpress']['page_for_posts'] );
		}
	}

	// import woocommerce settings
	if ( isset( $config['woocommerce'] ) && is_array( $config['woocommerce'] ) ) {
		$opt = $config['woocommerce'];
		if ( ! empty( $opt['cart_page_id'] ) ) {
			update_option( 'woocommerce_cart_page_id', 'map:'.$opt['cart_page_id'] );
		}

		if ( ! empty( $opt['checkout_page_id'] ) ) {
			update_option( 'woocommerce_checkout_page_id', 'map:'.$opt['checkout_page_id'] );
		}

		if ( ! empty( $opt['myaccount_page_id'] ) ) {
			update_option( 'woocommerce_myaccount_page_id', 'map:'.$opt['myaccount_page_id'] );
		}

		if ( ! empty( $opt['terms_page_id'] ) ) {
			update_option( 'woocommerce_terms_page_id', 'map:'.$opt['terms_page_id'] );
		}

		if ( ! empty( $opt['shop_page_id'] ) ) {
			update_option( 'woocommerce_shop_page_id', 'map:'.$opt['shop_page_id'] );
		}

		if ( ! empty( $opt['placeholder_image'] ) && ( $attachment_id = get_imported_post_id( $opt['placeholder_image'] ) ) ) {
			update_option( 'woocommerce_placeholder_image', $attachment_id );
		}

		if ( ! empty( $opt['checkout_pay_endpoint'] ) ) {
			update_option( 'woocommerce_checkout_pay_endpoint', $opt['checkout_pay_endpoint'] );
		}

		if ( ! empty( $opt['checkout_order_received_endpoint'] ) ) {
			update_option( 'woocommerce_checkout_order_received_endpoint', $opt['checkout_order_received_endpoint'] );
		}

		if ( ! empty( $opt['myaccount_add_payment_method_endpoint'] ) ) {
			update_option( 'woocommerce_myaccount_add_payment_method_endpoint', $opt['myaccount_add_payment_method_endpoint'] );
		}

		if ( ! empty( $opt['myaccount_delete_payment_method_endpoint'] ) ) {
			update_option( 'woocommerce_myaccount_delete_payment_method_endpoint', $opt['myaccount_delete_payment_method_endpoint'] );
		}

		if ( ! empty( $opt['myaccount_set_default_payment_method_endpoint'] ) ) {
			update_option( 'woocommerce_myaccount_set_default_payment_method_endpoint', $opt['myaccount_set_default_payment_method_endpoint'] );
		}

		if ( ! empty( $opt['myaccount_orders_endpoint'] ) ) {
			update_option( 'woocommerce_myaccount_orders_endpoint', $opt['myaccount_orders_endpoint'] );
		}

		if ( ! empty( $opt['myaccount_view_order_endpoint'] ) ) {
			update_option( 'woocommerce_myaccount_view_order_endpoint', $opt['myaccount_view_order_endpoint'] );
		}

		if ( ! empty( $opt['myaccount_downloads_endpoint'] ) ) {
			update_option( 'woocommerce_myaccount_downloads_endpoint', $opt['myaccount_downloads_endpoint'] );
		}

		if ( ! empty( $opt['myaccount_edit_account_endpoint'] ) ) {
			update_option( 'woocommerce_myaccount_edit_account_endpoint', $opt['myaccount_edit_account_endpoint'] );
		}

		if ( ! empty( $opt['myaccount_edit_address_endpoint'] ) ) {
			update_option( 'woocommerce_myaccount_edit_address_endpoint', $opt['myaccount_edit_address_endpoint'] );
		}

		if ( ! empty( $opt['myaccount_payment_methods_endpoint'] ) ) {
			update_option( 'woocommerce_myaccount_payment_methods_endpoint', $opt['myaccount_payment_methods_endpoint'] );
		}

		if ( ! empty( $opt['myaccount_lost_password_endpoint'] ) ) {
			update_option( 'woocommerce_myaccount_lost_password_endpoint', $opt['myaccount_lost_password_endpoint'] );
		}

		if ( ! empty( $opt['logout_endpoint'] ) ) {
			update_option( 'woocommerce_logout_endpoint', $opt['logout_endpoint'] );
		}
	}
}
