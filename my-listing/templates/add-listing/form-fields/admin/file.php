<?php
/**
 * Shows the `file` form field on listing forms.
 *
 * @since 2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$is_multiple = ! empty( $field['multiple'] );
$allowed_mime_types = array_keys( ! empty( $field['allowed_mime_types'] ) ? $field['allowed_mime_types'] : get_allowed_mime_types() );
$field_name = isset( $field['name'] ) ? $field['name'] : $key;
$field_name .= $is_multiple ? '[]' : '';
$uploaded_files = ! empty( $field['value'] ) ? (array) $field['value'] : [];
?>

<div class="file-upload-field <?php echo esc_attr( $is_multiple ? 'multiple-uploads' : 'single-upload' ) ?> form-group-review-gallery">
	<div class="uploaded-files-list review-gallery-images">
		<div class="upload-file review-gallery-add listing-file-upload-input" data-name="<?php echo esc_attr( $field_name ) ?>" data-multiple="<?php echo $is_multiple ? 'yes' : '' ?>">
			<i class="mi file_upload"></i>
			<div class="content"></div>
		</div>

		<input type="hidden" class="input-text" name="<?php echo esc_attr( $field_name ) ?>">

		<div class="job-manager-uploaded-files">
			<?php foreach ( $uploaded_files as $file ): ?>
				<?php
				if ( is_numeric( $file ) ) {
					$image_src = wp_get_attachment_image_src( absint( $file ) );
					$image_src = $image_src ? $image_src[0] : '';
				} else {
					$image_src = $file;
				}

				$extension = ! empty( $extension ) ? $extension : substr( strrchr( $image_src, '.' ), 1 );
				$is_image = in_array( $extension, [ 'jpg', 'gif', 'png', 'jpeg', 'jpe', 'webp' ] ); ?>

				<div class="uploaded-file">
					<?php if ( $is_image ): ?>
						<span class="uploaded-image-preview" style="background-image: url('<?php echo esc_url( c27()->get_resized_image( $image_src, 'medium' ) ?: $image_src ) ?>');">
						</span>
					<?php else: ?>
						<span class="uploaded-file-preview">
							<i class="mi insert_drive_file uploaded-file-icon"></i>
							<code><?php echo esc_html( basename( $image_src ) ) ?></code>
						</span>
					<?php endif ?>

					<a class="remove-uploaded-file"><i class="mi delete"></i></a>
					<input type="hidden" class="input-text" name="current_<?php echo esc_attr( $field_name ) ?>"
						value="<?php echo esc_attr( 'b64:'.base64_encode( $file ) ) ?>">
				</div>
			<?php endforeach ?>
		</div>
	</div>

	<small class="description">
		<?php printf( _x( 'Maximum file size: %s.', 'Add listing form', 'my-listing' ), size_format( wp_max_upload_size() ) ); ?>
	</small>
</div>
