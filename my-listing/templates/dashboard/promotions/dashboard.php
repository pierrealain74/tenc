<?php
/**
 * User dashboard page for listing promotions.
 *
 * @since 1.7.0
 * @var   array $products List of WooCommerce products of type promotion package, ordered by price.
 * @var   array $packages List of promotion packages owned by the user.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! \MyListing\Src\User_Roles\user_can_add_listings() ) {
	printf(
		'<div class="element col-sm-6 text-center col-sm-offset-3">%s</div>',
		__( 'You cannot access this page.', 'my-listing' )
	);
	return;
}

/**
 * Fires before the template has been loaded.
 *
 * @since 1.7.0
 */
do_action( 'mylisting/promotions/templates/dashboard/before' );

$page = ! empty( $_GET['pg'] ) ? absint( $_GET['pg'] ) : 1;
$is_single = false; // Viewing a single package flag.

global $post;

$args = [
	'post_type'        => 'cts_promo_package',
	'post_status'      => 'publish',
	'posts_per_page'   => 10,
	'paged'            => $page,
	'meta_query'       => [
		[
			'key'          => '_user_id',
			'value'        => get_current_user_id(),
		],
        [
            'key' => '_listing_id',
            'compare' => 'EXISTS',
        ],
        [
            'key'     => '_listing_id',
            'value'   => '',
            'compare' => '!=',
        ],
	],
];

if ( ! empty( $_GET['package'] ) ) {
	$args['post__in'] = [ absint( $_GET['package'] ) ];
	$is_single = true;
}

// Active promotions.
$promotions = new \WP_Query( $args );
?>

<div class="user-promotion-packages">
<div class="row">
<div class="col-md-12">

<?php if ( $promotions->have_posts() ) : ?>
	<ul class="promo-product-list">
		<?php while ( $promotions->have_posts() ): $promotions->the_post();
			$package     = $post;
			$product_id  = absint( get_post_meta( $package->ID, '_product_id', true ) );
			$listing_id  = absint( get_post_meta( $package->ID, '_listing_id', true ) );
			$order_id    = absint( get_post_meta( $package->ID, '_order_id', true ) );
			$expiry_date = get_post_meta( $package->ID, '_expires', true );

			if ( ! ( $listing = get_post( $listing_id ) ) ) {
				continue;
			}

			$title = $listing->post_title;

			if ( ( $product = wc_get_product( $product_id ) ) && $product->is_type( 'promotion_package' ) ) {
				$product_link = sprintf( '<a href="%s">%s</a>', esc_url( $product->get_permalink() ), esc_html( $product->get_name() ) );
			}

			if ( ( $order = wc_get_order( $order_id ) ) && $order->get_customer_id() === get_current_user_id() && $order->is_paid() ) {
				$order_link = sprintf( '<a href="%s">' . _x( 'Order #%d', 'User Dashboard > Promotions', 'my-listing' ) . '</a>', esc_url( $order->get_view_order_url() ), $order->get_id() );
			}
			?>

            <li class="promo-product-item promo-package <?php echo esc_attr( sprintf( 'promotion-%s', $package->ID ) ) ?>">
                <div class="promo-item-icon">
                    <i class="icon-flash"></i>
                </div>
                <div class="promo-item-details">
                    <h5>
                    	<a href="<?php echo get_permalink( $listing ) ?>">
                    		<?php echo esc_html( $title ) ?>
                    	</a>
                    </h5>
                    <div class="promo-meta">
                    	<?php if ( ! empty( $product_link ) ): ?>
                    		<div class="product-link"><?php echo $product_link ?></div>
                    	<?php endif ?>
                        <?php if ( ! empty( $order_link ) ): ?>
                    		<div class="order-link"><?php echo $order_link ?></div>
                    	<?php endif ?>
                        <?php if ( strtotime( $expiry_date ) ): ?>
                    		<div class="order-expiry">
                    			<?php printf(
                    				_x( 'Expires in: %s', 'User Dashboard > Promotions > Expiry Date', 'my-listing' ),
                    				date_i18n( get_option( 'date_format' ), strtotime( $expiry_date ) )
                    			) ?>
                    		</div>
                    	<?php endif ?>
                    </div>
                </div>
                <a href="#" class="promo-item-action process-promotion" data-process="cancel-package" data-listing-id="<?php echo esc_attr( $listing->ID ) ?>">
                	<?php _ex( 'Cancel Package', 'Promote listing dialog - cancel package button', 'my-listing' ) ?>
                </a>
            </li>
		<?php endwhile ?>
	</ul>

	<?php if ( $promotions->max_num_pages > 1 ): ?>
		<div class="pagination center-button">
			<?php echo paginate_links( [
				'format'  => '?pg=%#%',
				'current' => $page,
				'total'   => $promotions->max_num_pages,
				] );
				wp_reset_postdata(); ?>
		</div>
	<?php endif ?>
<?php else: ?>
	<div class="no-listings">
		<i class="no-results-icon material-icons mood_bad"></i>
		<?php _ex( 'You don\'t have any promoted listings yet.', 'User Dashboard > Promotions > No Active Packages.', 'my-listing' ) ?>
	</div>
<?php endif ?>
</div>
</div>
</div>

<?php
/**
 * Fires after the template has been loaded.
 *
 * @since 1.7.0
 */
do_action( 'mylisting/promotions/templates/dashboard/after' );
