<?php

namespace MyListing\Src\Listing_Types\Content_Blocks;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Contact_Form_Block extends Base_Block {

	public function props() {
		$this->props['type'] = 'contact_form';
		$this->props['title'] = 'Contact Form';
		$this->props['icon'] = 'mi email';
		$this->props['contact_form_id'] = false;
		$this->props['email_to'] = [ 'job_email' ];
	}

	public function get_editor_options() {
		$this->getLabelField();
		$this->getFormIdField();
		$this->getFormSourceField();
	}

	protected function getFormIdField() { ?>
		<div class="form-group">
			<label>Contact Form ID</label>
			<input type="number" v-model="block.contact_form_id" step="any">
		</div>
	<?php }

	protected function getFormSourceField() { ?>
		<div class="form-group">
			<label>Send email to</label>
			<select v-model="block.email_to" multiple="multiple">
				<option v-for="field in fieldsByType( [ 'email' ] )" :value="field.slug">{{ field.label }}</option>
			</select>
		</div>
	<?php }
}