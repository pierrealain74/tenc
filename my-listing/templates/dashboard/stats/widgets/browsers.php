<div class="element">
	<div class="pf-head round-icon">
		<div class="title-style-1">
			<i class="mi explore"></i>
			<h5><?php _ex( 'Top Browsers', 'User Dashboard', 'my-listing' ) ?></h5>
		</div>
	</div>
	<div class="pf-body">
		<?php if ( $browsers = $stats->get('visits.browsers') ): ?>
			<ul class="dash-table">
				<?php foreach ( $browsers as $browser ): ?>
					<li data-browser="<?php echo esc_attr( $browser['name'] ) ?>">
						<?php printf( '<strong>%s</strong> ('._x( '%s views', 'User Dashboard', 'my-listing' ).')', $browser['name'], number_format_i18n( $browser['count'] ) ) ?>
					</li>
				<?php endforeach ?>
			</ul>
		<?php endif ?>
	</div>
</div>