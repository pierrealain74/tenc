<?php
/**
 * Template for rendering a `contact_form` block in single listing page.
 *
 * @since 1.0
 */
if ( ! defined('ABSPATH') ) {
	exit;
}

$contact_form_id = $block->get_prop('contact_form_id');
$email_to = (array) $block->get_prop('email_to');
$recipients = [];

// check if there are any recipient emails available
foreach ( $email_to as $email_field ) {
    if ( ( $email = $listing->get_field( $email_field ) ) && is_email( $email ) ) {
        $recipients[] = $email;
    }
}

// if no recipients or no cf7 id, then don't display the block
if ( ! $contact_form_id || empty( $recipients ) ) {
	return;
}

// render the form
$the_form = do_shortcode( sprintf( '[contact-form-7 id="%d"]', $contact_form_id ) );

// set what fields the email will be sent to
$the_form = str_replace( '%case27_recipients%', join( '|', $email_to ), $the_form );
?>

<div class="<?php echo esc_attr( $block->get_wrapper_classes() ) ?>" id="<?php echo esc_attr( $block->get_wrapper_id() ) ?>">
	<div class="element content-block">
		<div class="pf-head">
			<div class="title-style-1">
				<i class="<?php echo esc_attr( $block->get_icon() ) ?>"></i>
				<h5><?php echo esc_html( $block->get_title() ) ?></h5>
			</div>
		</div>
		<div class="pf-body">
			<?php echo $the_form ?>
		</div>
	</div>
</div>