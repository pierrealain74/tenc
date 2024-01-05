<?php
/**
 * Paid Listings module.
 *
 * @since   1.6
 * @license GNU General Public License v3.0 (https://www.gnu.org/licenses/gpl-3.0.html)
 * @copyright:
 *     2019 27collective
 *     2017 Astoundify
 *     2015 Automattic
 */

namespace MyListing\Src\Paid_Listings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Paid_Listings {
	use \MyListing\Src\Traits\Instantiatable;

	public function __construct() {
		if ( ! class_exists( '\\WooCommerce' ) ) {
			return;
		}

		// Migrate WPJM WC Paid Listing DB.
		WCPL_Importer::instance();

		// Load User Packages.
		User_Packages::instance();

		// claims are enabled
		if ( mylisting_get_setting( 'claims_enabled' ) ) {
			\MyListing\Src\Claims\Claims::instance();
			Controllers\Claim_Listing::instance();
		}

		// paid listings enabled
		if ( mylisting_get_setting( 'paid_listings_enabled' ) ) {

			// add listing form handler
			Controllers\Add_Listing::instance();

			// switch package
			if ( apply_filters( 'mylisting/paid-listing/enable-switch-package', true ) ) {
				Controllers\Switch_Package::instance();
			}
		}

		// add "listing package" custom woocommerce product type
		add_filter( 'product_type_selector', [ $this, 'add_product_type' ] );
		add_filter( 'woocommerce_product_class' , [ $this, 'set_product_class' ], 10, 3 );
		add_action( 'woocommerce_product_options_general_product_data', [ $this, 'add_product_settings' ] );
		add_filter( 'woocommerce_process_product_meta_job_package', [ $this, 'save_product_settings' ] );
		add_filter( 'woocommerce_process_product_meta_job_package_subscription', [ $this, 'save_product_settings' ] );

		// handle cart processing
		add_action( 'woocommerce_job_package_add_to_cart', 'woocommerce_simple_add_to_cart', 30 );
		add_filter( 'woocommerce_get_cart_item_from_session', [ $this, 'get_cart_item_from_session' ], 10, 2 );
		add_action( 'woocommerce_checkout_create_order_line_item', [ $this, 'checkout_create_order_line_item' ], 10, 4 );

		// display listing in cart
		add_filter( 'woocommerce_get_item_data', [ $this, 'display_listing_in_cart' ], 10, 2 );

		// disable guest checkout when purchasing listing and enable checkout signup
		add_filter( 'option_woocommerce_enable_signup_and_login_from_checkout', [ $this, 'enable_signup_and_login_from_checkout' ] );
		add_filter( 'option_woocommerce_enable_guest_checkout', [ $this, 'enable_guest_checkout' ] );

		// order placed - thank you page
		add_action( 'woocommerce_thankyou', [ $this, 'woocommerce_thankyou' ], 5 );

		// process order
		add_action( 'woocommerce_order_status_processing', [ $this, 'order_paid' ] );
		add_action( 'woocommerce_order_status_completed', [ $this, 'order_paid' ] );
		add_action( 'woocommerce_order_status_cancelled', [ $this, 'order_cancelled' ] );

		// disable repeat product purchase, if enabled for the product
		add_filter( 'mylisting/paid-listings/product/is-purchasable', [ $this, 'disable_repeat_purchase' ], 10, 2 );
		add_filter( 'woocommerce_is_purchasable', [ $this, 'disable_repeat_purchase' ], 10, 2 );
		add_action( 'woocommerce_single_product_summary', [ $this, 'purchase_disabled_message' ], 31 );

		// hide product meta fields
		add_filter( 'woocommerce_order_item_display_meta_key', [ $this, 'display_meta_key' ] );
		add_filter( 'woocommerce_order_item_display_meta_value', [ $this, 'display_meta_value' ], 10, 2 );

		// Register post status.
		add_action( 'init', [ $this, 'register_post_status' ], 7 );
		add_filter( 'mylisting/valid-submission-statuses', [ $this, 'add_valid_listing_status' ] );

		// Implement field visibility by package.
		add_filter( 'mylisting/submission/fields', [ $this, 'listing_fields_visibility' ], 30, 2 );
		add_filter( 'mylisting/admin/submission/fields', [ $this, 'listing_fields_visibility' ], 30, 2 );

		// WC Subscriptions.
		Subscriptions::instance();
	}

