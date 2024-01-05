<?php
/**
 * "Theme Options" page in "WP Admin > Theme Tools"
 *
 * @since 2.2.3
 */

namespace MyListing\Src\Theme_Options;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Theme_Options {

	public static function boot() {
		add_action( 'admin_menu', [ __CLASS__, 'add_theme_options_page' ], 50 );

		Data_Updater\Data_Updater::instance();
		Preview_Cards::instance();
		Listing_Stats::instance();
		Map_Services::instance();
	}

	/**
	 * Add theme options page in WP Admin > Theme Tools.
	 *
	 * @since 2.2.3
	 */
	public static function add_theme_options_page() {
		add_submenu_page(
			'case27/tools.php',
			_x( 'Performance', 'WP Admin > Theme Tools > Performance', 'my-listing' ),
			_x( 'Performance', 'WP Admin > Theme Tools > Performance', 'my-listing' ),
			'manage_options',
			'mylisting-options',
			function() {
				$tabs = apply_filters( 'mylisting/options-page', [] );
				$active_tab = ! empty( $_GET['active_tab'] ) && isset( $tabs[ $_GET['active_tab'] ] ) ? $_GET['active_tab'] : key( $tabs );
				$subtabs = apply_filters( 'mylisting/options-page/'.$active_tab.'/subtabs', [ 'main' => 'Main' ] );
				$subtab = ! empty( $_GET['subtab'] ) && isset( $subtabs[ $_GET['subtab'] ] ) ? $_GET['subtab'] : key( $subtabs );
				$url = admin_url( 'admin.php?page=mylisting-options' ); ?>
					<style type="text/css">.wp-core-ui .notice, .update-nag { display: none; }</style>
					<div class="cts-pagewrap">
						<div class="cts-tabs">
							<?php foreach ( $tabs as $key => $label ): ?>
								<a href="<?php echo esc_url( add_query_arg( 'active_tab', $key, $url ) ) ?>" class="cts-tab <?php echo $active_tab === $key ? 'cts-tab-active' : '' ?>">
									<?php echo $label ?>
								</a>
							<?php endforeach ?>
						</div>
						<?php if ( count( $subtabs ) > 1 ): ?>
							<div class="cts-subtabs">
								<?php foreach ( $subtabs as $key => $label ): ?>
									<a
										href="<?php echo esc_url( add_query_arg( [ 'active_tab' => $active_tab, 'subtab' => $key ], $url ) ) ?>"
										class="cts-subtab <?php echo $subtab === $key ? 'cts-subtab-active' : '' ?>"
									><?php echo $label ?></a>
								<?php endforeach ?>
							</div>
						<?php endif ?>
						<div class="cts-pagecontent">
							<?php do_action( 'mylisting/options-page/'.$active_tab.':'.$subtab ) ?>
						</div>
					</div>
				<?php
			}
		);
	}
}