<?php
$extension = ! empty( $extension ) ? $extension : substr( strrchr( $value, '.' ), 1 );
$is_image = in_array( $extension, [ 'jpg', 'gif', 'png', 'jpeg', 'jpe', 'webp' ] );
?>

<div class="uploaded-file <?php echo $is_image ? 'uploaded-image' : '' ?> review-gallery-image job-manager-uploaded-file">
	<span class="uploaded-file-preview">
		<?php if ( $is_image ): ?>
			<span class="job-manager-uploaded-file-preview">
				<img src="<?php echo esc_url( $value ? c27()->get_resized_image( $value, 'medium' ) : '' ) ?>">
			</span>
		<?php else: ?>
			<span class="job-manager-uploaded-file-name">
				<i class="mi insert_drive_file uploaded-file-icon"></i>
				<code><?php echo esc_html( basename( $value ) ) ?></code>
			</span>
		<?php endif ?>

		<a class="remove-uploaded-file review-gallery-image-remove job-manager-remove-uploaded-file"><i class="mi delete"></i></a>
	</span>
	<input type="hidden" class="input-text" name="<?php echo esc_attr( $name ) ?>" value="<?php echo esc_attr( 'b64:'.base64_encode( $value ) ) ?>">
</div>
