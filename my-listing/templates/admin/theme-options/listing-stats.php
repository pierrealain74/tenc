<?php
/**
 * Template for rendering listing-stats settings.
 *
 * @since 2.3.4
 * @var $config
 */
if ( ! defined('ABSPATH') ) {
	exit;
}

if ( ! empty( $_GET['success'] ) ) {
	echo '<div class="updated"><p>'.esc_html__( 'Settings successfully saved!', 'my-listing' ).'</p></div>';
}
?>
<div class="wrap">
	<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ) ?>" method="POST">
		<h1 class="m-heading mb30">Listing Stats</h1>
		<div class="form-group mb30">
			<h4 class="m-heading mb5">Cache stats for (minutes)</h4>
			<p class="mt0 mb10">Set how long the user stats cache should last before it's regenerated.</p>
			<input type="number" class="m-input" style="max-width: 170px;" placeholder="60 minutes" name="cache_time" value="<?php echo esc_attr( $config['cache_time'] ) ?>">
		</div>

		<div class="form-group mb30">
			<h4 class="m-heading mb5">Delete stats older than (days)</h4>
			<p class="mt0 mb10">Set how long visit stats will be saved in the database. This is done to avoid database bloat, and performance hits as a result.</p>
			<input type="number" class="m-input" style="max-width: 170px;" placeholder="" min="0" name="db_time" value="<?php echo esc_attr( $config['db_time'] ) ?>">
		</div>

		<h3 class="m-heading mb30 mt40">Stat Boxes</h3>
		<div style="max-width:700px;">
			<div class="form-group mb20 dibvt switch-setting" style="width:270px;">
				<h4 class="m-heading mb5">Enable Referrers</h4>
				<p class="mt0 mb10">Displays a list of top referrers to your listings.</p>
				<label class="form-switch">
					<input type="checkbox" name="show_referrers" value="1" <?php checked( true, (bool) $config['show_referrers'] ) ?>>
					<span class="switch-slider"></span>
				</label>
			</div>

			<div class="form-group mb20 dibvt switch-setting" style="width:270px;">
				<h4 class="m-heading mb5">Enable Browsers</h4>
				<p class="mt0 mb10">Displays a list of top browsers.</p>
				<label class="form-switch">
					<input type="checkbox" name="show_browsers" value="1" <?php checked( true, (bool) $config['show_browsers'] ) ?>>
					<span class="switch-slider"></span>
				</label>
			</div>

			<div class="form-group mb20 dibvt switch-setting" style="width:270px;">
				<h4 class="m-heading mb5">Enable Platforms</h4>
				<p class="mt0 mb10">Displays a list of top platforms (operating systems).</p>
				<label class="form-switch">
					<input type="checkbox" name="show_platforms" value="1" <?php checked( true, (bool) $config['show_platforms'] ) ?>>
					<span class="switch-slider"></span>
				</label>
			</div>

			<div class="form-group mb20 dibvt switch-setting" style="width:270px;">
				<h4 class="m-heading mb5">Enable Countries</h4>
				<p class="mt0 mb10">Displays a list of top countries.</p>
				<label class="form-switch">
					<input type="checkbox" name="show_countries" value="1" <?php checked( true, (bool) $config['show_countries'] ) ?>>
					<span class="switch-slider"></span>
				</label>
			</div>

			<div class="form-group mb20 dibvt switch-setting" style="width:270px;">
				<h4 class="m-heading mb5">Enable Devices</h4>
				<p class="mt0 mb10">Displays the visits share between mobile/desktop.</p>
				<label class="form-switch">
					<input type="checkbox" name="show_devices" value="1" <?php checked( true, (bool) $config['show_devices'] ) ?>>
					<span class="switch-slider"></span>
				</label>
			</div>

			<div class="form-group mb20 dibvt switch-setting" style="width:270px;">
				<h4 class="m-heading mb5">Enable Views block</h4>
				<p class="mt0 mb10">Displays visit stats for the last day, last 7 days and last 30 days.</p>
				<label class="form-switch">
					<input type="checkbox" name="show_views" value="1" <?php checked( true, (bool) $config['show_views'] ) ?>>
					<span class="switch-slider"></span>
				</label>
			</div>

			<div class="form-group mb20 dibvt switch-setting" style="width:270px;">
				<h4 class="m-heading mb5">Enable Unique Views block</h4>
				<p class="mt0 mb10">Displays unique visit stats for the last day, last 7 days and last 30 days.</p>
				<label class="form-switch">
					<input type="checkbox" name="show_uviews" value="1" <?php checked( true, (bool) $config['show_uviews'] ) ?>>
					<span class="switch-slider"></span>
				</label>
			</div>

			<div class="form-group mb20 dibvt switch-setting" style="width:270px;">
				<h4 class="m-heading mb5">Enable Tracks block</h4>
				<p class="mt0 mb10">Displays click stats for quick action and cover actions in single listing page.</p>
				<label class="form-switch">
					<input type="checkbox" name="show_tracks" value="1" <?php checked( true, (bool) $config['show_tracks'] ) ?>>
					<span class="switch-slider"></span>
				</label>
			</div>
		</div>

		<h3 class="m-heading mb30 mt20">Visits Chart</h3>
		<div class="form-group mb30 dibvt" style="margin-right:40px;">
			<h4 class="m-heading mb10">Enable chart</h4>
			<label class="form-switch">
				<input type="checkbox" name="enable_chart" value="1" <?php checked( true, (bool) $config['enable_chart'] ) ?>>
				<span class="switch-slider"></span>
			</label>
		</div>

		<div class="form-group mb30 dibvt">
			<h4 class="m-heading mb15">Chart Categories</h4>
			<label>
				<input type="checkbox" class="form-checkbox" name="chart_categories[]" value="lastday" <?php checked( in_array( 'lastday', $config['chart_categories'] ) ) ?>> Last 24 hours &nbsp;
			</label>
			<label>
				<input type="checkbox" class="form-checkbox" name="chart_categories[]" value="lastweek" <?php checked( in_array( 'lastweek', $config['chart_categories'] ) ) ?>> Last 7 days &nbsp;
			</label>
			<label>
				<input type="checkbox" class="form-checkbox" name="chart_categories[]" value="lastmonth" <?php checked( in_array( 'lastmonth', $config['chart_categories'] ) ) ?>> Last 30 days &nbsp;
			</label>
			<label>
				<input type="checkbox" class="form-checkbox" name="chart_categories[]" value="lasthalfyear" <?php checked( in_array( 'lasthalfyear', $config['chart_categories'] ) ) ?>> Last 6 months &nbsp;
			</label>
			<label>
				<input type="checkbox" class="form-checkbox" name="chart_categories[]" value="lastyear" <?php checked( in_array( 'lastyear', $config['chart_categories'] ) ) ?>> Last 12 months
			</label>
		</div>

		<h3 class="m-heading mb30 mt20">Color Palette</h3>
		<div class="form-group mb30">
			<div class="dibvt" style="padding-right:20px;">
				<h4 class="m-heading mb10">Color #1</h4>
				<input type="text" value="<?php echo esc_attr( $config['color1'] ) ?>" data-default-color="#6c1cff" class="cts-color-picker" name="color1"></input>
			</div>

			<div class="dibvt" style="padding-right:20px;">
				<h4 class="m-heading mb10">Color #2</h4>
				<input type="text" value="<?php echo esc_attr( $config['color2'] ) ?>" data-default-color="#911cff" class="cts-color-picker" name="color2"></input>
			</div>

			<div class="dibvt" style="padding-right:20px;">
				<h4 class="m-heading mb10">Color #3</h4>
				<input type="text" value="<?php echo esc_attr( $config['color3'] ) ?>" data-default-color="#6c1cff" class="cts-color-picker" name="color3"></input>
			</div>

			<div class="dibvt" style="padding-right:20px;">
				<h4 class="m-heading mb10">Color #4</h4>
				<input type="text" value="<?php echo esc_attr( $config['color4'] ) ?>" data-default-color="#0079e0" class="cts-color-picker" name="color4"></input>
			</div>
		</div>

		<div class="form-group mb30">
			<div class="dibvt" style="padding-right:20px;">
				<h4 class="m-heading mb10"><em>Views</em> series color</h4>
				<input type="text" value="<?php echo esc_attr( $config['views_color'] ) ?>" data-default-color="#0079e0" class="cts-color-picker" name="views_color"></input>
			</div>
			<div class="dibvt">
				<h4 class="m-heading mb10"><em>Unique Views</em> series color</h4>
				<input type="text" value="<?php echo esc_attr( $config['uviews_color'] ) ?>" data-default-color="#911cff" class="cts-color-picker" name="uviews_color"></input>
			</div>
		</div>

		<div class="mt60">
			<input type="hidden" name="action" value="mylisting_update_userdash">
			<input type="hidden" name="page" value="theme-stats-settings">
			<input type="hidden" name="_wpnonce" value="<?php echo esc_attr( wp_create_nonce( 'mylisting_update_userdash' ) ) ?>">
			<button type="submit" class="btn btn-primary-alt btn-xs">Save settings</button>
		</div>
	</form>
</div>