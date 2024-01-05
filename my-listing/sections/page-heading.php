<?php
$data = c27()->merge_options( [
	'title' => '',
	'subtitle' => '',
	'title_color' => '#fff',
	'subtitle_color' => '#fff',
], $data );
wp_print_styles('mylisting-page-heading-widget');
?>

<section class="page-head ph-type-1">
	<div class="overlay"></div>
	<div class="ph-details">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<p style="color: <?php echo esc_attr( $data['subtitle_color'] ) ?>"><?php echo esc_html( $data['subtitle'] ) ?></p>
					<h1 class="case27-primary-text"><?php echo $data['title'] ? esc_html( $data['title'] ) : the_title() ?></h1>
				</div>
			</div>
		</div>
	</div>
</section>