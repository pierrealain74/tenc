<?php
/**
 * Display the "Filter by Listing Status" dropdown.
 *
 * @since 1.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>
<div class="col-md-3 sort-my-listings">
	<select class="custom-select filter-listings-select" required="required">
		<option value="<?php echo esc_url( $endpoint ) ?>" <?php selected( $active_status === 'all' ) ?>>
			<?php _ex( 'All Listings', 'User dashboard', 'my-listing' ) ?>
		</option>

		<optgroup>
			<option value="<?php echo esc_url( add_query_arg( 'status', 'publish', $endpoint ) ) ?>" <?php selected( $active_status === 'publish' ) ?>>
				<?php _ex( 'Published', 'User dashboard', 'my-listing' ) ?>
			</option>

			<option value="<?php echo esc_url( add_query_arg( 'status', 'pending', $endpoint ) ) ?>" <?php selected( $active_status === 'pending' ) ?>>
				<?php _ex( 'Pending Approval', 'User dashboard', 'my-listing' ) ?>
			</option>

			<option value="<?php echo esc_url( add_query_arg( 'status', 'expired', $endpoint ) ) ?>" <?php selected( $active_status === 'expired' ) ?>>
				<?php _ex( 'Expired', 'User dashboard', 'my-listing' ) ?>
			</option>

			<option value="<?php echo esc_url( add_query_arg( 'status', 'pending_payment', $endpoint ) ) ?>" <?php selected( $active_status === 'pending_payment' ) ?>>
				<?php _ex( 'Pending Payment', 'User dashboard', 'my-listing' ) ?>
			</option>

			<option value="<?php echo esc_url( add_query_arg( 'status', 'preview', $endpoint ) ) ?>" <?php selected( $active_status === 'preview' ) ?>>
				<?php _ex( 'Preview', 'User dashboard', 'my-listing' ) ?>
			</option>
		</optgroup>

		<?php if ( mylisting_get_setting( 'claims_enabled' ) ):
			$claims = get_posts( [
				'post_type' => 'claim',
				'post_status' => 'publish',
				'posts_per_page' => 1,
				'meta_key' => '_user_id',
				'meta_value' => get_current_user_id(),
				'fields' => 'ids',
			] ); ?>
			<?php if ( ! empty( $claims ) ): ?>
				<optgroup>
					<option value="<?php echo esc_url( wc_get_account_endpoint_url( _x( 'claim-requests', 'Claims user dashboard page slug', 'my-listing' ) ) ) ?>">
						<?php _ex( 'Claim requests', 'User dashboard', 'my-listing' ) ?>
					</option>
				</optgroup>
			<?php endif ?>
		<?php endif ?>
	</select>
</div>