	/**
	 * Add "Listing Package" product type.
	 *
	 * @since 1.6
	 */
	public function add_product_type( $types ) {
		$types['job_package'] = esc_html__( 'Listing Package', 'my-listing' );
		return $types;
	}

	/**
	 * Set custom product class for listing package products.
	 *
	 * @since 1.6
	 */
	public function set_product_class( $classname, $product_type ) {
		if ( $product_type === 'job_package' ) {
			return '\MyListing\Src\Paid_Listings\Product';
		}

		return $classname;
	}

	/**
	 * Product settings.
	 *
	 * @since 1.6
	 */
	public function add_product_settings() {
		require locate_template( 'templates/admin/single-product-screen/package-settings.php' );
	}

	/**
	 * Save Product Data
	 *
	 * @since 1.6
	 * @param int $post_id Product ID.
	 */
	public function save_product_settings( $post_id ) {
		// listing limit setting
		if ( ! empty( $_POST['_job_listing_limit'] ) ) {
			update_post_meta( $post_id, '_job_listing_limit', absint( $_POST['_job_listing_limit'] ) );
		} else {
			delete_post_meta( $post_id, '_job_listing_limit' );
		}

		// listing duration
		if ( ! empty( $_POST['_job_listing_duration'] ) ) {
			update_post_meta( $post_id, '_job_listing_duration', absint( $_POST['_job_listing_duration'] ) );
		} else {
			delete_post_meta( $post_id, '_job_listing_duration' );
		}

		// featured status
		update_post_meta( $post_id, '_job_listing_featured', ! empty( $_POST['_job_listing_featured'] ) ? 'yes' : 'no' );

		// mark verified setting
		update_post_meta( $post_id, '_listing_mark_verified', ! empty( $_POST['_listing_mark_verified'] ) ? 'yes' : 'no' );

		// use on claims
		update_post_meta( $post_id, '_use_for_claims', ! empty( $_POST['_use_for_claims'] ) ? 'yes' : 'no' );

		// disable repeat purchase
		update_post_meta( $post_id, '_disable_repeat_purchase', ! empty( $_POST['_disable_repeat_purchase'] ) ? 'yes' : 'no' );

		// Subscription type.
		if ( isset( $_POST['_job_listing_package_subscription_type'] ) ) {
			$type = 'package' === $_POST['_job_listing_package_subscription_type'] ? 'package' : 'listing';
			update_post_meta( $post_id, '_package_subscription_type', $type );
		}
	}

	/**
	 * Get the data from the session on page load
	 *
	 * @since 1.6
	 */
	public function get_cart_item_from_session( $cart_item, $values ) {
		if ( ! empty( $values['job_id'] ) ) {
			$cart_item['job_id'] = $values['job_id'];
			$cart_item['assignment_type'] = ! empty( $values['assignment_type'] ) ? $values['assignment_type'] : 'submission';
		}

		return $cart_item;
	}

	/**
	 * Set the order line item's meta data prior to being saved.
	 *
	 * @since 1.6
	 *
	 * @param WC_Order_Item_Product $order_item
	 * @param string                $cart_item_key  The hash used to identify the item in the cart
	 * @param array                 $cart_item_data The cart item's data.
	 * @param WC_Order              $order          The order or subscription object to which the line item relates
	 */
	public function checkout_create_order_line_item( $order_item, $cart_item_key, $cart_item_data, $order ) {
		if ( isset( $cart_item_data['job_id'] ) ) {
			$order_item->update_meta_data( '_job_id', $cart_item_data['job_id'] );
			if ( ! empty( $cart_item_data['assignment_type'] ) ) {
				$order_item->update_meta_data( '_assignment_type', $cart_item_data['assignment_type'] );
			}
		}
	}

