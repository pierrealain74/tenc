<?php

if ( ! defined('ABSPATH') ) {
	exit;
}

// load assets
wp_enqueue_style( 'jsoneditor' );
wp_enqueue_script( 'jsoneditor' );
wp_enqueue_script( 'sortablejs' );
wp_enqueue_script( 'vue-draggable' );
wp_enqueue_script( 'mylisting-admin-type-editor' );

$editor = $designer = MyListing\Src\Listing_Types\Editor::instance();
$type = MyListing\Src\Listing_Type::get( $post );
$config = $type->get_config()->prepare_for_editor();
$default_config = \MyListing\Src\Listing_Types\Default_Config::get();

$type_editor_data = apply_filters( 'mylisting/type-editor:config', [
	'preset_fields' => MyListing\Src\Forms\Fields\Presets::get( $type ),
	'custom_fields' => $designer->get_field_types(),
	'modifiers' => $designer->get_field_modifiers(),
	'special_keys' => $designer->get_special_keys(),
	'content_tabs' => $designer->get_tab_types(),
	'content_blocks' => $designer->get_block_types(),
] );
?>

<script type="text/javascript">
	var CASE27_TypeDesigner = {
		config: <?php echo wp_json_encode( $config ) ?>,
		blueprints: {
			quick_actions: <?php echo json_encode( $editor->get_quick_actions() ) ?>,
			structured_data: <?php echo json_encode( $type->get_default_schema_markup() ) ?>,
			filters: <?php echo json_encode( $designer->get_filter_types() ) ?>,
			explore_tabs: <?php echo wp_json_encode( $designer->get_explore_tab_presets() ) ?>
		},
		listing_packages: <?php echo json_encode( $designer->get_packages_dropdown() ) ?>,
		schemes: <?php echo wp_json_encode( $default_config ) ?>,
		fieldAliases: <?php echo json_encode( array_flip( MyListing\Src\Listing::$aliases ) ) ?>,
	};

	window.Type_Editor_Data = <?php echo wp_json_encode( $type_editor_data ) ?>;
</script>

<div class="tabs type-editor" id="case27-listing-options-inside" v-cloak>
	<input type="hidden" id="case27-post-id" value="<?php echo esc_attr( $post->ID ) ?>">

	<input type="hidden" v-model="settings_page_json_string" name="case27_listing_type_settings_page">
	<input type="hidden" v-model="result_template_json_string" name="case27_listing_type_result_template">
	<input type="hidden" v-model="search_page_json_string" name="case27_listing_type_search_page">
	<input type="hidden" v-model="fields_json_string" name="case27_listing_type_fields">
	<input type="hidden" v-model="single_page_options_json_string" name="case27_listing_type_single_page_options">

	<nav class="editor-nav">
		<div class="editor-nav-wrapper">
			<nav-item label="General" tab="settings" :icon="settings.icon ? settings.icon : 'icon-location-pin-add-2'" subtab="general" color="#8070c3"></nav-item>
			<nav-item label="Fields" tab="fields" icon="mi menu" color="#0085ba"></nav-item>
			<nav-item label="Single Page" tab="single-page" icon="mi web" subtab="style" color="#e26396"></nav-item>
			<nav-item label="Preview Card" tab="result-template" icon="mi view_day" subtab="preview-card" color="#69c9b4"></nav-item>
			<nav-item label="Search Forms" tab="search-page" icon="mi search" subtab="advanced" color="#ee8c57"></nav-item>
		</div>
	</nav>

	<div class="tabs-content">
		<section v-if="currentTab === 'settings'" class="section">
			<div class="sub-tabs">
				<nav-sub-item tab="settings" subtab="general" label="General"></nav-sub-item>
				<nav-sub-item tab="settings" subtab="packages" label="Packages"></nav-sub-item>
				<nav-sub-item tab="settings" subtab="reviews" label="Reviews"></nav-sub-item>
				<nav-sub-item tab="settings" subtab="expiry-rules" label="Expiry rules"></nav-sub-item>
				<nav-sub-item tab="settings" subtab="seo" label="Schema"></nav-sub-item>
				<nav-sub-item tab="settings" subtab="other" label="Other"></nav-sub-item>
			</div>

			<?php require locate_template( 'includes/src/listing-types/views/settings/general.php' ) ?>
			<?php require locate_template( 'includes/src/listing-types/views/settings/packages.php' ) ?>
			<?php require locate_template( 'includes/src/listing-types/views/settings/reviews.php' ) ?>
			<?php require locate_template( 'includes/src/listing-types/views/settings/seo.php' ) ?>
			<?php require locate_template( 'includes/src/listing-types/views/settings/expiry-rules.php' ) ?>
			<?php require locate_template( 'includes/src/listing-types/views/settings/other.php' ) ?>
		</section>

		<section v-if="currentTab === 'fields'" class="section">
			<?php require_once locate_template( 'includes/src/listing-types/views/fields.php' ) ?>
		</section>

		<section v-if="currentTab === 'single-page'" class="section">
			<div class="sub-tabs">
				<nav-sub-item tab="single-page" subtab="style" label="Cover style"></nav-sub-item>
				<nav-sub-item tab="single-page" subtab="cover-details" label="Cover details"></nav-sub-item>
				<nav-sub-item tab="single-page" subtab="quick-actions" label="Quick Actions"></nav-sub-item>
				<nav-sub-item tab="single-page" subtab="pages" label="Content &amp; Tabs"></nav-sub-item>
				<nav-sub-item tab="single-page" subtab="similar-listings" label="Similar Listings"></nav-sub-item>
			</div>

			<?php require_once locate_template( 'includes/src/listing-types/views/single-page/cover-style.php' ) ?>
			<?php require_once locate_template( 'includes/src/listing-types/views/single-page/cover-details.php' ) ?>
			<?php require_once locate_template( 'includes/src/listing-types/views/single-page/quick-actions.php' ) ?>
			<?php require_once locate_template( 'includes/src/listing-types/views/single-page/content-tabs.php' ) ?>
			<?php require_once locate_template( 'includes/src/listing-types/views/single-page/similar-listings.php' ) ?>
		</section>

		<section v-if="currentTab === 'result-template'" class="section">
			<div class="sub-tabs">
				<nav-sub-item tab="result-template" subtab="preview-card" label="Preview Card"></nav-sub-item>
				<nav-sub-item tab="result-template" subtab="quick-view" label="Quick View"></nav-sub-item>
			</div>

			<?php require_once locate_template( 'includes/src/listing-types/views/preview-card/preview.php' ) ?>
			<?php require_once locate_template( 'includes/src/listing-types/views/preview-card/quick-view.php' ) ?>
		</section>

		<section v-if="currentTab === 'search-page'" class="section">
			<div class="sub-tabs">
				<nav-sub-item tab="search-page" subtab="advanced" label="Advanced Form"></nav-sub-item>
				<nav-sub-item tab="search-page" subtab="basic" label="Basic Form"></nav-sub-item>
				<nav-sub-item tab="search-page" subtab="order" label="Listing Order"></nav-sub-item>
				<nav-sub-item tab="search-page" subtab="explore-tabs" label="Explore Tabs"></nav-sub-item>
			</div>

			<?php require_once locate_template( 'includes/src/listing-types/views/search-forms/forms.php' ) ?>
			<?php require_once locate_template( 'includes/src/listing-types/views/search-forms/listing-order.php' ) ?>
			<?php require_once locate_template( 'includes/src/listing-types/views/search-forms/explore-tabs.php' ) ?>
		</section>
	</div>
</div>
