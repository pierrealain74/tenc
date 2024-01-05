<?php
/**
 * Single author template.
 *
 * @since 2.6
 */
if ( ! defined('ABSPATH') ) {
	exit;
}

get_header();
$author = new \MyListing\Src\User( get_user_by( 'slug', get_query_var( 'author_name' ) ) );
$description = $author->get_description();
$links = $author->get_social_links();
?>

<section class="user-profile-cover">
    <div class="main-info-desktop">
        <div class="container listing-main-info">
            <div class="col-md-6">
                <div class="profile-name no-tagline no-rating">
                	<?php if ( $avatar = get_avatar_url( $author->get_id() ) ): ?>
						<a
						    class="profile-avatar open-photo-swipe"
						    href="<?php echo esc_url( $avatar ) ?>"
						    style="background-image: url('<?php echo esc_url( $avatar ) ?>')"
						></a>
                	<?php endif ?>

                    <h1 class="case27-primary-text">
                        <?php echo esc_html( $author->get_display_name() ) ?>
                        <?php if ( absint( $author->get_id() ) === absint( get_current_user_id() ) ): ?>
	                        <a
	                        	href="<?php echo esc_url( wc_get_account_endpoint_url('edit-account') ) ?>"
	                        	class="edit-listing"
	                        	data-toggle="tooltip"
	                        	data-title="<?php echo esc_attr( _x( 'Edit account', 'Author page', 'my-listing' ) ) ?>"
	                        >
	                        	<i class="mi edit"></i>
	                        </a>
                        <?php endif ?>
                    </h1>
                </div>
            </div>

            <div class="col-md-6">
			    <div class="listing-main-buttons detail-count-1">
			        <ul>
			        	<li class="price-or-date">
    	                    <div class="lmb-label">
    	                    	<?php echo esc_html( _x( 'Joined', 'Author page', 'my-listing' ) ) ?>
    	                    </div>
    	                    <div class="value">
    	                    	<?php echo esc_html( date_i18n(
    	                    		get_option('date_format'),
    	                    		strtotime( $author->user_registered )
    	                    	) ) ?>
	                    	</div>
			        	</li>

			        	<?php if ( $count = count_user_posts( $author->get_id(), 'job_listing', true ) ): ?>
				        	<li class="price-or-date">
	    	                    <div class="lmb-label"><?php echo esc_html( _x( 'Active listings', 'Author page', 'my-listing' ) ) ?></div>
	    	                    <div class="value"><?php echo number_format_i18n( $count ) ?></div>
				        	</li>
			        	<?php endif ?>

						<?php if ( c27()->get_setting( 'messages_enabled', true ) !== false ): ?>
							<li id="cta-549f5e" class="lmb-calltoaction">
							    <a href="#" class="cts-open-chat" data-user-id="<?php echo absint( $author->get_id() ) ?>">
							    	<i class="icon-chat-bubble-square-add"></i>
							    	<span><?php echo esc_html( _x( 'Direct message', 'Author page', 'my-listing' ) ) ?></span>
							    </a>
							</li>
						<?php endif ?>
					</ul>
				</div>
			</div>
		</div>
    </div>
    <div class="profile-header">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="profile-menu">
                        <ul class="cts-carousel">
                        	<li class="active">
                            	<a href="#" class="profile-tab-toggle" data-section-id="reviews">
                            		<?php echo esc_html( _x( 'Profil', 'Author page', 'my-listing' ) ) ?>
                            	</a>
                            </li>
                            <li>
                            	<a href="#" class="profile-tab-toggle" data-section-id="listings">
                            		<?php echo esc_html( _x( 'Listings', 'Author page', 'my-listing' ) ) ?>
                            	</a>
                            </li>

                            <?php if ( $description || $links ): ?>
	                            <li>
	                            	<a href="#" class="profile-tab-toggle" data-section-id="about">
	                            		<?php echo esc_html( _x( 'About', 'Author page', 'my-listing' ) ) ?>
	                            	</a>
	                            </li>
                            <?php endif ?>
                            <li class="cts-prev">prev</li>
                            <li class="cts-next">next</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php if ( ! empty( $_GET['rating-submitted'] ) ): ?>
    <div class="container listing-notifications">
    	<div class="row">
    		<div class="col-md-12">
				<div class="woocommerce-message" role="alert">
					<?php echo esc_html( __( 'Your review has been submitted.', 'my-listing' ) ) ?>
					<a href="#" class="button wc-forward hide-notification"><?php _e( 'Close', 'my-listing' ) ?></a>
				</div>
			</div>
    	</div>
    </div>
<?php endif ?>

<div class="tab-content listing-tabs">
	<section class="profile-body listing-tab tab-active" id="profile_tab_reviews">
        <div>
        	<?php require get_stylesheet_directory() . '/comment-review.php'; ?>
        </div>
    </section>

    <section class="profile-body listing-tab tab-hidden" id="profile_tab_about">
        <div class="container">
            <div class="row">
            	<?php if ( $description ): ?>
                	<div class="col-md-6">
                		<div class="element content-block block-type-text">
            				<div class="pf-head">
            					<div class="title-style-1">
            						<i class="mi view_headline"></i>
            						<h5><?php echo esc_html( _x( 'About', 'Author page', 'my-listing' ) ) ?></h5>
            					</div>
            				</div>
            				<div class="pf-body">
								<p><?php echo $description ?></p>
            				</div>
                		</div>
                	</div>
            	<?php endif ?>

				<?php if ( $links ) : ?>
	            	<div class="col-md-6">
	            		<div class="element">
	        		        <div class="pf-head">
	        					<div class="title-style-1">
	        						<i class="mi view_module"></i>
	        						<h5><?php echo esc_html( _x( 'Follow', 'Author page', 'my-listing' ) ) ?></h5>
	        					</div>
	        		        </div>
	        		        <div class="pf-body">
								<?php mylisting_locate_template( 'templates/single-listing/content-blocks/lists/outlined-list.php', [
									'items' => array_map( function( $network ) {
										return [
											'name' => $network['name'],
											'icon' => sprintf( '<i class="%s"></i>', esc_attr( $network['icon'] ) ),
											'link' => $network['link'],
											'color' => $network['color'],
											'text_color' => '#fff',
											'target' => '_blank',
										];
									}, $links ) ] ) ?>
	        		        </div>
	        		    </div>
	        		</div>
				<?php endif; ?>
            </div>
        </div>
    </section>

    <section class="profile-body listing-tab tab-hidden" id="profile_tab_listings">
        <div class="container">
			<?php if ( have_posts() ): ?>
   				<div class="row section-body grid">
   					<?php while ( have_posts() ): the_post(); ?>
   						<?php if ( get_post_type() === 'job_listing' ): ?>
   							<?php printf(
   								'<div class="%s">%s</div>',
   								'col-md-4 col-sm-6 col-xs-12',
   								\MyListing\get_preview_card( get_the_ID() )
   							) ?>
   						<?php endif ?>
   					<?php endwhile ?>
   				</div>

   				<div class="blog-footer">
   					<div class="row project-changer">
   						<div class="text-center">
   							<?php echo paginate_links() ?>
   						</div>
   					</div>
   				</div>
			<?php else: ?>
			<div class="no-results-wrapper">
				<i class="no-results-icon mi mood_bad"></i>
				<p class="text-center">
					<?php echo esc_html( _x( 'This user does not have any listings.', 'Author page', 'my-listing' ) ) ?>
				</p>
			</div>
			<?php endif ?>
        </div>
    </section>
</div>

<?php get_footer() ?>
