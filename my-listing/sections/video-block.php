<?php
$data = c27()->merge_options([
	'icon' => '',
	'icon_style' => 1,
	'title' => '',
	'video_url' => '',
	'wrapper_class' => 'block-element grid-item',
	'wrapper_id' => '',
	'ref' => '',
], $data);

$video = \MyListing\Helpers::get_video_embed_details( $data['video_url'] );

// validate
if ( ! ( $data['video_url'] && $video ) ) {
	return;
}
?>

<div class="<?php echo esc_attr( $data['wrapper_class'] ) ?>" <?php echo $data['wrapper_id'] ? sprintf( 'id="%s"', $data['wrapper_id'] ) : '' ?>>
	<div class="element video-block">
		<div class="pf-head">
			<div class="title-style-1 title-style-<?php echo esc_attr( $data['icon_style'] ) ?>">
				<?php if ($data['icon_style'] != 3): ?>
					<?php echo c27()->get_icon_markup($data['icon']) ?>
				<?php endif ?>
				<h5><?php echo esc_html( $data['title'] ) ?></h5>
			</div>
		</div>
		<div class="pf-body video-block-body">
			<iframe src="<?php echo esc_attr( $video['url'] ) ?>" frameborder="0" allowfullscreen height="315"></iframe>
		</div>
	</div>
</div>
