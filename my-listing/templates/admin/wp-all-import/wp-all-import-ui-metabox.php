<?php
/**
 * Renders the MyListing custom metabox in WP All Import import settings.
 *
 * @since 2.6
 */
if ( ! defined('ABSPATH') ) {
	exit;
} ?>
<div class="wpallimport-collapsed wpallimport-section wpallimport-addon mylisting-addon <?php echo \MyListing\is_dev_mode()?'':'closed' ?>">
	<div class="wpallimport-content-section">
		<div class="wpallimport-collapsed-header">
			<h3>MyListing Add-On</h3>
		</div>
		<div class="wpallimport-collapsed-content" style="padding: 0;">
			<div class="wpallimport-collapsed-content-inner">
				<?php foreach ( $type->get_fields() as $field ): ?>
					<div class="field-group">
						<?php \MyListing\Apis\Wp_All_Import\render_field( $field, $values ) ?>
					</div>
				<?php endforeach ?>

				<h4>Listing Settings</h4>
				<?php \MyListing\Apis\Wp_All_Import\render_settings( $values ) ?>
			</div>
		</div>
	</div>
</div>