	/**
	 * Output listing name in cart
	 *
	 * @since 1.6
	 */
	public function display_listing_in_cart( $data, $cart_item ) {
		if ( isset( $cart_item['job_id'] ) ) {
			$data[] = [
				'name'  => ! empty( $cart_item['assignment_type'] ) && $cart_item['assignment_type'] === 'claim' ? esc_html__( 'Claim for', 'my-listing' ) : esc_html__( 'Listing', 'my-listing' ),
				'value' => get_the_title( absint( $cart_item['job_id'] ) ),
			];
		}

		return $data;
	}

	/**
	 * When cart contains a listing package, always set to "yes".
	 *
	 * @since 1.6
	 */
	public function enable_signup_and_login_from_checkout( $value ) {
		global $woocommerce;
		$contain_listing = false;
		if ( ! empty( $woocommerce->cart->cart_contents ) ) {
			foreach ( $woocommerce->cart->cart_contents as $cart_item ) {
				$product = $cart_item['data'];
				if ( $product instanceof \WC_Product && $product->is_type( [ 'job_package', 'job_package_subscription' ] ) ) {
					$contain_listing = true;
				}
			}
		}

		return $contain_listing ? 'yes' : $value;
	}

	/**
	 * When cart contains a listing package, always set to "no".
	 *
	 * @since 1.6
	 */
	public function enable_guest_checkout( $value ) {
		global $woocommerce;
		$contain_listing = false;
		if ( ! empty( $woocommerce->cart->cart_contents ) ) {
			foreach ( $woocommerce->cart->cart_contents as $cart_item ) {
				$product = $cart_item['data'];
				if ( $product instanceof \WC_Product && $product->is_type( [ 'job_package', 'job_package_subscription' ] ) ) {
					$contain_listing = true;
				}
			}
		}

		return $contain_listing ? 'no' : $value;
	}

	/**
	 * Thank you page after checkout completed.
	 *
	 * @since 1.6
	 */
	public function woocommerce_thankyou( $order_id ) {
		global $wp_post_types;
		$order = wc_get_order( $order_id );
		$is_paid = in_array( $order->get_status(), [ 'completed', 'processing' ] );

		foreach ( $order->get_items() as $item ) {
			if ( ! isset( $item['job_id'] ) ) {
				continue;
			}

			$listing_status = get_post_status( $item['job_id'] );
			$is_claim = ! empty( $item['assignment_type'] ) && $item['assignment_type'] === 'claim';

			if ( $is_claim ) {
				if ( $is_paid ) {
					echo wpautop( sprintf( __( 'Your claim to %s has been submitted successfully.', 'my-listing' ), get_the_title( $item['job_id'] ) ) );
				} else {
					echo wpautop( sprintf( __( 'Your claim to %s will be processed after the order is completed.', 'my-listing' ), get_the_title( $item['job_id'] ) ) );
				}
			} else {
				switch ( get_post_status( $item['job_id'] ) ) {
					case 'pending' :
						echo wpautop( sprintf( __( '%s has been submitted successfully and will be visible once approved.', 'my-listing' ), get_the_title( $item['job_id'] ) ) );
					break;
					case 'pending_payment' :
					case 'expired' :
						echo wpautop( sprintf( __( '%s has been submitted successfully and will be visible once payment has been confirmed.', 'my-listing' ), get_the_title( $item['job_id'] ) ) );
					break;
					default :
						echo wpautop( sprintf( __( '%s has been submitted successfully.', 'my-listing' ), get_the_title( $item['job_id'] ) ) );
					break;
				}

				/**
				 * Make sure the `mylisting/submission/order-placed` hook is only
				 * run once after the order is done.
				 */
				$order_placed_handler_key = 'wc_paid_listings_order_placed_handler_'.absint( $item['job_id'] );
				if ( ! get_post_meta( $order_id, $order_placed_handler_key, true ) && ! get_post_meta( $order_id, '_subscription_renewal', true ) ) {
					do_action( 'mylisting/submission/order-placed', $item['job_id'] );
					update_post_meta( $order_id, $order_placed_handler_key, true );
				}
			}
			?>
			<p class="job-manager-submitted-paid-listing-actions">
				<?php
				if ( get_post_status( $item['job_id'] ) === 'publish' ) {
					echo '<a class="button" href="' . get_permalink( $item['job_id'] ) . '">' . __( 'View Listing', 'my-listing' ) . '</a> ';
				} else {
					echo '<a class="button" href="' . esc_url( wc_get_account_endpoint_url( \MyListing\my_listings_endpoint_slug() ) ) . '">' . __( 'Go to Dashboard', 'my-listing' ) . '</a> ';
				}
				?>
			</p>
			<?php
		}
	}

