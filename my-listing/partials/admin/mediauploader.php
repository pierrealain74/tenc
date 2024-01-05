<?php
/**
 * MediaUploader template.
 *
 * @since 2.8
 */
if ( ! defined('ABSPATH') ) {
	exit;
}

wp_enqueue_media();
?>
<script id="cts-image-media-template" type="text/script">
	<div class="c27-image-uploader">
		<div class="selected-image" v-if="selected">
			<img :src="selected" class="avatar avatar-96 photo" />
		</div>
		<div class="hide-if-value">
			<p>No image selected <a @click.prevent="media_upload" class="upload-button button" href="#">Add Image</a></p>
		</div>
	</div>
</script>