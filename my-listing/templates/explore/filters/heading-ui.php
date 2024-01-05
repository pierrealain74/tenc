<?php
/**
 * Template for rendering a `heading` UI form elemnent in Explore page.
 *
 * @since 2.4
 */
if ( ! defined('ABSPATH') ) {
	exit;
} ?>

<div class="form-group explore-filter heading-ui">
	<h3><?php echo esc_html( $filter->get_label() ) ?></h3>
</div>