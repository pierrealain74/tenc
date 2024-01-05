<?php

namespace MyListing\Src\Forms\Fields;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Texteditor_Field extends Wp_Editor_Field {

	public function get_posted_value() {
		return isset( $_POST[ $this->key ] )
			? wp_kses_post( trim( stripslashes( $_POST[ $this->key ] ) ) )
			: '';
	}

	public function field_props() {
		parent::field_props();
		$this->props['type'] = 'texteditor';
		$this->props['editor-type'] = 'wp-editor';
	}

	public function get_editor_options() {
		$this->getLabelField();
		$this->getKeyField();
		$this->getPlaceholderField();
		$this->getDescriptionField();
		$this->getEditorTypeField();
		$this->getEditorControlsField();
		$this->getAllowShortcodesField();

		$this->getMinLengthField();
		$this->getMaxLengthField();

		$this->getRequiredField();
		$this->getShowInSubmitFormField();
		$this->getShowInAdminField();
		$this->getShowInCompareField();
	}

	protected function getEditorControlsField() { ?>
		<div v-if="field['editor-type'] == 'wp-editor'">
			<?php parent::getEditorControlsField() ?>
		</div>
	<?php }

	protected function getAllowShortcodesField() { ?>
		<div v-if="field['editor-type'] == 'wp-editor'">
			<?php parent::getAllowShortcodesField() ?>
		</div>
	<?php }

	protected function getEditorTypeField() { ?>
		<div class="form-group">
			<label>Type</label>
			<div class="select-wrapper">
				<select v-model="field['editor-type']">
					<option value="textarea">Textarea</option>
					<option value="wp-editor">WP Editor</option>
				</select>
			</div>
		</div>
	<?php }
}