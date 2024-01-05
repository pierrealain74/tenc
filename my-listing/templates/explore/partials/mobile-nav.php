<div class="explore-mobile-nav">
	<ul class="nav nav-tabs">
		<li class="show-results" :class="state.mobileTab === 'results' ? 'active' : ''">
			<a href="#" @click.prevent="state.mobileTab = 'results';">
				<i class="mi view_agenda"></i>
				<span><?php _ex( 'List view', 'Explore page', 'my-listing' ) ?></span>
			</a>
		</li>

		<?php if ($data['template'] !== 'explore-no-map'): ?>
			<li class="show-map" :class="state.mobileTab === 'map' ? 'active' : ''" v-if="map">
				<a href="#" @click.prevent="state.mobileTab = 'map'; this.window.scrollTo(0,0);">
					<i class="fa fa-map-marked-alt"></i>
					<span><?php _ex( 'Map view', 'Explore page', 'my-listing' ) ?></span>
				</a>
			</li>
		<?php endif ?>
	</ul>
</div>