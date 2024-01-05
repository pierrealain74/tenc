<?php
/**
 * Add the primary mobile filter for each listing type.
 *
 * @since 2.4
 */
if ( ! defined('ABSPATH') ) {
	exit;
} ?>

<?php foreach ( $explore->types as $type ): ?>
	<div class="mobile-explore-head-top" v-if="activeType.id===<?php echo $type->get_id() ?> && state.mobileTab!=='filters'">
		<div v-if="currentTax" class="primary-category">
			<span class="cat-icon" :style="'background-color:'+(currentTax.activeTerm?currentTax.activeTerm.color:'#fff')"
				v-html="currentTax.activeTerm.single_icon"></span>
			<div v-html="currentTermName"></div>
		</div>
		<template v-else>
			<?php if ( $filter = $type->get_primary_filter() ): ?>
				<?php mylisting_locate_template( sprintf( 'templates/explore/filters/%s.php', $filter->get_type() ), [
					'filter' => $filter,
					'model' => sprintf( 'types["%s"].filters.search_keywords', $type->get_slug() ),
					'location' => 'primary-filter',
					'onchange' => sprintf( 'getListings( \'primary-filter:%s\', true )', $filter->get_type() ),
				] ) ?>
			<?php endif ?>
		</template>

		<div class="explore-head-top-filters">
			<a v-if="template==='explore-no-map' && !isMobile" href="#"
				@click.prevent="this.jQuery('.fc-type-2').toggleClass('fc-type-2-open')">
				<?php _ex( 'Filters', 'Explore page', 'my-listing' ) ?>
				<i class="icon-settings-1"></i>
			</a>
			<a v-else href="#" @click.prevent="state.mobileTab = 'filters'">
				<?php _ex( 'Filters', 'Explore page', 'my-listing' ) ?>
				<i class="icon-settings-1"></i>
			</a>
		</div>
	</div>
<?php endforeach ?>