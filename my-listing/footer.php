<?php

// End of #c27-site-wrapper div.
printf('</div>');

if ( apply_filters( 'mylisting/hide-footer', false ) === true ) {
	// don't render a footer
} elseif ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'footer' ) ) {
	/**
	 * An Elementor Pro custom footer is available.
	 *
	 * @link  https://developers.elementor.com/theme-locations-api/migrating-themes/
	 * @since 2.0
	 */
} elseif ( function_exists( 'hfe_render_footer' ) && function_exists( 'get_hfe_footer_id' ) && get_hfe_footer_id() ) {
	/**
	 * An "Header, Footer & Blocks for Elementor" footer is available.
	 *
	 * @link  https://github.com/Nikschavan/header-footer-elementor/wiki/Adding-Header-Footer-Elementor-support-for-your-theme
	 * @since 2.1
	 */
	hfe_render_footer();
} else {
	/**
	 * No custom footers detected, use the default theme footer.
	 *
	 * @since 1.0
	 */
	$show_footer = c27()->get_setting( 'footer_show', true ) !== false;
	if ( $show_footer && isset( $GLOBALS['c27_elementor_page'] ) && $page = $GLOBALS['c27_elementor_page'] ) {
		if ( ! $page->get_settings('c27_hide_footer') ) {
			$args = [
				'show_widgets'      => $page->get_settings('c27_footer_show_widgets'),
				'show_footer_menu'  => $page->get_settings('c27_footer_show_footer_menu'),
			];

			c27()->get_section('footer', ($page->get_settings('c27_customize_footer') == 'yes' ? $args : []));
		}
	} elseif ( $show_footer ) {
		c27()->get_section('footer');
	}
}

// MyListing footer hooks.
do_action( 'case27_footer' );
do_action( 'mylisting/get-footer' );

wp_footer();

?>
</body>
</html>