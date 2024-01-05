<?php
/**
 * Listing type selection step.
 *
 * @since 2.0
 * @var   array $data
 */

$cardClasses = [
	'small' => 'col-md-3',
	'medium' => 'col-md-4',
	'large' => 'col-md-6',
];

$cardClass = in_array( $data['size'], array_keys( $cardClasses ) ) ? $cardClasses[$data['size']] : $cardClasses['medium'];
wp_print_styles( 'mylisting-listing-categories-widget' );
?>

<section class="i-section choose-type">
	<div class="container-fluid">
		<div class="row section-body">
		<?php foreach ($data['listing_types'] as $listing_type):
			if ( ! ( $type_obj = get_page_by_path( $listing_type['listing_type'], OBJECT, 'case27_listing_type' ) ) ) {
				continue;
			}

			$type = \MyListing\Src\Listing_Type::get( $type_obj );
			$link = get_permalink();
			$args = [ 'listing_type' => $type->get_slug() ];
			if ( ! empty( $_GET['selected_package'] ) ) {
				$args['selected_package'] = absint( $_GET['selected_package'] );
			}
			?>
			<div class="<?php echo esc_attr( $cardClass ) ?> col-sm-6 col-xs-12 ac-category">
				<div class="cat-card">
					<a href="<?php echo esc_url( add_query_arg( $args, get_permalink() ) ) ?>">
						<div class="ac-front-side face">
							<div class="hovering-c">
								<span class="cat-icon" style="color: #fff; background-color: <?php echo esc_attr( $listing_type['color'] ) ?>">
									<?php echo $type->get_icon(); ?>
								</span>
								<span class="category-name"><?php echo esc_attr( $type->get_singular_name() ) ?></span>
							</div>
						</div>
						<div class="ac-back-side face" style="background-color: <?php echo esc_attr( $listing_type['color'] ) ?>">
							<div class="hovering-c">
								<p><?php _e( 'Choose type', 'my-listing' ) ?></p>
							</div>
						</div>
					</a>
				</div>
			</div>
		<?php endforeach ?>
		</div>
	</div>
</section>