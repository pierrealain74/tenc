<?php
/**
 * Bookings tab type, that can use content blocks to create the layout.
 *
 * @since 1.0
 */

namespace MyListing\Src\Listing_Types\Content_Tabs;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Bookings_Tab extends Base_Tab {

	public function tab_props() {
		$this->props['page'] = 'bookings';
		$this->props['field'] = '';
		$this->props['provider'] = [];
		$this->props['contact_form_id'] = 0;
		$this->props['default_label'] = 'Bookings';
	}

	public function get_editor_options() {
		$this->get_label_field();
		$this->get_slug_field();
		$this->get_provider_field();
		$this->get_basic_form_source_field();
		$this->get_basic_form_id_field();
		$this->get_timekit_source_field();
	}

	public function get_provider_field() { ?>
		<div class="form-group mb20">
			<label>Booking Method:</label>
			<div class="select-wrapper">
				<select v-model="menu_item.provider">
					<option value="basic-form">Basic Form</option>
					<option value="timekit">Timekit</option>
				</select>
			</div>
		</div>
	<?php }

	public function get_basic_form_source_field() { ?>
		<div class="form-group mb20" v-if="menu_item.provider === 'basic-form'">
			<label>Submission sends email to:</label>
			<div class="select-wrapper">
				<select v-model="menu_item.field">
					<option v-for="field in fieldsByType(['email'])" :value="field.slug">{{ field.label }}</option>
				</select>
			</div>
		</div>
	<?php }

	public function get_basic_form_id_field() { ?>
		<div class="form-group mb20" v-if="menu_item.provider === 'basic-form'">
			<label>Contact Form ID:</label>
			<input type="text" v-model="menu_item.contact_form_id">
		</div>
	<?php }

	public function get_timekit_source_field() { ?>
		<div class="form-group mb20" v-if="menu_item.provider === 'timekit'">
			<label>TimeKit Widget ID:</label>
			<div class="select-wrapper">
				<select v-model="menu_item.field">
					<option v-for="field in fieldsByType(['text'])" :value="field.slug">{{ field.label }}</option>
				</select>
			</div>
		</div>
	<?php }
}