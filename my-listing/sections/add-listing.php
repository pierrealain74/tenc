<?php
$data = c27()->merge_options([
	'listing_types' => [],
	'is_edit_mode' => false,
	'size' => 'medium',
	'packages_layout' => 'regular',
	'form_section_animation' => 'yes',
], $data);

if ( ! apply_filters( 'mylisting/show-add-listing-widget', true ) ) {
	return;
}

$GLOBALS['cts-add-listing-config'] = $data;
$listing_type = false;
$listing_id = false;

if ( is_array( $data['listing_types'] ) && count( $data['listing_types'] ) === 1 ) {
	$listing_type = $data['listing_types'][0]['listing_type'];
	$_REQUEST['listing_type'] = $_GET['listing_type'] = $listing_type;
}

if ( ! empty( $_GET['listing_type'] ) && in_array( $_GET['listing_type'], array_column( $data['listing_types'], 'listing_type' ) ) ) {
	$listing_type = sanitize_text_field( $_GET['listing_type'] );
	$_REQUEST['listing_type'] = $_GET['listing_type'] = $listing_type;
}

if ( ! empty( $_GET['listing'] ) && ( $listing = \MyListing\Src\Listing::get( $_GET['listing'] ) ) && $listing->type && $listing->editable_by_current_user() ) {
	$listing_type = $listing->type->get_slug();
	$_GET['job_id'] = $listing->get_id();
	$_REQUEST['listing_type'] = $_GET['listing_type'] = $listing_type;
}
?>

<?php if ( $listing_type ): ?>
	<style type="text/css">
		/* Hide Elementor on Add-Listing form steps. The step contents are moved outside the Elementor container using JS. */
		body:not(.elementor-editor-active) .elementor:not([data-elementor-type=header]):not([data-elementor-type=footer]) {
		    display: none;
		}

		<?php if ( $data['form_section_animation'] !== 'yes' ): ?>
			/* Disable add listing form section animation */
			#submit-job-form .form-section { opacity: 1 !important; transform: scale(1) !important; }
		<?php endif ?>
	</style>
	<script type="text/javascript">
		// Add body class to help target this screen via css.
		document.body.classList.add('add-listing-form');
	</script>

	<div class="add-listing-step" style="opacity:0;">
		<?php do_action('case27_add_listing_form_template_start', $listing_type) ?>

		<?php MyListing\Src\Forms\Add_Listing_Form::instance()->render() ?>

		<?php if ($data['is_edit_mode']): ?>
			<script type="text/javascript">case27_ready_script(jQuery);</script>
		<?php endif ?>
	</div>

	<script type="text/javascript">
		/**
		 * Add listing page - move step contents
		 * outside of Elementor container.
		 *
		 * @since 2.0
		 */
		(function() {
			var step = jQuery('.add-listing-step');
			if ( ! step.length || jQuery('body').hasClass('elementor-editor-active') ) {
				return;
			}

			step.appendTo('#c27-site-wrapper');

			// Trigger resize event.
			var evt = window.document.createEvent('UIEvents');
			evt.initUIEvent('resize', true, false, window, 0);
			window.dispatchEvent(evt);
		})();
	</script>
<?php else: ?>
	<?php require locate_template( 'templates/add-listing/choose-type.php' ) ?>
<?php endif ?>

