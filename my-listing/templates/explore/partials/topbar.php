<?php
/**
 * Template for displaying listing types as tabs.
 *
 * @since 2.0
 */
if ( ! defined('ABSPATH') ) {
	exit;
}

if ( count( $explore->types ) <= 1 ) {
    return;
} ?>

<div class="explore-head" v-show="!(isMobile && state.mobileTab==='filters')">
	<div class="explore-types cts-carousel">
		<div class="finder-title">
			<h2 class="case27-primary-text"><?php echo esc_html( $data['title'] ) ?></h2>
		</div>
		<?php foreach ( $explore->types as $listing_type ): ?>
			<div class="type-<?php echo esc_attr( $listing_type->get_slug() ) ?> item"
				 :class="activeType.slug === '<?php echo esc_attr( $listing_type->get_slug() ) ?>'  ? 'active' : ''">
				<a @click.prevent="setType( <?php echo c27()->encode_attr( $listing_type->get_slug() ) ?> )">
					<div class="type-info">
						<?php echo $listing_type->get_icon(); ?>
						<h4><?php echo esc_html( $listing_type->get_plural_name() ) ?></h4>
					</div>
				</a>
			</div>
		<?php endforeach ?>
		<div class="cts-prev">prev</div>
		<div class="cts-next">next</div>
	</div>
</div>