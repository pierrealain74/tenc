<select
	name="listing"
	class="custom-select dashboard-filter-stats"
	data-mylisting-ajax="true"
	data-mylisting-ajax-url="mylisting_list_posts"
	data-mylisting-ajax-params="<?php echo c27()->encode_attr( [ 'cts_author' => get_current_user_id() ] ) ?>"
	placeholder="<?php echo esc_attr( _x( 'Filter by listing', 'User dashboard', 'my-listing' ) ) ?>"
	data-url="<?php echo esc_url( wc_get_account_endpoint_url( 'dashboard' ) ) ?>"
>
<?php if ( isset( $listing) && $listing instanceof \MyListing\Src\Listing ): ?>
	<option value="<?php echo esc_attr( $listing->get_id() ) ?>" selected="selected"><?php echo esc_attr( $listing->get_name() ) ?></option>
<?php endif ?>
</select>