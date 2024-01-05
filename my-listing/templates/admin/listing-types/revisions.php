<?php
/**
 * Template for displaying listing type revisions,
 * in listing type edit page in WP Admin.
 *
 * @since 2.0
 */

if ( ! defined('ABSPATH') ) {
	exit;
}

/**
 * Display actions on the current version of the listing type editor.
 */

$export_url = $this->_get_action_url( 'mylisting_revisions_export', $type->get_id(), 'current' );
$import_url = add_query_arg( [
	'action' => 'mylisting_revisions_import',
	'listing_type' => $type->get_id(),
], wp_nonce_url( admin_url( 'admin-ajax.php' ), 'mylisting_type_revisions' ) );

$import_message = _x( 'This will replace your existing listing type configuration. Do you want to proceed?', 'Listing type editor', 'my-listing' );
?>
<div class="current-actions">
	<a href="<?php echo esc_url( $export_url ) ?>" class="button button-primary"><?php _ex( 'Export config file', 'Listing type editor', 'my-listing' ) ?></a>
	<a href="<?php echo esc_url( $import_url ) ?>" class="button" id="mylisting_import_start"><?php _ex( 'Import config file', 'Listing type editor', 'my-listing' ) ?></a>
	<input type="file" id="mylisting_import_config" data-confirm="<?php echo esc_attr( $import_message ) ?>" style="display: none;">
</div>

<?php

/**
 * Display all revisions.
 */
printf( '<label>%s</label>', _x( 'Previous revisions', 'Listing type editor', 'my-listing' ) );

$count = 0;
foreach ( array_reverse( $revisions ) as $revision ) {
	if ( ! is_array( $revision ) || empty( $revision['time'] ) || empty( $revision['author'] ) ) {
		continue;
	}

	$count++;
	$rollback_url = $this->_get_action_url( 'mylisting_revisions_rollback', $type->get_id(), $revision['time'] );
	$export_url = $this->_get_action_url( 'mylisting_revisions_export', $type->get_id(), $revision['time'] );
	$remove_url = $this->_get_action_url( 'mylisting_revisions_remove', $type->get_id(), $revision['time'] );
	$author = get_userdata( $revision['author'] );
	$message = sprintf( _x( '%s ago', 'Listing type editor', 'my-listing' ), '<strong>'.human_time_diff( $revision['time'], current_time( 'timestamp' ) ).'</strong>' , current_time( 'timestamp' ) );
	if ( $author instanceof \WP_User ) {
		$message .= sprintf( ' '. _x( 'by %s.', 'Listing type editor', 'my-listing' ), '<strong>'.$author->display_name.'</strong>' );
	} ?>
	<div class="revision">
		<div class="message"><?php echo $message ?></div>
		<div class="actions">
			<a href="<?php echo esc_url( $rollback_url ) ?>" class="button button-small" onclick="return confirm('<?php echo esc_attr( _x( 'Are you sure?', 'Listing type editor', 'my-listing' ) ) ?>');">
				<?php _ex( 'Rollback', 'Listing type editor', 'my-listing' ) ?>
			</a> &middot;
			<a href="<?php echo esc_url( $export_url ) ?>" class="button-small"><?php _ex( 'Export', 'Listing type editor', 'my-listing' ) ?></a> &middot;
			<a href="<?php echo esc_url( $remove_url ) ?>" class="button-small" onclick="return confirm('<?php echo esc_attr( _x( 'Are you sure you want to remove this revision?', 'Listing type editor', 'my-listing' ) ) ?>');">
				<?php _ex( 'Remove', 'Listing type editor', 'my-listing' ) ?>
			</a>
		</div>
	</div>
	<?php
}

/**
 * Display message at the end of the revision metabox.
 */
if ( $count === 0 ) {
	printf( '<p><em>%s</em></p>', _x( 'No revisions stored yet.', 'Listing type editor', 'my-listing' ) );
} else {
	printf(
		'<p><em>'.
			_x( 'The previous %s revisions of the listing type settings are stored automatically.', 'Listing type editor', 'my-listing' ).
		'</em></p>',
		number_format_i18n( $this->max_revisioun_count )
	);
}
