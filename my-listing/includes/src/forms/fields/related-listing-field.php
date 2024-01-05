<?php

namespace MyListing\Src\Forms\Fields;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Related_Listing_Field extends Base_Field {

	private $cached_value;

	public function get_posted_value() {
		// if multiple, value must be an array
		if ( in_array( $this->props['relation_type'], [ 'has_many', 'belongs_to_many' ], true ) ) {
			return isset( $_POST[ $this->key ] )
				? array_map( 'absint', (array) $_POST[ $this->key ] )
				: [];
		}

		return isset( $_POST[ $this->key ] )
			? absint( $_POST[ $this->key ] )
			: false;
	}

	public function validate() {
		global $wpdb;

		$value = $this->get_posted_value();
		$imploded_ids = implode( ',', array_map( 'absint', (array) $value ) );
		if ( empty( $value ) || empty( $imploded_ids ) ) {
			return;
		}

		$listings = $wpdb->get_results( "SELECT ID, post_author, post_title, post_status FROM {$wpdb->posts} WHERE post_type = 'job_listing' AND ID IN ({$imploded_ids})", ARRAY_A );

		// in add listing form, we use `get_current_user_id` since the listing hasn't been created yet
		$author_id = ! empty( $this->listing ) ? absint( $this->listing->get_author_id() ) : get_current_user_id();

		foreach ( $listings as $listing ) {
			if ( $this->props['author_restriction'] === 'author' && absint( $listing['post_author'] ) !== $author_id ) {
				throw new \Exception( sprintf( _x( '%s - the following listing cannot be selected: %s, it belongs to another user.', 'Add listing form', 'my-listing' ), $this->props['label'], $listing['post_title'] ) );
			}

			if ( ! in_array( $listing['post_status'], $this->props['status_restriction'], true ) && $listing['post_status'] !== 'publish' ) {
				throw new \Exception( sprintf( _x( '%s - the following listing cannot be selected due to it\'s current status: %s', 'Add listing form', 'my-listing' ), $this->props['label'], $listing['post_title'] ) );
			}

			if ( ! in_array( get_post_meta( $listing['ID'], '_case27_listing_type', true ), (array) $this->props['listing_type'], true ) ) {
				throw new \Exception( sprintf( _x( '%s - the following listing cannot be selected: %s, it belongs to another listing type.', 'Add listing form', 'my-listing' ), $this->props['label'], $listing['post_title'] ) );
			}
		}
	}

	public function update() {
		global $wpdb;

		$value = $this->get_posted_value();
		$relation_type = $this->props['relation_type'];

		$current_value = $this->get_value();
		$current_ids = ! empty( $current_value ) ? (array) $current_value : [];

		// delete existing relations
		$delete_column = in_array( $relation_type, [ 'has_one', 'has_many' ], true ) ? 'parent_listing_id' : 'child_listing_id';
		$wpdb->delete( $wpdb->prefix.'mylisting_relations', [
			$delete_column => $this->listing->get_id(),
			'field_key' => $this->key,
		] );

		// all related listings have been removed
		$imploded_ids = implode( ',', array_map( 'absint', (array) $value ) );
		if ( empty( $value ) || empty( $imploded_ids ) ) {
			foreach ( $current_ids as $listing_id ) {
				$listing = \MyListing\Src\Listing::get( $listing_id );
				if ( $listing && $listing->get_status() === 'publish' ) {
					mlog()->note( 'Cached preview card for deleted relation and field #'.$listing->get_id().' ('.current_action().')' );
					\MyListing\cache_preview_card( $listing->get_id() );
				}
			}
			return;
		}

		// create query with updated values
		$query_rows = [];
		foreach ( (array) $value as $item_order => $listing_id ) {
			// if it's a `has_one` or `has_many` relation, then the current listing being updated
			// must be the parent listing in the database.
			$parent_id = in_array( $relation_type, [ 'has_one', 'has_many' ], true )
				? $this->listing->get_id()
				: $listing_id;

			// if it's a `belongs_to_one` or `belongs_to_many` relation, then the current listing being updated
			// must be the child listing in the database.
			$child_id = in_array( $relation_type, [ 'has_one', 'has_many' ], true )
				? $listing_id
				: $this->listing->get_id();

			$query_rows[] = $wpdb->prepare( '(%d,%d,%s,%d)', $parent_id, $child_id, $this->key, $item_order );
		}

		$query = "INSERT INTO {$wpdb->prefix}mylisting_relations (parent_listing_id, child_listing_id, field_key, item_order) VALUES ";
		$query .= implode( ',', $query_rows );

		// update database with new values
		$wpdb->query( $query );

		// get all deleted relations and regenerate preview card cache for those as well
		$deleted_relations = array_diff( (array) $current_ids, (array) $value );
		foreach ( $deleted_relations as $listing_id ) {
			$listing = \MyListing\Src\Listing::get( $listing_id );
			if ( $listing && $listing->get_status() === 'publish' ) {
				mlog()->note( 'Cached preview card for deleted relation #'.$listing->get_id().' ('.current_action().')' );
				\MyListing\cache_preview_card( $listing->get_id() );
			}
		}
	}

