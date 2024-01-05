<?php
wp_print_styles('mylisting-quick-search-form');
$data = c27()->merge_options([
	'instance-id' => 'quick-search--' . uniqid(),
	'placeholder' => _x( 'Search...', 'Quicksearch placeholder', 'my-listing' ),
	'ref' => '',
	'align' => 'center',
	'style' => 'light',
	'focus' => 'default',
], $data);

$featured_categories = c27()->get_setting('header_search_form_featured_categories', []);
?>
<div class="quick-search-instance <?php echo esc_attr( 'text-' . $data['align'] ) ?>" id="<?php echo esc_attr( $data['instance-id'] ) ?>" data-focus="<?php echo esc_attr( $data['focus'] ) ?>">
	<form action="<?php echo esc_url( c27()->get_setting('general_explore_listings_page') ) ?>" method="GET">
		<div class="dark-forms header-search <?php echo $data['ref'] == 'shortcode' ? 'search-shortcode' : '' ?> search-shortcode-<?php echo esc_attr( $data['style'] ) ?>">
			<i class="mi search"></i>
			<input type="search" placeholder="<?php echo esc_attr( $data['placeholder'] ) ?>" name="search_keywords" autocomplete="off">
			<div class="instant-results">
				<ul class="instant-results-list ajax-results"></ul>
				<button type="submit" class="buttons full-width button-5 search view-all-results all-results">
					<i class="mi search"></i><?php _e( 'View all results', 'my-listing' ) ?>
				</button>
				<button type="submit" class="buttons full-width button-5 search view-all-results no-results">
					<i class="mi search"></i><?php _e( 'No results', 'my-listing' ) ?>
				</button>
				<div class="loader-bg">
					<?php c27()->get_partial( 'spinner', [
						'color' => '#777',
						'classes' => 'center-vh',
						'size' => 24,
						'width' => 2.5,
					] ) ?>
				</div>

				<?php if ( ! is_wp_error( $featured_categories ) && is_array( $featured_categories ) ): ?>
					<ul class="instant-results-list default-results">
        				<li class="ir-cat"><?php _e( 'Featured', 'my-listing' ) ?></li>

						<?php foreach ($featured_categories as $category):
							if ( ! $category instanceof \WP_Term ) {
								continue;
							}
							$term = new MyListing\Src\Term( $category );
							?>
							<li>
								<a href="<?php echo esc_url( $term->get_link() ) ?>">
									<span class="cat-icon" style="background-color: <?php echo esc_attr( $term->get_color() ) ?>;">
                                        <?php echo $term->get_icon([ 'background' => false ]) ?>
									</span>
									<span class="category-name"><?php echo esc_html( $term->get_name() ) ?></span>
								</a>
							</li>
						<?php endforeach ?>

					</ul>
				<?php endif ?>
			</div>
		</div>
	</form>
</div>