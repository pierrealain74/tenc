<div class="element">
	<div class="pf-head round-icon">
		<div class="title-style-1">
			<i class="mi flag"></i>
			<h5><?php _ex( 'Top Countries', 'User Dashboard', 'my-listing' ) ?></h5>
		</div>
	</div>
	<div class="pf-body">
		<?php if ( $countries = $stats->get('visits.countries') ): ?>
			<ul class="dash-table">
				<?php foreach ( $countries as $country ): ?>
					<li><i class="mi flag"></i>
						<?php printf(
							'</span> <strong>%s</strong> ('._x( '%s views', 'User Dashboard', 'my-listing' ).')',
							$country['name'],
							number_format_i18n( $country['count'] )
						) ?>
					</li>
				<?php endforeach ?>
			</ul>
		<?php endif ?>
	</div>
</div>