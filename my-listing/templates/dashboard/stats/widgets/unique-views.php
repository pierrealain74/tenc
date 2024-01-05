<div class="element">
	<div class="pf-head round-icon">
		<div class="title-style-1">
			<i class="mi remove_red_eye"></i>
			<h5><?php _ex( 'Unique views', 'User Dashboard', 'my-listing' ) ?></h5>
		</div>
	</div>
	<div class="pf-body">
		<div class="number-stats">
			<?php if ( $views = $stats->get('visits.unique_views') ): ?>
				<p>
					<span class="animated-bars"></span>
					<span><?php echo number_format_i18n( $views['lastday'] ) ?></span>
					<?php _ex( 'Last 24 hours', 'User Dashboard', 'my-listing' ) ?>
				</p>
				<p>
					<span class="animated-bars"></span>
					<span><?php echo number_format_i18n( $views['lastweek'] ) ?></span>
					<?php _ex( 'Last 7 days', 'User Dashboard', 'my-listing' ) ?>
				</p>
				<p>
					<span class="animated-bars"></span>
					<span><?php echo number_format_i18n( $views['lastmonth'] ) ?></span>
					<?php _ex( 'Last 30 days', 'User Dashboard', 'my-listing' ) ?>
				</p>
			<?php endif ?>
		</div>
	</div>
</div>
