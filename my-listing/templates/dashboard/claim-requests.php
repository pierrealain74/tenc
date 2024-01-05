<?php
/**
 * User dashboard page for claim requests.
 *
 * @since 2.1
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$claim_id = ! empty( $_GET['claim-id'] ) ? absint( $_GET['claim-id'] ) : false;
$is_single = (bool) $claim_id;

$args = [
	'post_type' => 'claim',
	'post_status' => 'publish',
	'posts_per_page' => -1,
	'meta_key' => '_user_id',
	'meta_value' => get_current_user_id(),
];

if ( $claim_id ) {
	$args['post__in'] = (array) $claim_id;
}

$claims = get_posts( $args );
$claim_count = 0;
?>

<div class="mlduo-welcome-message">
	<?php if ( $is_single ): ?>
		<h1><?php _ex( 'Claim Details', 'User dashboard', 'my-listing' ) ?></h1>
	<?php else: ?>
		<h1><?php _ex( 'Claim Requests', 'User dashboard', 'my-listing' ) ?></h1>
	<?php endif ?>
</div>

<div class="user-claim-requests">
	<div class="row">
		<?php foreach ( $claims as $claim ):
			$listing = \MyListing\Src\Listing::get( $claim->_listing_id );
			$claim_status = \MyListing\Src\Claims\Claims::get_claim_status( $claim->ID );
			$claim_date = get_the_date( get_option( 'date_format' ), $claim->ID );
			if ( ! $listing ) {
				continue;
			}
			?>
			<div class="col-md-4">
				<div class="claim-info">
					<img src="<?php echo $listing->get_logo('thumbnail') ?: c27()->image( 'marker.jpg' ) ?>">
					<h4>
						<a
							href="<?php echo esc_url( $listing->get_link() ) ?>"
							title="<?php echo esc_attr( sprintf( _x( 'Claim #%d', 'User dashboard', 'my-listing' ), $claim->ID ) ) ?>"
						><?php echo $listing->get_name() ?></a>
					</h4>
					<p>
						<span class="<?php echo esc_attr( $claim->_status ) ?>" title="<?php echo esc_attr( _x( 'Claim status', 'User dashboard', 'my-listing' ) ) ?>"><?php echo $claim_status ?></span>
						&middot;
						<span class="submitted-on" title="<?php echo esc_attr( _x( 'Claim submitted on', 'User dashboard', 'my-listing' ) ) ?>"><?php echo $claim_date ?></span>
					</p>
				</div>
			</div>
			<?php $claim_count++ ?>
		<?php endforeach ?>

		<?php if ( $claim_count === 0 ): ?>
			<p class="text-center"><?php _ex( 'No claim requests to show.', 'User dashboard', 'my-listing' ) ?></p>
		<?php endif ?>
	</div>
</div>
