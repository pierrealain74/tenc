<?php
/**
 * Template for rendering data-updater settings.
 *
 * @since 2.2.3
 * @var   array $updates
 */
if ( ! defined('ABSPATH') ) {
	exit;
}

$tab_url = admin_url( 'admin.php?page=mylisting-options&active_tab=data-updater' );
$update_endpoint = wp_nonce_url( admin_url( 'admin-post.php?action=mylisting_run_updater' ), 'mylisting_run_updater' );
?>

<div class="cts-data-updater">
	<?php if ( ! empty( $messages ) ): ?>
		<?php foreach ( (array) $messages as $handler_key => $message ):
			if ( ! isset( $updates[ $handler_key ] ) ) {
				continue;
			} ?>
			<div class="form-group mb20">
				<h3 class="m-heading mb10"><?php echo $updates[ $handler_key ]['label'] ?></h3>
				<p class="mt0 mb0"><?php echo $message ?></p>
			</div>
		<?php endforeach ?>
		<a href="<?php echo esc_url( $tab_url ) ?>" class="btn btn-secondary btn-xxs">Done</a>
	<?php else: ?>
		<?php foreach ( $updates as $handler_key => $handler ): ?>
			<div class="form-group mb30 update-handler handler-<?php echo esc_attr( $handler_key ) ?>">
				<div class="update-status">
					<i class="mi <?php echo $handler['completed'] ? 'check_circle' : 'info_outline' ?>"></i>
				</div>
				<h4 class="m-heading mb5"><?php echo $handler['label'] ?></h4>
				<p class="mt0 mb10"><?php echo $handler['description'] ?></p>
				<a
					href="<?php echo esc_url( add_query_arg( 'run', $handler_key, $update_endpoint ) ) ?>"
					class="btn btn-xxs btn-run-update <?php echo $handler['completed'] ? 'btn-secondary' : 'btn-primary-alt' ?>"
				><?php echo $handler['completed'] ? 'Run again' : 'Run' ?></a>
			</div>
		<?php endforeach ?>

		<div class="update-in-progress text-center hide mt60">
			<?php c27()->get_partial( 'spinner', [
				'color' => '#1e88e5',
				'size' => 22,
				'width' => 3,
			] ) ?>
			<p class="mt10 mb0"><strong>Update in progress</strong></p>
			<p class="mt0">Do not close this window</p>
		</div>
	<?php endif ?>
</div>
