<?php
/**
 * Base field class which can be extended to construct field types
 * for the listing type editor.
 *
 * @since 1.0
 */

namespace MyListing\Src\Forms\Fields;

if ( ! defined('ABSPATH') ) {
	exit;
}

abstract class Base_Field implements \JsonSerializable, \ArrayAccess {
	use Traits\Editor_Markup_Helpers;
	use Traits\Validation_Helpers;

	/**
	 * Slugified string used to identify a field. Alias of `$this->props['slug']`
	 *
	 * @since 1.0
	 */
	public $key;

	/**
	 * Listing object which this field belongs to.
	 *
	 * @since 1.0
	 */
	public $listing;

	/**
	 * Listing type object which this field belongs to.
	 *
	 * @since 1.0
	 */
	public $listing_type;

	/**
	 * Array of field value modifiers to be used with the bracket syntax, formatted as
	 * key=>value pairs of modifier_key=>modifier_label. Modifier label can include "%s"
	 * which will be replaced with the field label, for example:
	 * "%s Latitude" => "Location Latitude" (location field's "lat" modifier).
	 *
	 * @since 2.4.5
	 */
	public $modifiers = [];

	/**
	 * List of field properties/configuration. Values below are available for
	 * all field types, but there can be additional props for specific field types.
	 *
	 * @since 1.0
	 */
	public $props = [
		'type'                => 'text',
		'slug'                => 'custom-field',
		'default'             => '',
		'priority'            => 10,
		'is_custom'           => true,
		'label'               => 'Custom Field',
		'default_label'       => 'Custom Field',
		'placeholder'         => '',
		'description'         => '',
		'required'            => false,
		'show_in_admin'       => true,
		'show_in_submit_form' => true,
		'show_in_compare' => true,
		'conditional_logic' => false,
		'conditions' => [ [ [ 'key' => '__listing_package', 'compare' => '==', 'value' => '', ] ] ],
	];

	public function __construct( $props = [] ) {
		$this->field_props();

		// override props if any provided as a parameter
		foreach ( $props as $key => $value ) {
			if ( isset( $this->props[ $key ] ) ) {
				$this->props[ $key ] = $value;
			}
		}

		$this->after_custom_props();
		$this->key = $this->props['slug'];

		$this->init();
	}

	/**
	 * Get and sanitize the posted field value in Add Listing, Edit Listing,
	 * and backend Add Listing forms. Required for every field type.
	 *
	 * @since  1.0
	 * @return sanitized field value
	 */
	abstract public function get_posted_value();

	/**
	 * Validate the field value after submission in Add Listing, Edit Listing,
	 * and backend Add Listing forms. Required for every field type.
	 *
	 * Use `$this->get_posted_value()` to get the field value, then mark any errors
	 * using exceptions. If no excpetions are thrown, validation is successful.
	 *
	 * @since  1.0
	 * @return void
	 */
	abstract public function validate();

	/**
	 * Field validation is generally bypassed in wp-admin to allow more flexibility.
	 * However, it can sometimes be necessary to force some validations even for
	 * admin users.
	 *
	 * @since 2.6.3
	 */
	public function admin_validate() {
		//
	}

	/**
	 * Runs after field props have been populated.
	 *
	 * @since 2.6.3
	 */
	public function init() {
		//
	}

	/**
	 * After the field value has been validated successfully, update listing with the
	 * field value, in Add Listing, Edit Listing, and backend Add Listing forms.
	 *
	 * By default, it will store the value in `wp_postmeta` table, with the field key
	 * prefixed with an underscore. This method can be overridden to save the value in
	 * different formats, e.g. the Term Select or Related Listing fields.
	 *
	 * @since 1.0
	 */
	public function update() {
		if ( $this->key === 'job_title' || $this->key === 'job_description' ) {
			return;
		}

		$value = $this->get_posted_value();
		update_post_meta( $this->listing->get_id(), '_'.$this->key, $value );
	}

	/**
	 * Get the field value from database. By default, it will be retrieved from `wp_postmeta`
	 * table. If the `update` method has been overriden to save in a different format, then
	 * this method must be overridden as well to get the value from there.
	 *
	 * @since 2.2
	 */
	public function get_value() {
		if ( $this->key === 'job_title' ) {
			return $this->listing->get_name();
		}

		if ( $this->key === 'job_description' ) {
			return $this->listing->get_data('post_content');
		}

		// othewise, retrieve from wp_postmeta
		return get_post_meta( $this->listing->get_id(), '_'.$this->key, true );
	}

