<?php

if ( ! defined('ABSPATH') ) {
	exit;
}

$shortcodes = \MyListing\Shortcodes::instance();
if ( ! $shortcodes->all() ) {
	return;
}

wp_enqueue_style( 'mylisting-admin-shortcodes' );
wp_enqueue_script( 'mylisting-admin-shortcodes' );

?>

<div class="tabs" id="case27-shortcode-generator" data-shortcodes="<?php echo esc_attr( $shortcodes->all_encoded() ) ?>" v-cloak>
	<div class="wrapper">
		<div class="column shortcodes-list">
			<h3>List of shortcodes</h3>
			<ul>
				<li v-for="shcode in shortcodes" @click="shortcode = shcode">{{ shcode.title }}</li>
			</ul>
		</div>

		<div class="column shortcode-options">
			<h3><?php _e( 'Shortcode Options', 'my-listing' ) ?></h3>
			<p>{{ shortcode.description }}</p>
			<br>

			<?php foreach ( $shortcodes->all() as $key => $shortcode ): ?>
				<div v-if="shortcode.name == '<?php echo esc_attr( $shortcode->name ) ?>'">
					<div class="form-wrapper">
						<?php $shortcode->output_options() ?>
					</div>
				</div>
			<?php endforeach ?>
		</div>

		<div class="column generated-shortcode">
			<h3><?php _e( 'Generated Code', 'my-listing' ) ?></h3>
			<p>Paste this code in the content area of your post or page.</p>
			<textarea class="generated" v-model="generated_shortcode" rows="10"></textarea>
		</div>
	</div>
</div>
