<?php
/**
 * The template for displaying reviews.
 *
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */

$comments_wrapper = 'col-md-7';
$comment_form_wrapper = 'col-md-5';

$reviews = MyListing\Ext\Author_Review\Author_Review::get_reviews();
$author_id = get_the_author_meta('ID');

if ( is_singular( 'job_listing' ) ) {
	global $post;
	$post_id = $post->ID;
	$author_id = get_post_field( 'post_author', $post_id );
}
?>

<div class="container">
	<div class="row">
		<div class="<?php echo esc_attr( $comments_wrapper ) ?> comments-list-wrapper">

			<?php if ( ! $reviews ) : ?>
				<div class="no-results-wrapper">
					<i class="no-results-icon material-icons mood_bad"></i>
					<li class="no_job_listings_found"><?php _e( 'No Reviews yet.', 'my-listing' ) ?></li>
				</div>
			<?php else: ?>
				<?php echo MyListing\Ext\Author_Review\Author_Review::get_reviews_html( $reviews ); ?>
			<?php endif ?>
		</div>

		<div class="<?php echo esc_attr( $comment_form_wrapper ) ?>">
			<div>
				<div class="element">
					<div class="validation-message"></div>
					<div class="pf-head">
						<div class="title-style-1">
							<i class="mi chat_bubble_outline"></i>
							<h5><?php _e( 'Add a comment', 'my-listing' ) ?></h5>
						</div>
					</div>
					<div class="pf-body">

						<!-- Rating Field -->
						<?php $rating_field = MyListing\Ext\Author_Review\Author_Review::get_ratings_field( false, $author_id ); ?>

						<!-- Author Field -->
						<?php ob_start(); ?>
						<div class="form-group">
							<label><?php _e( 'Name', 'my-listing' ) ?></label>
							<input name="author" type="text" value="<?php echo esc_attr( $commenter['comment_author'] ) ?>" required="required" placeholder="<?php echo esc_html__('Your Name', 'my-listing') ?>">
						</div>
						<?php $author_field = ob_get_clean(); ?>

						<!-- Email Field -->
						<?php ob_start(); ?>
						<div class="form-group">
							<label><?php _e( 'Email', 'my-listing' ) ?></label>
							<input name="email" type="email" value="<?php echo esc_attr( $commenter['comment_author_email'] ) ?>" required="required" placeholder="<?php echo esc_html__('Your Email', 'my-listing') ?>">
						</div>
						<?php $email_field = ob_get_clean(); ?>

						<!-- Message Field -->
						<?php ob_start(); ?>
						<div class="form-group">
							<label><?php _e( 'Your Message', 'my-listing' ) ?></label>
							<textarea rows="5" name="comment" required="required" placeholder="<?php echo esc_html__('Enter message...', 'my-listing') ?>"></textarea>
						</div>
						<?php $message_field = ob_get_clean(); ?>

						<!-- Submit Field -->
						<?php ob_start(); ?>
						<button name="submit" type="submit" class="buttons button-2 full-width review-submit">
							<?php echo esc_html__('Submit comment', 'my-listing') ?>
						</button>

						<?php $submit_field = ob_get_clean(); ?>

						<?php
						$args = array(
							'comment_field'       => $rating_field . $message_field . $submit_field,
							'class_submit'        => 'hide',
							'cancel_reply_before' => ' &middot; <span>',
							'cancel_reply_after'  => '</span>',
						);
						?>

						<div class="sidebar-comment-form">
							<div class="">
								<?php MyListing\Ext\Author_Review\Author_Review::review_form( $args ) ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>
