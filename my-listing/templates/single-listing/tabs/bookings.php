<?php
/**
 * Template for `Bookings` tab in single listing page.
 *
 * @since 1.0
 */
if ( ! defined('ABSPATH') ) {
    exit;
}
?>

<div class="container">
    <div class="row">

        <?php // Contact Form Block.
        if ( $menu_item['provider'] === 'basic-form' ) {
            $contact_form_id = absint( $menu_item['contact_form_id'] );
            $email_to = array_filter( [$menu_item['field']] );
            $recipients = [];
            foreach ( $email_to as $email_field ) {
                if ( ( $email = $listing->get_field( $email_field ) ) && is_email( $email ) ) {
                    $recipients[] = $email;
                }
            }

            if ( $contact_form_id && count( $email_to ) && count( $recipients ) ) {

                // render the form
                $the_form = do_shortcode( sprintf( '[contact-form-7 id="%d"]', $contact_form_id ) );

                // set what fields the email will be sent to
                $the_form = str_replace( '%case27_recipients%', join( '|', $email_to ), $the_form );
                ?>
                <div class="col-md-6 col-md-push-3 col-sm-8 col-sm-push-2 col-xs-12 grid-item bookings-form-wrapper">
                    <div class="element content-block">
                        <div class="pf-body">
                            <?php echo $the_form ?>
                        </div>
                    </div>
                </div>
                <?php
            }
        }
        ?>

        <?php // TimeKit Widget.
        if ($menu_item['provider'] == 'timekit' && ( $timekitID = $listing->get_field( $menu_item['field'] ) ) ): ?>
            <div class="col-md-8 col-md-push-2 c27-timekit-wrapper">
                <iframe src="https://my.timekit.io/<?php echo esc_attr( $timekitID ) ?>" frameborder="0"></iframe>
            </div>
        <?php endif ?>

    </div>
</div>