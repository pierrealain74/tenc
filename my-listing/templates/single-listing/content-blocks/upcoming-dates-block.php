<?php
/**
 * Template for rendering a `upcoming-dates` block in single listing page.
 *
 * @since 1.0
 */
if ( ! defined('ABSPATH') ) {
	exit;
}

$dates = $block->get_dates();
if ( empty( $dates ) ) {
	return;
} ?>

<div class="<?php echo esc_attr( $block->get_wrapper_classes() ) ?>" id="<?php echo esc_attr( $block->get_wrapper_id() ) ?>">
	<div class="element content-block upcoming-dates-block">
		<div class="pf-head">
			<div class="title-style-1">
				<i class="<?php echo esc_attr( $block->get_icon() ) ?>"></i>
				<h5><?php echo esc_html( $block->get_title() ) ?></h5>
			</div>
		</div>
		<div class="pf-body">
			<ul class="event-dates-timeline" data-field="<?php echo esc_attr( $block->get_prop( 'show_field' ) ) ?>" data-count="<?php echo absint( $block->get_prop('count') ) ?>" data-past-count="<?php echo absint( $block->get_prop('past_count') ) ?>">
				<?php foreach ( $dates as $index => $date ) : ?>
					<li class="upcoming-event-date <?php echo $date['is_over'] ? 'event-ended' : '' ?>">
						<i class="fa fa-calendar-alt"></i>
						<span>
	    					<?php echo \MyListing\Src\Recurring_Dates\display_instance( $date, $block->get_date_modifier() ) ?>
						</span>

						<?php if ( $block->get_prop( 'show_add_to_gcal' ) && ! $date['is_over'] ): ?>
							<a class="add-to-google-cal" target="_blank" rel="nofollow" href="<?php echo esc_url( $date['gcal_link'] ) ?>">
								<i class="fab fa-google"></i>
								<?php _e( 'Add to Google Calendar', 'my-listing' ) ?>
							</a>
						<?php endif ?>

						<?php if ( $block->get_prop( 'show_add_to_ical' ) && ! $date['is_over'] ): ?>
							<a class="add-to-i-cal" href="javascript:void(0)" data-event-id="<?php echo absint( $index ) ?>">
								<i class="fas fa-calendar-alt"></i>
								<?php _e( 'Add to iCalendar', 'my-listing' ) ?>
							</a>
						<?php endif; ?>

						<?php if ( $date['is_over'] ) : ?>
							<a class="add-to-google-cal" href="javascript:void(0)">
								<i class="fa fa-check"></i>
	    						<?php echo \MyListing\Src\Recurring_Dates\display_instance( $date,'status' ) ?>
							</a>
						<?php endif ?>
					</li>
				<?php endforeach ?>
			</ul>
		</div>
	</div>
</div>