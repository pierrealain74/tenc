<?php
/**
 * Single listing quick actions template.
 *
 * @since 2.0
 */

if ( empty( $layout['quick_actions'] ) ) {
	$layout['quick_actions'] = [];
	$actions = require locate_template( 'includes/src/listing-types/quick-actions/quick-actions.php' );
	$defaults = ['get-directions', 'call-now', 'direct-message', 'send-email', 'visit-website', 'leave-review', 'claim-listing', 'bookmark', 'share', 'report-listing'];
	foreach ( $defaults as $default_action ) {
		if ( empty( $actions[ $default_action ] ) ) {
			continue;
		}

		$layout['quick_actions'][] = $actions[ $default_action ];
	}
}
?>

<div class="container qla-container">
	<div class="quick-listing-actions">
		<ul class="cts-carousel">
			<?php foreach ( $layout['quick_actions'] as $action ):
				if ( empty( $action['id'] ) ) {
					$action['id'] = sprintf( 'qa-%s', substr( md5( json_encode( $action ) ), 0, 6 ) );
				}

				$action['original_label'] = $action['label'];
				$action['label'] = do_shortcode( $listing->compile_string( $action['label'] ) );

				// active/checked label, e.g. for bookmark action, it'd be 'Bookmarked'.
				if ( ! empty( $action['active_label'] ) ) {
					$action['original_active_label'] = $action['active_label'];
					$action['active_label'] = do_shortcode( $listing->compile_string( $action['active_label'] ) );
				}

				if ( ! empty( $action['track_custom_btn'] ) ) {
				    $action['class'] .= ' ml-track-btn';
				}
				
				$template = sprintf( 'templates/single-listing/quick-actions/%s.php', $action['action'] ); ?>
				<?php if ( locate_template( $template ) ): ?>
					<?php require locate_template( $template ) ?>
				<?php elseif ( has_action( sprintf( 'mylisting/single/quick-actions/%s', $action['action'] ) ) ): ?>
					<?php do_action( sprintf( 'mylisting/single/quick-actions/%s', $action['action'] ), $action, $listing ) ?>
				<?php else: ?>
					<?php // dump($action) ?>
				<?php endif ?>
			<?php endforeach ?>

            <li class="cts-prev">prev</li>
            <li class="cts-next">next</li>
		</ul>
	</div>
</div>