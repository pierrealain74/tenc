<?php
/**
 * Helper functions for block types that have content rows, like tables,
 * accordions, details, and tabs blocks.
 *
 * @since 2.2
 */

namespace MyListing\Src\Listing_Types\Content_Blocks\Traits;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait Content_Rows {

	/**
	 * Validate and format rows for use in template files.
	 *
	 * @since 2.2
	 */
	public function get_formatted_rows( $listing ) {
		$rows = [];
		foreach ( (array) $this->get_prop('rows') as $row ) {
			if ( ! is_array( $row ) || empty( $row['content'] ) ) {
				continue;
			}

			$content = do_shortcode( $listing->compile_string( $row['content'] ) );
            if ( ! empty( $content ) ) {
			    $rows[] = [
			        'title' => $row['label'] ?? '',
			        'content' => $content,
	        		'icon' => $row['icon'] ?? '',
			    ];
            }
		}

		return $rows;
	}

	protected function getRowsField() { ?>
		<div class="repeater-option">
			<label>Rows</label>
			<draggable v-model="block.rows" :options="{group: 'repeater', handle: '.row-head'}">
				<div v-for="row, row_id in block.rows" class="row-item">
					<div class="row-head" @click="toggleRepeaterItem($event)">
						<div class="row-head-toggle"><i class="mi chevron_right"></i></div>
						<div class="row-head-label">
							<h4>
								<span v-for="part in $root.getLabelParts( row.content, '(click to edit)' )"
									:class="'label-part-'+part.type" v-text="part.content"></span>
							</h4>
							<div class="details">
								<div class="detail">Click to edit</div>
							</div>
						</div>
						<div class="row-head-actions">
							<span title="Remove" @click.stop="block.rows.splice(row_id, 1)" class="action red"><i class="mi delete"></i></span>
						</div>
					</div>
					<div class="row-edit">
						<div class="form-group" v-if="block.type === 'details'">
							<label>Icon</label>
							<iconpicker v-model="row.icon"></iconpicker>
						</div>

						<div class="form-group" v-if="block.type !== 'details'">
							<label>Label</label>
							<input type="text" v-model="row.label">
						</div>

						<div class="form-group">
							<label>Content</label>
							<atwho v-model="row.content" template="input"></atwho>
						</div>

						<div class="text-right mt10">
							<div class="btn btn-xs" @click.prevent="toggleRepeaterItem($event)">Done</div>
						</div>
					</div>
				</div>
				<div class="text-right mt10">
					<div class="btn btn-xs" @click.prevent="block.rows.push({label: '', content: '', icon: ''})">Add row</div>
				</div>
			</draggable>
		</div>
	<?php }

}
