<?php
/**
 * Template for rendering a basic search form widget.
 *
 * @since 1.0
 */
if ( ! defined('ABSPATH') ) {
	exit;
}
wp_print_styles('mylisting-basic-search-form');
?>

<div class="mylisting-basic-form text-center" :class="tabMode==='dark'?'featured-light':tabMode"
	data-listing-types="<?php echo esc_attr( wp_json_encode( $types_config ) ) ?>"
	data-config="<?php echo esc_attr( wp_json_encode( $config ) ) ?>"
	v-cloak>
	<div class="featured-search wide">
		<div class="fs-tabs">
			<?php if ( $config['types_display'] !== 'dropdown' ): ?>
				<ul class="nav nav-tabs cts-carousel" role="tablist">
					<li v-for="listingType, key in types" role="presentation" :class="activeType.id===listingType.id?'active':''">
						<a role="tab" @click="activeType=listingType">
							<div class="lt-icon" v-html="listingType.icon"></div>
							{{ listingType.name }}
						</a>
					</li>
					<li class="cts-prev">prev</li>
					<li class="cts-next">next</li>
				</ul>
			<?php endif ?>

			<div class="tab-content" :class="boxShadow?'add-box-shadow':''">
				<?php foreach ( $types as $key => $type ):
					$filters = $type->get_basic_filters(); ?>
					<div role="tabpanel" class="tab-pane fade in filter-count-<?php echo count($filters)+($config['types_display']==='dropdown'?2:1) ?>"
						:class="activeType.id===<?php echo $type->get_id() ?>?'active':''">

						<form method="GET">
							<?php if ( $config['types_display'] === 'dropdown' ): ?>
								<div class="form-group explore-filter md-group dropdown-filter listing-types-dropdown">
								    <select @select:change="typeDropdownChanged( $event.detail.value )" required="true" class="custom-select" ref="types-dropdown-<?php echo absint( $type->get_id() ) ?>">
								    	<option v-for="listingType, key in types" :value="key">{{ listingType.name }}</option>
								    </select>
								    <label><?php _ex( 'Listing Type', 'Basic Form > Listing types dropdown', 'my-listing' ) ?></label>
								</div>
							<?php endif ?>

							<?php foreach ( $filters as $filter ): ?>
								<?php mylisting_locate_template( sprintf( 'templates/explore/filters/%s.php', $filter->get_type() ), [
									'filter' => $filter,
									'location' => 'basic-form',
									'onchange' => 'filterChanged',
								] ) ?>
							<?php endforeach ?>

							<div class="form-group">
								<button class="buttons button-2 search" @click.prevent="submit">
									<i class="mi search"></i>
									<?php _e( 'Search', 'my-listing' ) ?>
								</button>
							</div>
						</form>
					</div>
				<?php endforeach ?>
			</div>
		</div>
	</div>
</div>
