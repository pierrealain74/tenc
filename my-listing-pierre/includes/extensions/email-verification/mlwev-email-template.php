<?php
defined( 'ABSPATH' ) || exit;
?>
<table class="form-table">
    <tr class="tr_email_subject mlwev-tr-border-bottom">
        <th><?php echo __( 'Subject', 'my-listing' ); ?></th>
        <td>
            <input name="mlwev_email_subject" type="text" class="mlwev-input-text" placeholder="Enter Email Subject"
                   value="<?php echo $tab_options['mlwev_email_subject']; ?>" required>
        </td>
    </tr>
    <tr class="tr_email_heading mlwev-tr-border-bottom">
        <th><?php echo __( 'Email Heading', 'my-listing' ); ?></th>
        <td>
            <input name="mlwev_email_heading" type="text" class="mlwev-input-text" placeholder="Enter Email Heading"
                   value="<?php echo $tab_options['mlwev_email_heading']; ?>" required>
        </td>
    </tr>
    <tr class="tr_email_body_textarea mlwev-tr-border-bottom">
        <th><?php echo __( 'Email Body', 'my-listing' ); ?></th>
        <td>
			<textarea name="mlwev_email_body" class="mlwev-input-textarea" placeholder="Enter Email Content" rows="10"
                      required><?php echo $tab_options['mlwev_email_body']; ?></textarea>
        </td>
    </tr>
    <tr class="mlwev-tr-border-bottom">
        <th></th>
        <td>
            <p class="description"><?php echo __( 'You can use following merge tags in email body.', 'my-listing' ); ?></p>
            {{mlwev_user_login}} = <?php echo __( 'User Login Name for login', 'my-listing' ); ?><br>
            {{mlwev_display_name}} = <?php echo __( 'User Display Name', 'my-listing' ); ?><br>
            {{mlwev_user_email}} = <?php echo __( 'User Email', 'my-listing' ); ?><br>
            {{mlwev_user_verification_link}} = <?php echo __( 'Email Verification Link', 'my-listing' ); ?><br>
            {{wcemailverificationcode}} = <?php echo __( 'Email Verification Link', 'my-listing' ); ?><br>
            {{sitename}} = <?php echo __( 'Your Website Name', 'my-listing' ); ?><br>
            {{sitename_with_link}} = <?php echo __( 'Your Website Name with link', 'my-listing' ); ?><br>
            {{mlwev_verification_link_text}} = <?php echo __( 'Shows the verification link text', 'my-listing' ); ?><br>
            <br><br>
        </td>
    </tr>
</table>
