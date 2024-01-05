<?php
/**
 * Display current author information, and ability
 * to switch author.
 *
 * @since 2.1.6
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$author = $listing->get_author();
?>

<div class="current_author">
	<?php if ( $author instanceof \WP_User ):
		$roles = array_filter( array_map( [ c27(), 'get_role_name' ], (array) $author->roles ) );
		?>
		<div class="auth_details">
			<?php echo get_avatar( $author->ID ) ?>
			<div class="auth_name">
				<a href="<?php echo esc_url( c27()->get_edit_user_link( $author->ID ) ) ?>" class="auth_link">
					<?php echo esc_html( $author->display_name ) ?>
				</a>
				<?php if ( ! empty( $roles ) ): ?>
					<span class="auth_role"><?php echo join( ', ', $roles ) ?></span>
				<?php endif ?>
			</div>
		</div>
	<?php endif ?>

	<input type="checkbox" name="mylisting_change_author" id="mylisting_change_author" value="yes">
	<a href="javascript:void(0)">
		<label for="mylisting_change_author"><?php _ex( 'Change Author', 'Author field', 'my-listing' ) ?></label>
	</a>
	<div class="change_author_dropdown">
		<select
			name="mylisting_author" id="mylisting_author" class="custom-select" data-mylisting-ajax="true"
			data-mylisting-ajax-url="mylisting_list_users" placeholder="<?php _ex( 'Select author', 'Author field', 'my-listing' ) ?>"
		>
			<?php if ( $author instanceof \WP_User ): ?>
				<option value="<?php echo esc_attr( $author->ID ) ?>" selected="selected"><?php echo esc_attr( $author->display_name ) ?></option>
			<?php endif ?>
		</select>
		<br>
		<div class="author_dropdown_actions">
			<label for="mylisting_change_author">
				<div class="button"><?php _ex( 'Cancel', 'Author field', 'my-listing' ) ?></div>
			</label>
			<button type="submit" class="button button-primary"><?php _ex( 'Apply', 'Author field', 'my-listing' ) ?></button>
		</div>
	</div>
</div>
