<div class="element">
	<div class="pf-head round-icon">
		<div class="title-style-1">
			<i class="mi explore"></i>
			<h5><?php _ex( 'Button clicks', 'User Dashboard', 'my-listing' ) ?></h5>
		</div>
	</div>
	<div class="pf-body">
		<?php
		if ( $tracks = $stats->get('tracks') ) : ?>
			<ul class="dash-table">
				<?php foreach ( $tracks as $track_group ) : ?>
					<li><strong><?= esc_html( $track_group['label'] ) ?></strong></li>
					<?php foreach ( $track_group['tracks'] as $track ) : ?>
						<li>
							<i class="fa fa-mouse-pointer"></i>
							<?php printf( '<strong>%s</strong> ('. _nx( '%s click', '%s clicks', number_format_i18n( $track['count'] ), 'User Dashboard', 'my-listing' ) .')', $track['name'], number_format_i18n( $track['count'] ) ); ?>
						</li>
					<?php endforeach ?>
				<?php endforeach ?>
			</ul>
		<?php else: ?>
			<em><?= _x( 'No click stats recorded yet.', 'stats', 'my-listing' ) ?></em>
		<?php endif ?>
	</div>
</div>