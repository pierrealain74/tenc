<?php
/**
 * Base block class which can be extended to construct content
 * blocks for the single listing page.
 *
 * @since 1.0
 */

namespace MyListing\Src\Listing_Types\Content_Blocks;

if ( ! defined('ABSPATH') ) {
	exit;
}

abstract class Base_Block implements \JsonSerializable, \ArrayAccess {
	use Traits\Editor_Markup_Helpers;

	/**
	 * A randomly generated block identifier.
	 *
	 * @since 2.2
	 */
	public $unique_id;

	/**
	 * Listing object which this block belongs to.
	 *
	 * @since 1.0
	 */
	public $listing;

	/**
	 * List of block properties/configuration. Values below are available for
	 * all block types, but there can be additional props for specific block types.
	 *
	 * @since 1.0
	 */
	protected $props = [
		'type' => 'text',
		'title' => '(no label)',
		'icon' => 'mi view_headline',
		'class' => '',
		'id' => '',
	];

	/**
	 * Blocks that require selecting a field as the data source can use this array
	 * to define what field-types the source can be.
	 *
	 * @since 1.0
	 */
	protected $allowed_fields;

	/**
	 * List of CSS classes to wrap the block in in single listing page.
	 *
	 * @since 1.0
	 */
	private $wrapper_classes = [];

	public function __construct( $props = [] ) {
		$this->props();

		// override props if any provided as a parameter
		foreach ( $props as $key => $value ) {
			if ( isset( $this->props[ $key ] ) ) {
				$this->props[ $key ] = $value;
			}

			// convert each option stored in the old format to props
			if ( $key === 'options' ) {
				foreach ( (array) $value as $option ) {
					if ( ! is_array( $option ) || empty( $option['name'] ) ) {
						continue;
					}

					if ( ! ( isset( $this->props[ $option['name'] ] ) && isset( $option['value'] ) ) ) {
						continue;
					}

					$this->props[ $option['name'] ] = $option['value'];
				}
			}
		}
	}

	/**
	 * Get the markup for block settings to be shown in the listing type editor.
	 *
	 * @since 1.0
	 */
	abstract protected function get_editor_options();

	/**
	 * Each block must set the "type" prop to a unique value,
	 * and may add other props if needed.
	 *
	 * @since 1.0
	 */
	abstract protected function props();

	final public function print_options() {
		ob_start(); ?>
		<div class="block-settings-wrapper" v-if="block.type === '<?php echo esc_attr( $this->get_type() ) ?>'">
			<?php $this->get_editor_options() ?>
			<?php $this->getCommonSettings() ?>
		</div>
		<?php return ob_get_clean();
	}

	public function get_props() {
		return $this->props;
	}

	public function get_prop( $prop ) {
		return isset( $this->props[ $prop ] ) ? $this->props[ $prop ] : null;
	}

	public function get_type() {
		return $this->get_prop('type');
	}

	public function get_title() {
		return $this->get_prop('title');
	}

	public function get_icon() {
		return $this->get_prop('icon');
	}

	public function get_wrapper_id() {
		return ! empty( $this->get_prop('id') ) ? $this->get_prop('id') : $this->get_unique_id();
	}

	public function get_wrapper_classes() {
		$this->wrapper_classes[] = 'block-type-'.$this->get_type();

		if ( $show_field = $this->get_prop('show_field') ) {
			$this->wrapper_classes[] = 'block-field-'.$show_field;
		}

		if ( $custom_classes = $this->get_prop('class') ) {
			$this->wrapper_classes[] = $custom_classes;
		}

		return join( ' ', $this->wrapper_classes );
	}

	public function add_wrapper_classes( $classes ) {
		if ( is_array( $classes ) ) {
			$classes = join( ' ', $classes );
		}

		$this->wrapper_classes[] = $classes;
	}

	public function jsonSerialize() {
		return $this->props;
	}

	/**
	 * Implements \ArrayAccess interface to keep compatibility with older
	 * snippets where content blocks were associative arrays.
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

    public function get_unique_id() {
    	if ( ! empty( $this->unique_id ) ) {
    		return $this->unique_id;
    	}

    	$this->unique_id = 'block_'.\MyListing\Utils\Random_Id::generate(7);
    	return $this->unique_id;
    }

	/**
	 * Set the listing for this block.
	 *
	 * @since 2.4
	 */
	public function set_listing( $listing ) {
		$this->listing = $listing;
	}
}
