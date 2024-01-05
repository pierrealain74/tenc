<div class="element referrers-panel">
	<div class="pf-head round-icon">
		<div class="title-style-1">
			<i class="mi insert_link"></i>
			<h5><?php _ex( 'Top Referrers', 'User Dashboard', 'my-listing' ) ?></h5>
			<button class="collapse-referrers-button" data-toggle="collapse" data-target="#collapse-referrers">
				<i class="mi keyboard_arrow_down"></i>
			</button>
		</div>
	</div>
	<div class="collapse <?php echo apply_filters( 'mylisting/dashboard/referrers:default-state', 'collapsed' ) === 'expanded' ? 'in' : '' ?>" id="collapse-referrers">
		<div class="pf-body">

			<?php if ( $referrers = $stats->get('visits.referrers') ): ?>
				<ul class="dash-table">
					<?php foreach ( $referrers as $ref ): ?>
						<li>
							<div class="dash-table-group">
								<?php printf( '<strong>%s</strong> ('._x( '%s views', 'User Dashboard', 'my-listing' ).')', $ref['domain'], number_format_i18n( $ref['count'] ) ) ?>
							</div>
							<ul>
								<?php foreach ( $ref['subrefs'] as $subref ): ?>
									<li><?php printf( '&mdash; %s ('._x( '%s views', 'User Dashboard', 'my-listing' ).')', $subref['url'], number_format_i18n( $subref['count'] ) ) ?></li>
								<?php endforeach ?>
							</ul>
						</li>
					<?php endforeach ?>
				</ul>
			<?php endif ?>
		</div>
	</div>
</div>