	/**
	 * Triggered when an order is paid
	 *
	 * @since 1.6
	 */
	public function order_paid( $order_id ) {
		$order = wc_get_order( $order_id );

		// Bail if already processed. Using WCPL prefix for back-compat.
		if ( get_post_meta( $order_id, 'wc_paid_listings_packages_processed', true ) ) {
			return;
		}

		foreach ( $order->get_items() as $item ) {
			$product = wc_get_product( $item['product_id'] );
			if ( ! ( $product->is_type( [ 'job_package' ] ) && $order->get_customer_id() ) ) {
				continue;
			}

			// Give packages to user
			for ( $i = 0; $i < $item['qty']; $i++ ) {
				$package = \MyListing\Src\Package::create( [
					'user_id'        => $order->get_customer_id(),
					'order_id'       => $order_id,
					'product_id'     => $product->get_id(),
					'duration'       => $product->get_duration(),
					'limit'          => $product->get_limit(),
					'featured'       => $product->is_listing_featured(),
					'mark_verified'  => $product->mark_verified(),
					'use_for_claims' => $product->use_for_claims(),
				] );

				// user package created & make sure listing id is set
				if ( ! ( $package && isset( $item['job_id'] ) ) ) {
					continue;
				}

				// validate listing
				$listing = \MyListing\Src\Listing::get( $item['job_id'] );
				if ( ! $listing ) {
					continue;
				}

				$assignment_type = ! empty( $item['assignment_type'] ) ? $item['assignment_type'] : 'submission';
				if ( ! empty( $item['is_claim'] ) ) {
					$assignment_type = 'claim'; // backwards compatibility pre v2.1.3
				}

				/**
				 * Order has been processed, payment package has been created, and a valid
				 * listing has been passed to the order. This can be used by Add Listing and
				 * other forms to modify the listing information, assign the package, etc.
				 *
				 * @since 2.1.6
				 */
				do_action( sprintf( 'mylisting/payments/%s/order-processed', $assignment_type ), $listing, $package );
			}
		}

		// mark order as processed
		update_post_meta( $order_id, 'wc_paid_listings_packages_processed', true );
	}

	/**
	 * Fires when a order was canceled. Looks for listing
	 * packages in order and deletes the package if found.
	 *
	 * @since 1.6
	 */
	public function order_cancelled( $order_id ) {
		$packages = get_posts( [
			'post_type'        => 'case27_user_package',
			'post_status'      => 'any',
			'posts_per_page'   => -1,
			'suppress_filters' => false,
			'fields'           => 'ids',
			'meta_query' => [
				'relation' => 'AND',
				[
					'key'     => '_order_id',
					'value'   => $order_id,
					'compare' => 'IN',
				],
			],
		] );

		if ( $packages && is_array( $packages ) ) {
			foreach ( $packages as $package_id ) {
				wp_update_post( [
					'ID'          => $package_id,
					'post_status' => 'case27_cancelled',
				] );
			}
		}
	}

