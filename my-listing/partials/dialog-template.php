<script id="mylisting-dialog-template" type="text/template">
	<div class="mylisting-dialog-wrapper">
		<div class="mylisting-dialog">
			<div class="mylisting-dialog--message"></div><!--
			 --><div class="mylisting-dialog--actions">
				<div class="mylisting-dialog--dismiss mylisting-dialog--action"><?php _ex( 'Dismiss', 'Close dialog button text', 'my-listing' ) ?></div>
				<div class="mylisting-dialog--loading mylisting-dialog--action hide">
					<?php c27()->get_partial( 'spinner', [
						'color' => '#777',
						'classes' => '',
						'size' => 24,
						'width' => 2.5,
					] ) ?>
				</div>
			</div>
		</div>
	</div>
</script>