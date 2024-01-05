<?php

namespace MyListing\Ext\WooCommerce\Requests;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Get_Products {
	use \MyListing\Src\Traits\Instantiatable;

	public function __construct() {
		add_action( 'wp_ajax_mylisting_get_products', [ $this, 'get_products' ] );
		add_action( 'wp_ajax_nopriv_mylisting_get_products', [ $this, 'get_products' ] );
	}

	public function get_products() {
		check_ajax_referer( 'c27_ajax_nonce', 'security' );

		if ( empty( $_POST['author_id'] ) ) {
			return;
		}

		$products  = array_map( 'absint', isset( $_POST['products'] ) ? (array) $_POST['products'] : [] );
		$page      = absint( isset( $_POST['page'] ) ? $_POST['page'] : 0 );
		$author_id = absint( isset( $_POST['author_id'] ) ? $_POST['author_id'] : 0 );
		$per_page  = 9;
		$form_data = isset( $_POST['form_data'] ) ? $_POST['form_data'] : [];

		$args = [
			'post_type' => 'product',
			'post_status' => 'publish',
			'posts_per_page' => $per_page,
			'offset' => $page * $per_page,
			'post__in' => ! empty( $products ) ? $products : [0],
			'orderby' => 'post__in',
			'order' => 'DESC',
            'author' => $author_id,
		];

		// dd($args);
		add_filter( 'loop_shop_columns', function() {
			return 3;
		} );

		$products = new \WP_Query($args);
		$response = [];

		ob_start();
		if ( $products->have_posts() ): ?>
			<?php while ( $products->have_posts() ) : $products->the_post(); ?>
				<?php wc_get_template_part( 'content', 'product' ) ?>
			<?php endwhile ?>
		<?php else: ?>
			<div class="no-results-wrapper">
				<i class="no-results-icon material-icons mood_bad"></i>
				<li class="no_job_listings_found"><?php _e( 'There are no products matching your search.', 'my-listing' ) ?></li>
			</div>
		<?php endif;

		$response['html'] = ob_get_clean();
		$response['pagination'] = c27()->get_listing_pagination( $products->max_num_pages, ( $page + 1 ) );
		$response['count'] = $products->found_posts;
		$response['formatted_count'] = number_format_i18n( $products->found_posts );

		wp_send_json( $response );
	}
}
