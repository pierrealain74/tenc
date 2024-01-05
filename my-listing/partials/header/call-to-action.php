<?php

$label = c27()->get_setting( 'header_call_to_action_label' );
$links_to = c27()->get_setting( 'header_call_to_action_links_to' );

if ( is_user_logged_in() && ! \MyListing\Src\User_Roles\user_can_add_listings() ) {
	return;
}

if ( ! ( $data['show_call_to_action'] && $label && $links_to ) ) {
	return;
}

?>
<div class="header-button">
	<a href="<?php echo esc_url( $links_to ) ?>" class="buttons button-<?php echo $data['skin'] == 'light' ? '2' : '1' ?>">
		<?php echo do_shortcode( $label ) ?>
	</a>
</div>