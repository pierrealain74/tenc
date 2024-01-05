<!-- Quick view modal -->
<?php
	wp_print_styles('mylisting-quick-view-modal');
?>
<div id="quick-view" class="modal modal-27 quick-view-modal c27-quick-view-modal" role="dialog">
	<div class="container">
		<div class="modal-dialog">
			<div class="modal-content"></div>
		</div>
	</div>
	<div class="loader-bg">
		<?php c27()->get_partial('spinner', [
			'color' => '#ddd',
			'classes' => 'center-vh',
			'size' => 28,
			'width' => 3,
			]); ?>
	</div>
</div>