	public function get_value() {
		if ( ! is_null( $this->cached_value ) ) {
			return $this->cached_value;
		}

		$this->cached_value = $this->get_related_items();
		return $this->cached_value;
	}

	/**
	 * Get related items to this listing for this field.
	 *
	 * @since 2.2
	 */
	public function get_related_items() {
		global $wpdb;

		$is_multiple = in_array( $this->props['relation_type'], [ 'has_many', 'belongs_to_many' ], true );

		if ( in_array( $this->props['relation_type'], [ 'has_one', 'has_many' ], true ) ) {
			$rows = $wpdb->get_col( $wpdb->prepare( "
				SELECT child_listing_id FROM {$wpdb->prefix}mylisting_relations
				WHERE parent_listing_id = %d AND field_key = %s
				ORDER BY item_order ASC
			", $this->listing->get_id(), $this->key ) );
		}

		if ( in_array( $this->props['relation_type'], [ 'belongs_to_one', 'belongs_to_many' ], true ) ) {
			$rows = $wpdb->get_col( $wpdb->prepare( "
				SELECT parent_listing_id FROM {$wpdb->prefix}mylisting_relations
				WHERE child_listing_id = %d AND field_key = %s
				ORDER BY item_order ASC
			", $this->listing->get_id(), $this->key ) );
		}

		$ids = array_map( 'absint', (array) $rows );

		if ( empty( $ids ) ) {
			$items = $is_multiple ? [] : false;
		} else {
			$items = $is_multiple ? $ids : array_shift( $ids );
		}

		return apply_filters( 'mylisting/related-listing-field/get-related-items', $items, $this );
	}

	public function field_props() {
		$this->props['type'] = 'related-listing';
		$this->props['listing_type'] = [];
		$this->props['relation_type'] = 'belongs_to_one';
		$this->props['author_restriction'] = 'author';
		$this->props['status_restriction'] = [];
	}

	public function after_custom_props() {
		// this property was converted to an array in 2.2, keep compatibility
		if ( is_string( $this->props['listing_type'] ) ) {
			$this->props['listing_type'] = [ $this->props['listing_type'] ];
		}
	}

	public function get_editor_options() {
		$this->getLabelField();
		$this->getKeyField();
		$this->getPlaceholderField();
		$this->getDescriptionField();
		$this->getRelationSettings();
		$this->getRequiredField();
		$this->getShowInSubmitFormField();
		$this->getShowInAdminField();
		$this->getShowInCompareField();
	}

	protected function getRelationSettings() { ?>
		<div class="text-right">
			<a href="#" class="cts-show-tip btn btn-primary btn-xs mb10 mt10" data-tip="related-listings">
				<i class="mi info_outline"></i>
				Need Help?
			</a>
		</div>
		<div class="form-group full-width">
			<label>Related to:</label>

			<div class="mt5" style="column-count:3;">
				<?php foreach ( \MyListing\Src\Listing_Types\Editor::instance()->get_listing_types() as $listing_type ): ?>
					<label class="mb5 pt5">
						<input type="checkbox" value="<?php echo $listing_type->get_slug() ?>" v-model="field.listing_type" class="form-checkbox">
						<?php echo $listing_type->get_plural_name() ?>
					</label>
				<?php endforeach ?>
			</div>
		</div>

		<div class="form-group full-width">
			<label>Relation type:</label>
			<div class="select-wrapper">
				<select v-model="field.relation_type">
					<option value="has_one">Has One</option>
					<option value="has_many">Has Many</option>
					<option value="belongs_to_one">Belongs to One</option>
					<option value="belongs_to_many">Belongs to Many</option>
				</select>
			</div>
		</div>

		<div class="form-group full-width">
			<label>Limit listing selections by author:</label>
			<div class="select-wrapper">
				<select v-model="field.author_restriction">
					<option value="author">Only listings that belong to the current author</option>
					<option value="any">All listings that belong to any author</option>
				</select>
			</div>
		</div>

		<div class="form-group full-width">
			<label class="mb10">Limit listing selections by status:</label>
			<label class="mb10">
				<input type="checkbox" value="publish" class="form-checkbox" checked disabled>
				Published
			</label>

			<label class="mb10">
				<input type="checkbox" value="pending" v-model="field.status_restriction" class="form-checkbox">
				Pending approval
			</label>

			<label class="mb10">
				<input type="checkbox" value="pending_payment" v-model="field.status_restriction" class="form-checkbox">
				Pending payment
			</label>

			<label class="mb10">
				<input type="checkbox" value="expired" v-model="field.status_restriction" class="form-checkbox">
				Expired
			</label>
			<p>
				You can allow users to pre-select listings that haven't been approved yet.
				They will then be shown in the single listing page once they're published.
			</p>
		</div>
	<?php }
}