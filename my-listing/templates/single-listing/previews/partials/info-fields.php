<?php

if ( ! defined('ABSPATH') ) {
	exit;
}

if ( empty( $options['info_fields'] ) ) {
	return;
} ?>

<ul class="lf-contact">
	<?php foreach ( (array) $options['info_fields'] as $info ) {
        $string = $info['label'];
        $attributes = [];

        if ( $is_caching ) {
            list( $string, $attributes, $cls ) = \MyListing\prepare_string_for_cache( $string, $listing );
        }

        $content = do_shortcode( $listing->compile_string( $string ) );
        if ( ! empty( $content ) ) { ?>
            <li <?php echo join( ' ', $attributes ) ?>>
            	<?php if ( ! empty( $info['icon'] ) ): ?>
                	<i class="<?php echo esc_attr( $info['icon'] ) ?> sm-icon"></i>
            	<?php endif ?>
                <?php echo $content ?>
            </li>
        <?php }
	} ?>
</ul>
