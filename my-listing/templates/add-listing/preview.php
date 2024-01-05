<?php
/**
 * Template for `Preview` step in Add Listing page.
 *
 * @since 1.0
 */

// Load single listing scripts.
wp_add_inline_script( 'mylisting-single', 'MyListing.isListingPreview = true;', 'before' );
wp_enqueue_script( 'mylisting-single' );
wp_print_styles( 'mylisting-single-listing' );

// Hide similar listings in preview step.
add_filter( 'mylisting/single/show-similar-listings', '__return_false' );
?>
<form method="post" id="job_preview" action="<?php echo esc_url( $form->get_action() ); ?>" novalidate>
	<div class="job_listing_preview_title">
		<input type="submit" name="continue" id="job_preview_submit_button" class="button buttons button-2 job-manager-button-submit-listing" value="<?php echo esc_attr( _x( 'Submit Listing', 'Add Listing > Preview Step', 'my-listing' ) ) ?>">
		<input type="submit" name="edit_job" class="button job-manager-button-edit-listing buttons button-5" value="<?php _e( 'Edit listing', 'my-listing' ); ?>">
	</div>

	<div class="job_listing_preview single_job_listing single-listing">

		<?php get_template_part( 'templates/listing' ) ?>

		<input type="hidden" name="job_id" value="<?php echo esc_attr( $form->get_job_id() ) ?>">
		<input type="hidden" name="step" value="<?php echo esc_attr( $form->get_step() ) ?>">
		<input type="hidden" name="job_manager_form" value="<?php echo esc_attr( $form->get_form_name() ) ?>">
		<?php if ( ! empty( $_REQUEST['listing_package'] ) ): ?>
			<input type="hidden" name="listing_package" value="<?php echo esc_attr( $_REQUEST['listing_package'] ) ?>">
		<?php endif ?>
	</div>
</form>
