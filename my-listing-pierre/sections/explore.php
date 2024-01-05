<?php

if ( ! defined('ABSPATH') ) {
	exit;
}

/**
 * Explore page options.
 */
$data = c27()->merge_options([
	'title'    		 => '',
	'subtitle'       => '',
	'template' 		 => 'explore-default',
    'categories'     => [ 'count' => 10, ],
    'is_edit_mode'   => false,
    'scroll_to_results' => false,
    'disable_live_url_update' => false,
    'drag_search' => true,
    'default_values' => '',
	'listing-wrap'   => '',
    'listing_types'  => [],
    'types_template' => 'topbar',
	'finder_columns' => 'finder-one-columns',
	'categories_overlay' => [
		'type' => 'gradient',
		'gradient' => 'gradient1',
		'solid_color' => 'rgba(0, 0, 0, .1)',
	],
	'map' => [
		'default_lat' => 51.492,
		'default_lng' => -0.130,
		'default_zoom' => 11,
		'min_zoom' => 2,
		'max_zoom' => 18,
		'skin' => 'skin1',
    	'scrollwheel' => false,
	],
	'display_ad' 	=> false,
	'ad_pub_id'		=> '',
	'ad_slot_id'	=> '',
	'ad_interval'	=> '',
], $data);

$GLOBALS['c27-explore'] = new MyListing\Src\Explore( $data );
$explore = &$GLOBALS['c27-explore'];

if ( ! in_array( $data['types_template'], ['topbar', 'dropdown'] ) ) {
	$data['types_template'] = 'topbar';
}

/*
 * The maximum number of columns for explore-2 template is "two". So, if the user sets
 * the option to "three" in Elementor settings, convert it to "two" columns.
 */
if ( $data['template'] == 'explore-2' && $data['finder_columns'] == 'finder-three-columns' ) {
	$data['finder_columns'] = 'finder-two-columns';
}

/**
 * If a query string for default filter values has been passed, use it.
 *
 * @since 2.2
 */
if ( ! empty( $data['default_values'] ) && ( $query_string = parse_url( $data['default_values'], PHP_URL_QUERY ) ) ) {
	$query_args = wp_parse_args( $query_string );
	if ( $query_args ) {
		foreach ( $query_args as $key => $value ) {
			if ( ! isset( $_GET[ $key ] ) ) {
				$_GET[ $key ] = $value;
			}
		}
	}
}
?>

<?php if (!$data['template'] || $data['template'] == 'explore-1' || $data['template'] == 'explore-2'): ?>
	<?php require locate_template( 'templates/explore/regular.php' ) ?>
<?php endif ?>

<?php if ($data['template'] == 'explore-no-map'): ?>
	<?php require locate_template( 'templates/explore/alternate.php' ) ?>
<?php endif ?>

<?php if ($data['template'] == 'explore-custom'): ?>
	<?php require locate_template( 'templates/explore/custom.php' ) ?>
<?php endif ?>

<?php if ($data['template'] === 'explore-classic'): ?>
	<?php require locate_template( 'templates/explore/classic.php' ) ?>
<?php endif ?>
<script type="text/javascript">
	var CASE27_Explore_Settings = {
		ListingWrap: <?php echo json_encode( $data['listing-wrap'] ) ?>,
		ActiveMobileTab: <?php echo json_encode( $explore->get_active_mobile_tab() ) ?>,
		ScrollToResults: <?php echo json_encode( $data['scroll_to_results'] ) ?>,
		Map: <?php echo wp_json_encode( $data['map'] ) ?>,
		IsFirstLoad: true,
		DisableLiveUrlUpdate: <?php echo json_encode( $data['disable_live_url_update'] ) ?>,
		DragSearchEnabled: <?php echo json_encode( $data['drag_search'] ) ?>,
		DragSearchLabel: <?php echo wp_json_encode( _x( 'Visible map area', 'map drag search label', 'my-listing' ) ) ?>,
		TermSettings: <?php echo wp_json_encode( $data['categories'] ) ?>,
		ListingTypes: <?php echo wp_json_encode( $explore->get_types_config() ) ?>,
		ExplorePage: <?php echo wp_json_encode( $explore::$explore_page && is_page( $explore::$explore_page->ID ) ? get_permalink( $explore::$explore_page ) : null ) ?>,
		ActiveListingType: <?php echo wp_json_encode( $explore->active_listing_type ? $explore->active_listing_type->get_slug() : null ) ?>,
		TermCache: {},
		Cache: {},
		ScrollPosition: <?php echo ! empty( $_GET['sp'] ) ? absint( $_GET['sp'] ) : 0 ?>,
		Template: <?php echo wp_json_encode( $data['template'] ) ?>,
		DisplayAd: <?php echo json_encode( $data['display_ad'] ) ?>,
		AdPublisherID: <?php echo json_encode( $data['ad_pub_id'] ) ?>,
		AdSlotID: <?php echo json_encode( $data['ad_slot_id'] ) ?>,
		AdInterval: <?php echo json_encode( $data['ad_interval'] ) ?>
	};
</script>

<?php if ( $data['display_ad'] ): ?>
	<?php \MyListing\print_script_tag( $data['ad_pub_id'] ) ?>
<?php endif ?>

<?php if ($data['is_edit_mode']): ?>
    <script type="text/javascript">case27_ready_script(jQuery); MyListing.Explore_Init(); MyListing.Maps.init();</script>
<?php endif ?>
