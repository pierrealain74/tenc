<div class="element">
	<div class="pf-head round-icon">
		<div class="title-style-1">
			<i class="mi devices"></i>
			<h5><?php _ex( 'Top Platforms', 'User Dashboard', 'my-listing' ) ?></h5>
		</div>
	</div>
	<div class="pf-body">
		<?php if ( $platforms = $stats->get('visits.platforms') ): ?>
			<ul class="dash-table">
				<?php foreach ( $platforms as $platform ): ?>
					<?php printf(
						'<li data-os="%s"><strong>%s</strong> ('._x( '%s views', 'User Dashboard', 'my-listing' ).')</li>',
						esc_attr( $platform['name'] ),
						$platform['name'],
						number_format_i18n( $platform['count'] )
					) ?>
				<?php endforeach ?>
			</ul>
		<?php endif ?>
	</div>
</div>