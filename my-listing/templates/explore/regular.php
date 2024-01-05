<?php
/**
 * Template for displaying regular Explore page with map.
 *
 * @since 1.0
 */
if ( ! defined('ABSPATH') ) {
	exit;
}
$data['listing-wrap'] = 'col-md-12 grid-item';
?>
<div
	:class="['mobile-tab-'+state.mobileTab,mapExpanded?'map-expanded':'',loading?'loading-new-results':'']"
	class="cts-explore finder-container fc-type-1 <?php echo esc_attr( $data['finder_columns'] ) ?> <?php echo $data['types_template'] === 'dropdown' ? 'explore-types-dropdown' : 'explore-types-topbar' ?>"
	id="c27-explore-listings"
	:style="containerStyles"
>

	<?php if ( $data['types_template'] === 'topbar' ): ?>
		<?php require locate_template( 'templates/explore/partials/topbar.php' ) ?>
	<?php endif ?>

	<?php require locate_template( 'templates/explore/partials/primary-filters.php' ) ?>

	<div class="<?php echo $data['template'] === 'explore-2' ? 'fc-one-column min-scroll' : 'fc-default' ?>">
		<div class="finder-search min-scroll" id="finderSearch" :class="( state.mobileTab === 'filters' ? '' : 'visible-lg' )">
			<div class="finder-tabs-wrapper">
				<?php require locate_template( 'templates/explore/partials/sidebar.php' ) ?>
			</div>
		</div>

		<div class="finder-listings min-scroll" id="finderListings" :class="( state.mobileTab === 'results' ? '' : 'visible-lg' )" :style="loading?'overflow:hidden':''">
			<div class="fl-head">
				<results-header inline-template>
					<div class="explore-desktop-head" v-if="foundPosts !== 0">
						<div class="load-previews-batch load-batch-icon" :class="! hasPrevPage ? 'batch-unavailable' : ''">
							<a href="#" @click.prevent="getPrevPage">
								<i class="material-icons arrow_back"></i>
							</a>
						</div>

						<span href="#" class="fl-results-no text-left" v-cloak>
							<span class="rslt-nr" v-html="resultCountText"></span>
						</span>

						<div class="load-next-batch load-batch-icon" :class="{ 'batch-unavailable': ! hasNextPage }">
							<a href="#" @click.prevent="getNextPage">
								<i class="material-icons arrow_forward"></i>
							</a>
						</div>

						<?php if ( $data['template'] === 'explore-1' ): ?>
							<a href="#" class="expand-map-btn" v-if="!$root.isMobile && !$root.mapExpanded"
								@click.prevent="$root.toggleMap(true)">
								<i class="mi map"></i>
								<span><?php _ex( 'Map view', 'Explore page', 'my-listing' ) ?></span>
							</a>
						<?php endif ?>
					</div>
				</results-header>
			</div>
			<div class="results-view grid" v-show="!loading && found_posts !== 0"></div>

			<div class="no-results-wrapper" v-show="!loading && found_posts === 0">
				<i class="no-results-icon mi mood_bad"></i>
				<li class="no_job_listings_found">
					<?php _e( 'There are no listings matching your search.', 'my-listing' ) ?>
					<a href="#" class="reset-results-27 full-width" @click.prevent="resetFilters($event); getListings('reset', true);">
						<i class="mi refresh"></i>
						<?php _e( 'Reset Filters', 'my-listing' ) ?>
					</a>
				</li>
			</div>

			<div class="loader-bg" v-show="loading">
				<?php c27()->get_partial( 'spinner', [
					'color' => '#777',
					'classes' => 'center-vh',
					'size' => 28,
					'width' => 3,
				] ) ?>
			</div>
			<div class="col-md-12 center-button pagination c27-explore-pagination" v-show="!loading"></div>
		</div>
	</div>

	<?php require locate_template( 'templates/explore/partials/compare-bar.php' ) ?>

	<div class="finder-map" id="finderMap" :class="{'map-mobile-visible':state.mobileTab==='map'}">
		<div
			class="map c27-map mylisting-map-loading"
			id="<?php echo esc_attr( 'map__' . uniqid() ) ?>"
			data-options="<?php echo c27()->encode_attr( [
				'skin' => $data['map']['skin'],
				'scrollwheel' => $data['map']['scrollwheel'],
				'zoom' => 10,
				'minZoom' => $data['map']['min_zoom'],
				'maxZoom' => $data['map']['max_zoom'],
			] ) ?>"
		>
		</div>
		<?php require locate_template( 'templates/explore/partials/drag-search-toggle.php' ) ?>
		<?php if ( $data['template'] === 'explore-1' ): ?>
			<a href="#" class="collapse-map-btn" v-if="!isMobile && mapExpanded"
				@click.prevent="$root.toggleMap(false)">
				<i class="mi view_agenda"></i>
				<span><?php _ex( 'List view', 'Explore page', 'my-listing' ) ?></span>
			</a>
		<?php endif ?>
	</div>
	<div style="display: none;">
		<div id="explore-map-location-ctrl" title="<?php echo esc_attr( _x( 'Click to show your location', 'Explore page', 'my-listing' ) ) ?>">
			<i class="mi my_location"></i>
		</div>
	</div>

	<?php require locate_template( 'templates/explore/partials/mobile-nav.php' ) ?>
</div>