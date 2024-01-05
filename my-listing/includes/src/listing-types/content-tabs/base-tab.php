<?php
/**
 * Base tab class which can be extended to construct tab types
 * for the listing type editor.
 *
 * @since 1.0
 */

namespace MyListing\Src\Listing_Types\Content_Tabs;

if ( ! defined('ABSPATH') ) {
	exit;
}

abstract class Base_Tab implements \JsonSerializable {

	/**
	 * Tab type. Alias of `$this->props['page']`
	 *
	 * @since 1.0
	 */
	public $type;

	/**
	 * List of tab properties/configuration. Values below are available for
	 * all tab types, but there can be additional props for specific tabs.
	 *
	 * @since 1.0
	 */
	public $props = [
		'page' => 'main',
		'label' => '',
		'default_label' => 'Profile',
		'slug' => '',
	];

	public function __construct( $props = [] ) {
		$this->tab_props();

		// override props if any provided as a parameter
		foreach ( $props as $key => $value ) {
			if ( isset( $this->props[ $key ] ) ) {
				$this->props[ $key ] = $value;
			}
		}

		$this->type = $this->props['page'];
	}

	/**
	 * When an object of this type is serialized, simply output its props.
	 *
	 * @since 1.0
	 */
	public function jsonSerialize() {
		return $this->props;
	}

	/**
	 * Used to override the default props or set new ones. `$this->props['page']` must
	 * be set for every tab that extends this class.
	 *
	 * @since 1.0
	 */
	abstract public function tab_props();

	/**
	 * Get the markup for tab settings to be shown in the listing type editor.
	 *
	 * @since 1.0
	 */
	abstract public function get_editor_options();

	/**
	 * Editor options - markup helpers.
	 *
	 * @since 1.0
	 */
	public function get_label_field() { ?>
		<div class="form-group mb20">
			<label>Label</label>
			<input type="text" v-model="menu_item.label">
		</div>
	<?php }

	public function get_slug_field() { ?>
		<div class="form-group mb20">
			<label>URL slug</label>
			<input type="text" :value="menu_item.slug" @input="menu_item.slug = slugify( $event.target.value )" :placeholder="slugify( menu_item.label )">
			<p>This value can be appended to the listing url to link directly to this tab.</p>
		</div>
	<?php }

	public function get_layout_field() { ?>
		<div class="form-group mb20">
			<label>Layout</label>
			<div class="select-wrapper">
				<select v-model="menu_item.template">
					<option value="masonry">Masonry (Two columns)</option>
					<option value="two-columns">Two Columns</option>
					<option value="content-sidebar">Two thirds / One third</option>
					<option value="sidebar-content">One third / Two thirds</option>
					<option value="full-width">Single column</option>
				</select>
			</div>
		</div>
	<?php }
}