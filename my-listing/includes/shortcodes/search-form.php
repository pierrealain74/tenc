<?php

namespace MyListing\Shortcodes;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Search_Form {

	public
		$name = '27-search-form',
		$title = '',
		$description = '',
	    $content = null,
	    $attributes = [
		    'listing_types' => [],
		    'tabs_mode' => 'light',
		    'types_display' => 'tabs',
		    'box_shadow' => 'no',
		    'search_page_id' => '',
	    ];

	public function __construct() {
		$this->title = __( 'Search Form', 'my-listing' );
		$this->description = __( 'A search form widget suited for featured sections.', 'my-listing' );
		add_shortcode( $this->name, [ $this, 'add_shortcode' ] );
	}

	public function add_shortcode( $atts, $content = null ) {
		$atts = shortcode_atts( $this->attributes, $atts );
		if ( ! $atts['search_page_id'] ) {
			$atts['search_page_id'] = c27()->get_setting( 'general_explore_listings_page' );
		}

		ob_start();

		$listing_types = \MyListing\get_basic_form_config_for_types( $atts['listing_types'] );
		$types_config = $listing_types['config'];
		$types = $listing_types['types'];

		$config = [
			'form_id' => sprintf( 'sform-%s', \MyListing\Utils\Random_Id::generate(5) ),
			'tabs_mode' => $atts['tabs_mode'],
			'types_display' => $atts['types_display'],
			'box_shadow' => $atts['box_shadow'] === 'yes',
			'target_url' => is_numeric( $atts['search_page_id'] )
				? get_permalink( absint( $atts['search_page_id'] ) )
				: $atts['search_page_id'],
		];

		require locate_template( 'partials/search-form.php' );

		if ( class_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			printf(
				'<script type="text/javascript">%s</script>',
				'document.dispatchEvent( new Event( "mylisting:refresh-basic-forms" ) ); case27_ready_script(jQuery);'
			);
		}

		return ob_get_clean();
	}

	public function output_options() {
		$listing_types = \MyListing\get_posts_dropdown( 'case27_listing_type', 'post_name' ) ?>
		<div class="form-group">
			<label><?php _e( 'Listing Type(s)', 'my-listing' ) ?></label>
			<select v-model="shortcode.attributes.listing_types" multiple="multiple">
				<?php foreach ($listing_types as $slug => $name): ?>
					<option value="<?php echo esc_attr( $slug ) ?>"><?php echo esc_html( $name ) ?></option>
				<?php endforeach ?>
			</select>
		</div>

		<div class="form-group">
			<label><?php _e( 'Display listing types as', 'my-listing' ) ?></label>
			<select v-model="shortcode.attributes.types_display">
				<option value="tabs"><?php _e( 'Tabs', 'my-listing' ) ?></option>
				<option value="dropdown"><?php _e( 'Dropdown', 'my-listing' ) ?></option>
			</select>
		</div>

		<div class="form-group">
			<label><?php _e( 'Style', 'my-listing' ) ?></label>
			<select v-model="shortcode.attributes.tabs_mode">
				<option value="light"><?php _e( 'Light tabs', 'my-listing' ) ?></option>
				<option value="dark"><?php _e( 'Dark tabs', 'my-listing' ) ?></option>
				<option value="transparent"><?php _e( 'Transparent', 'my-listing' ) ?></option>
			</select>
		</div>

		<div class="form-group">
			<label><?php _e( 'Box Shadow?', 'my-listing' ) ?></label>
			<select v-model="shortcode.attributes.box_shadow">
				<option value="yes"><?php _e( 'Yes', 'my-listing' ) ?></option>
				<option value="no"><?php _e( 'No', 'my-listing' ) ?></option>
			</select>
		</div>

		<div class="form-group">
			<label><?php _e( 'Search Page ID', 'my-listing' ) ?><br><small><?php _e( 'Leave blank to use the main explore page.', 'my-listing' ) ?></small></label>
			<input type="number" v-model="shortcode.attributes.search_page_id">
		</div>
	<?php }
}
