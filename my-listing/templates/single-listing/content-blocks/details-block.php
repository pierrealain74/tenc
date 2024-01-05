<?php
/**
 * Template for rendering a `details` block in single listing page.
 *
 * @since 1.0
 */
if ( ! defined('ABSPATH') ) {
    exit;
}

$rows = $block->get_formatted_rows( $listing );
if ( empty( $rows ) ) {
    return;
}
?>

<div class="<?php echo esc_attr( $block->get_wrapper_classes() ) ?>" id="<?php echo esc_attr( $block->get_wrapper_id() ) ?>">
    <div class="element">
        <div class="pf-head">
			<div class="title-style-1">
				<i class="<?php echo esc_attr( $block->get_icon() ) ?>"></i>
				<h5><?php echo esc_html( $block->get_title() ) ?></h5>
			</div>
        </div>
        <div class="pf-body">
            <ul class="outlined-list details-block-content">

                <?php foreach ( $rows as $row ): ?>
                    <li>
                        <i class="<?php echo esc_attr( $row['icon'] ) ?>"></i>
                        <span class="wp-editor-content"><?php echo $row['content'] ?></span>
                    </li>
                <?php endforeach ?>

            </ul>
        </div>
    </div>
</div>
