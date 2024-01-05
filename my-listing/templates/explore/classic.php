<?php
/**
 * Template for displaying alternate Explore page with grid results (no-map).
 *
 * @var   $data
 * @var   $explore
 * @since 2.0
 */

$data['listing-wrap'] = 'col-md-6 col-sm-6 grid-item';
?>
<div id="c27-explore-listings" :class="['mobile-tab-'+state.mobileTab]" class="cts-explore hide-until-load explore-classic <?php echo $data['types_template'] === 'dropdown' ? 'explore-types-dropdown' : 'explore-types-topbar' ?> <?php echo esc_attr( $data['finder_columns'] ) ?>">
	<?php if ( $data['types_template'] === 'topbar' ): ?>
		<?php require locate_template( 'templates/explore/partials/topbar.php' ) ?>
	<?php endif ?>

	<?php require locate_template( 'templates/explore/partials/primary-filters.php' ) ?>

	<div class="finder-container fc-type-2">
		<div class="mobile-explore-head">
			<a type="button" class="toggle-mobile-search" data-toggle="collapse" data-target="#finderSearch">
				<i class="material-icons sort sm-icon"></i>
				<?php _e( 'Search Filters', 'my-listing' ) ?>
			</a>
		</div>

		<div class="finder-search collapse" id="finderSearch" :class="( state.mobileTab === 'filters' ? '' : 'visible-lg' )">

			<div class="finder-tabs-wrapper">
				<?php require locate_template( 'templates/explore/partials/sidebar.php' ) ?>
			</div>
		</div>
		<div class="finder-overlay"></div>
	</div>

	<section class="i-section explore-type-4" :class="( state.mobileTab === 'results' ? '' : 'visible-lg' )">
		<div class="container">
			<div class="explore-classic-sidebar col-md-4">
				<div class="element">
					<?php require locate_template( 'templates/explore/partials/sidebar.php' ) ?>
				</div>
			</div>
			<div class="explore-classic-content col-md-8">
			<div class="fl-head row">
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
					</div>
				</results-header>
			</div>
			<div class="row results-view grid fc-type-2-results" v-show="!loading && found_posts !== 0"></div>

			<?php require locate_template( 'templates/explore/partials/compare-bar.php' ) ?>

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
			<div class="row center-button pagination c27-explore-pagination" v-show="!loading"></div>
			</div>
		</div>
	</section>

	<?php require locate_template( 'templates/explore/partials/mobile-nav.php' ) ?>
</div>