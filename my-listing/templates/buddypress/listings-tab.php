<div id="c27-bp-listings-wrapper" data-authid="<?php echo absint( bp_displayed_user_id() ) ?>">
	<div class="container">
		<div class="listings-loading">
			<div class="loader-bg">
				<?php c27()->get_partial( 'spinner', [
					'color' => '#777',
					'classes' => 'center-vh',
					'size' => 28,
					'width' => 3,
				] ) ?>
			</div>
		</div>
		<div class="row section-body c27-bp-listings-grid i-section"></div>
		<div class="c27-bp-listings-pagination text-center"></div>
	</div>
</div>
