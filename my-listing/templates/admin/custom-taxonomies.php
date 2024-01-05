<?php
/**
 * The template for WP Admin > Listings > Taxonomies screen.
 *
 * @since 2.1
 */
if ( ! defined('ABSPATH') ) {
	exit;
} ?>

<div class="wrap mylisting-settings-wrap">
	<form class="mylisting-options" method="post" action="options.php">
		<?php
		if ( ! empty( $_GET['settings-updated'] ) ) {
			flush_rewrite_rules();
			echo '<div class="updated"><p>' . esc_html__( 'Settings successfully saved!', 'my-listing' ) . '</p></div>';
		}

		settings_fields( 'mylisting_custom_taxonomies' ); ?>

		<div class="m-form-section">
			<h3 class="m-heading mb10"><?php _ex( 'Custom Taxonomies', 'WP Admin > Listings > Taxonomies', 'my-listing' ) ?></h3>
			<p class="mt0 mb10"><?php _ex( 'You can manage/add/remove custom listing taxonomies in this page.', 'WP Admin > Listings > Taxonomies', 'my-listing' ) ?></p>
			<?php // @todo: links to docs on using custom taxonomies ?>

			<div id="settings-taxonomies" style="max-width: 550px; margin-top: 30px;">
				<section class="section tabs-content" id="section-result-template">
				    <div class="fields-wrapper">
				        <div class="fields-draggable">
				            <div class="taxonomy-fields" id="c27-custom-taxonomies"></div>

							<div class="text-center mt30">
					            <a class="btn btn-outline" id="c27-add-taxonomy">
					                <?php esc_html_e('Add Taxonomy', 'my-listing'); ?>
					            </a>
				            </div>
				        </div>
				    </div>
				</section>
			</div>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'my-listing' ); ?>" />
			</p>
		</div>
	</form>
</div>

<script type="text/html" id="tmpl-c27-custom-taxonomies">

    <# data.taxonomies.forEach( ( settings, id ) => { #>

    <div class="head-button row-item" draggable="false" data-taxonomy>
        <div class="row-head" data-edit-btn>
			<div class="row-head-toggle"><i class="mi chevron_right"></i></div>
			<div class="row-head-label">
				<h4 data-label>{{{ settings.label || '(click to edit)' }}}</h4>
				<div class="details">
					<div class="detail" data-slug>{{{ settings.slug }}}</div>
				</div>
			</div>
			<div class="row-head-actions">
				<span title="Remove Taxonomy" data-delete-btn class="action red"><i class="mi delete"></i></span>
			</div>
        </div>

        <div class="row-edit">
            <div class="form-group">
                <label><?php esc_html_e('Label', 'my-listing'); ?></label>
                <input name="job_manager_custom_taxonomy[{{{data.count}}}][label]" type="text" class="regular-text" value="{{{ settings.label }}}" data-field-label />
            </div>

            <div class="form-group">
                <label><?php esc_html_e('Taxonomy Slug', 'my-listing'); ?></label>
                <input name="job_manager_custom_taxonomy[{{{data.count}}}][slug]" type="text" class="regular-text" value="{{{ settings.slug }}}" data-field-slug {{{ ! settings.can_edit_slug ? 'readonly' : '' }}}/>
            </div>

			<div class="text-right">
				<div class="btn" data-edit-btn>Done</div>
			</div>
        </div>
    </div>

    <# data.count++
    }); #>
</script>