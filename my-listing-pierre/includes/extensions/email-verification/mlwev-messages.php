<?php
defined( 'ABSPATH' ) || exit;
?>
<table class="form-table">
    <tr class="mlwev-tr-border-bottom">
        <th><?php echo __( 'Verification Notice', 'my-listing' ); ?></th>
        <td>
            <textarea name="mlwev_email_registration_message" class="mlwev-input-textarea" required rows="3"><?php echo $tab_options['mlwev_email_registration_message']; ?></textarea>
        </td>
    </tr>
    <tr class="mlwev-tr-border-bottom">
        <th><?php echo __( 'Verification Success Notice', 'my-listing' ); ?></th>
        <td>
            <textarea name="mlwev_email_success_message" class="mlwev-input-textarea" required rows="3"><?php echo $tab_options['mlwev_email_success_message']; ?></textarea>
        </td>
    </tr>
    <tr class="mlwev-tr-border-bottom">
        <th><?php echo __( 'Re-Verification Notice', 'my-listing' ); ?></th>
        <td>
            <textarea name="mlwev_email_new_verification_link" class="mlwev-input-textarea" required rows="3"><?php echo $tab_options['mlwev_email_new_verification_link']; ?></textarea>
        </td>
    </tr>
    <tr class="mlwev-tr-border-bottom">
        <th><?php echo __( 'Notice For Verified Users', 'my-listing' ); ?></th>
        <td>
            <textarea name="mlwev_email_verification_already_done" class="mlwev-input-textarea" required rows="3"><?php echo $tab_options['mlwev_email_verification_already_done']; ?></textarea>
        </td>
    </tr>
    <tr class="mlwev-tr-border-bottom">
        <th><?php echo __( 'Resend Confirmation Link Notice', 'my-listing' ); ?></th>
        <td>
            <textarea name="mlwev_email_resend_confirmation" class="mlwev-input-textarea" required rows="3"><?php echo $tab_options['mlwev_email_resend_confirmation']; ?></textarea>
        </td>
    </tr>
    <tr class="mlwev-tr-border-bottom">
        <th><?php echo __( 'Email Verification Link Notice', 'my-listing' ); ?></th>
        <td>
            <textarea name="mlwev_email_new_verification_link_text" class="mlwev-input-textarea" required rows="3"><?php echo $tab_options['mlwev_email_new_verification_link_text']; ?></textarea>
        </td>
    </tr>
    <tr class="mlwev-tr-border-bottom">
        <th></th>
        <td>
            <p class="description"><?php echo __( 'You can use following merge tags in any of the above messages.', 'my-listing' ); ?></p>
            {{mlwev_resend_link}} = <?php echo __( 'Generates Resend Confirmation Email Link', 'my-listing' ); ?><br>
            {{mlwev_site_login_link}} = <?php echo __( 'Show the WooCommerce Myaccount Page', 'my-listing' ); ?><br>
        </td>
    </tr>
</table>