	/**
	 * Disables repeat purchase for packages.
	 * Useful for allowing users to only have one free package plan.
	 *
	 * @since  1.6.3
	 *
	 * @param  bool        $purchasable
	 * @param  \WC_Product $product
	 * @return bool        $purchasable
	 */
	public function disable_repeat_purchase( $purchasable, $product ) {
	    if ( ! $product->is_type( [ 'job_package', 'job_package_subscription' ] ) ) {
	        return $purchasable;
	    }

	    if ( $product->get_meta( '_disable_repeat_purchase' ) !== 'yes' ) {
	        return $purchasable;
	    }

	    if ( wc_customer_bought_product( wp_get_current_user()->user_email, get_current_user_id(), $product->get_id() ) ) {
	        $purchasable = false;
	    }

	    return $purchasable;
	}

	/**
	 * Shows a "purchase disabled" message to the customer.
	 *
	 * @since 1.6.3
	 */
	public function purchase_disabled_message() {
	    global $product;

	    if ( ! $product->is_type( [ 'job_package', 'job_package_subscription' ] ) ) {
	        return false;
	    }

	    if ( $product->get_meta( '_disable_repeat_purchase' ) !== 'yes' ) {
	        return false;
	    }

	    if ( wc_customer_bought_product( wp_get_current_user()->user_email, get_current_user_id(), $product->get_id() ) ) {
	    	printf(
	    		'<div class="woocommerce"><div class="woocommerce-info wc-nonpurchasable-message">%s</div></div>',
	    		__( 'You\'ve already purchased this product! It can only be purchased once.', 'my-listing' )
	    	);
	    }
	}

	/**
	 * Display a nicename instead of the meta key in advanced order settings.
	 *
	 * @since 2.1
	 */
	public function display_meta_key( $display_key ) {
		if ( $display_key === '_job_id' ) {
			return _x( 'Listing ID', 'Product meta', 'my-listing' );
		}

		if ( $display_key === '_is_claim' || $display_key === '_assignment_type' ) {
			return _x( 'Type', 'Product meta', 'my-listing' );
		}

		return $display_key;
	}

	/**
	 * Display a formatted value instead of the default meta value in advanced order settings.
	 *
	 * @since 2.1
	 */
	public function display_meta_value( $value, $meta ) {
		if ( $meta->key === '_is_claim' ) {
			return (bool) $value ? _x( 'Claim', 'Product meta', 'my-listing' ) : _x( 'Submission', 'Product meta', 'my-listing' );
		}

		if ( $meta->key === '_assignment_type' ) {
			if ( $value === 'claim' ) {
				return _x( 'Claim', 'Product meta', 'my-listing' );
			} elseif ( $value === 'switch' ) {
				return _x( 'Switch Plan', 'Product meta', 'my-listing' );
			} else {
				return _x( 'Submission', 'Product meta', 'my-listing' );
			}
		}

		return $value;
	}

	/**
	 * Field visibility conditions handler.
	 *
	 * @since 1.0
	 */
	public function listing_fields_visibility( $fields, $listing ) {
		return array_filter( $fields, function( $field ) use ( $listing ) {
			$conditions = new \MyListing\Src\Conditions( $field, $listing );
			return $conditions->passes();
		} );
	}

	/**
	 * Register Listing Status.
	 *
	 * @since 1.6
	 */
	public function register_post_status() {
		register_post_status( 'pending_payment', [
			'label'                     => esc_html__( 'Pending Payment', 'my-listing' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => false,
			'show_in_admin_status_list' => true,
			// translators: %s is label count.
			'label_count'               => _n_noop( 'Pending Payment <span class="count">(%s)</span>', 'Pending Payment <span class="count">(%s)</span>', 'my-listing' ),
		] );
	}

	/**
	 * Set "Pending Payment" as Valid Status.
	 *
	 * @since 1.6
	 */
	public function add_valid_listing_status( $statuses ) {
		$statuses[] = 'pending_payment';
		$statuses[] = 'expired';
		$statuses[] = 'publish';
		return $statuses;
	}
}
