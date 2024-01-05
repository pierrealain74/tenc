<?php

	wp_print_styles( 'mylisting-footer' );

	$data = c27()->merge_options([
			'footer_background'=> c27()->get_setting('footer_background_color', '#fff'),
			'footer_text'      => c27()->get_setting('footer_text', ''),
			'show_widgets'     => c27()->get_setting('footer_show_widgets', true),
			'show_footer_menu' => c27()->get_setting('footer_show_menu', true),
		], $data);

	if ($data['footer_background']) {
		if (!isset($GLOBALS['case27_custom_styles'])) $GLOBALS['case27_custom_styles'] = '';

		$GLOBALS['case27_custom_styles'] .= 'footer.footer';
		$GLOBALS['case27_custom_styles'] .= '{ background: ' . $data['footer_background'] . ' }';
	}
?>

<footer class="footer <?php echo esc_attr( ! $data['show_widgets'] ? 'footer-mini' : '' ) ?>">
	<div class="container">
		<?php if ( $data['show_widgets'] ): ?>
			<div class="row">
				<?php dynamic_sidebar('footer') ?>
			</div>
		<?php endif ?>

		<div class="row">
			<div class="col-md-12">
				<div class="footer-bottom">
					<div class="row">
						<?php if ($data['show_footer_menu']): ?>
							<div class="col-md-12 col-sm-12 col-xs-12 social-links">
								<?php wp_nav_menu([
									'theme_location' => 'footer',
									'container' => false,
									'menu_class' => 'main-menu',
									'items_wrap' => '<ul id="%1$s" class="%2$s social-nav">%3$s</ul>'
									]); ?>
							</div>
						<?php endif ?>
						<div class="col-md-12 col-sm-12 col-xs-12 copyright">
							<p><?php echo str_replace( '{{year}}', date('Y'), $data['footer_text'] ) ?></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</footer>