	/**
	 * Get a string representation of the field value. For use in locations that support
	 * the bracket listing field syntax through `$listing->compile_string()` method.
	 *
	 * @since 2.2
	 */
	public function get_string_value( $modifier = null ) {
		/**
		 * Add support for custom field modifiers. Example usage, adding a
		 * "coordinates" modifier to the "location" field:
		 *
		 * add_filter( 'mylisting/location-field/modifiers', function( $modifiers ) {
		 *     $modifiers['coordinates'] = '%s Coordinates';
		 *     return $modifiers;
		 * } );
		 *
		 * add_filter( 'mylisting/location-field/apply-modifier:coordinates', function( $value, $field ) {
		 *     return sprintf(
		 *         '%s,%s',
		 *         $field->listing->get_special_key(':lat'),
		 *         $field->listing->get_special_key(':lng')
		 *     );
		 * }, 30, 2 );
		 *
		 * @since 2.4.5
		 */
		if ( ! empty( $modifier ) && ! isset( $this->modifiers[ $modifier ] ) ) {
			$filter = sprintf( 'mylisting/%s-field/apply-modifier:%s', $this->get_type(), esc_attr( $modifier ) );
			return apply_filters( $filter, '', $this );
		}

		return $this->string_value( $modifier );
	}

	public function string_value( $modifier = null ) {
		return $this->get_value();
	}

	/**
	 * Used to override the default props or set new ones. `$this->props['type']` must
	 * be set for every field type that extends this class.
	 *
	 * @since 1.0
	 */
	abstract public function field_props();

	/**
	 * Get the markup for field settings to be shown in the listing type editor.
	 *
	 * @since 1.0
	 */
	abstract public function get_editor_options();

	/**
	 * Fired after custom props have been merged. Can be optionally overriden
	 * to validate a prop or add backward compatibility.
	 *
	 * @since 2.2
	 */
	public function after_custom_props() {}

	/**
	 * Print the editor settings markup.
	 *
	 * @since 1.0
	 */
	final public function print_editor_options() {
		ob_start(); ?>
		<div class="field-settings-wrapper" v-if="field.type == '<?php echo esc_attr( $this->props['type'] ) ?>'">
			<?php $this->get_editor_options(); ?>
			<?php $this->get_visibility_settings() ?>
		</div>
		<?php return ob_get_clean();
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
	 * Validate common rules among all field types,
	 * then run the unique validations for each field.
	 *
	 * @since 2.1
	 */
	public function check_validity() {
		$value = $this->get_posted_value();

		// required field check
		// 0, '0', and 0.0 need special handling since they're valid, but PHP considers them falsy values.
		if ( $this->props['required'] && ( empty( $value ) && ! in_array( $value, [ 0, '0', 0.0 ], true ) ) ) {
			// translators: Placeholder %s is the label for the required field.
			throw new \Exception( sprintf( _x( '%s is a required field.', 'Add listing form', 'my-listing' ), $this->props['label'] ) );
		}

		// if field isn't required, then no validation is needed for empty values
		if ( empty( $value ) ) {
			return;
		}

		// otherwise, run validations
		$this->validate();
	}

	/**
	 * Set the listing for this field if available.
	 *
	 * @since 2.1
	 */
	public function set_listing( $listing ) {
		$this->listing = $listing;
	}

	/**
	 * Set the listing type for this field if available.
	 *
	 * @since 2.1
	 */
	public function set_listing_type( $listing_type ) {
		$this->listing_type = $listing_type;
	}

	/**
	 * Implements \ArrayAccess interface to keep compatibility with older
	 * snippets where fields were associative arrays.
	 *
	 * @link  https://www.php.net/manual/en/class.arrayaccess.php
	 * @since 2.2
	 */
    public function offsetSet( $offset, $value ) {
        if ( ! is_null( $offset ) ) {
            $this->props[ $offset ] = $value;
        }
    }

    public function offsetExists( $offset ) {
        return isset( $this->props[ $offset ] );
    }

    public function offsetUnset( $offset ) {
        unset( $this->props[ $offset ] );
    }

    public function offsetGet( $offset ) {
        return isset( $this->props[ $offset ] ) ? $this->props[ $offset ] : null;
    }

    /**
	 * Helper methods.
	 *
	 * @since 2.2
     */
	public function get_type() {
		return $this->props['type'];
	}

	public function get_key() {
		return $this->key;
	}

	public function get_label() {
		return $this->props['label'];
	}

	public function get_description() {
		return $this->props['description'];
	}

	public function get_placeholder() {
		return $this->props['placeholder'];
	}

	public function is_required() {
		return (bool) $this->props['required'];
	}

	public function get_prop( $prop ) {
		if ( ! isset( $this->props[ $prop ] ) ) {
			return false;
		}

		return $this->props[ $prop ];
	}

	public function get_props() {
		return $this->props;
	}

	public function passes_conditions() {
		$conditions = new \MyListing\Src\Conditions( $this, $this->listing );
		return $conditions->passes();
	}
}
