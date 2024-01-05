<?php
/**
 * Template Name: Content + Sidebar
 */

get_header(); the_post(); ?>

<section id="post-<?php echo esc_attr( get_the_ID() ) ?>" <?php post_class('i-section'); ?>>
	<div class="container">
		<div class="row section-body">
			<div class="col-md-9 page-content">
				<div class="element">
					<div class="pf-head">
						<div class="title-style-1">
							<h1><?php the_title() ?></h1>
						</div>
					</div>

					<div class="pf-body c27-content-wrapper">
						<?php the_content() ?>

						<?php wp_link_pages( array(
							'before' => '<div class="page-links">' . __( 'Pages:', 'my-listing' ),
							'after' => '</div>',
							)); ?>
					</div>
				</div>
			</div>
			<div class="col-md-3 page-sidebar sidebar-widgets">
				<?php dynamic_sidebar('sidebar') ?>
			</div>
		</div>
	</div>
</section>

<?php if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ): ?>
	<section class="i-section">
		<div class="container">
			<div class="row section-title">
				<p><?php _e( 'Comments', 'my-listing' ) ?></p>
				<h2 class="case27-primary-text"><?php _e( 'Add a comment', 'my-listing' ) ?></h2>
			</div>
		</div>
		<?php comments_template() ?>
	</section>
<?php endif ?>

<?php get_footer();