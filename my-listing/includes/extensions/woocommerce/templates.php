<?php

namespace MyListing\Ext\WooCommerce;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Templates {
	use \MyListing\Src\Traits\Instantiatable;

	public function __construct() {
		// Wrap sections in custom markup.
		$this->wrap_default_sections();

		// Add custom hooks on every WooCommerce template.
		add_action( 'woocommerce_before_template_part', [ $this, 'before_template' ] );
        add_action( 'woocommerce_after_template_part', [ $this, 'after_template' ] );

		// Add custom template locations.
		add_filter( 'woocommerce_locate_template', [ $this, 'locate_template' ] );
	}

	/**
	 * Locate a WooCommerce template in MyListing.
	 *
	 * @since 1.0
	 */
    public function locate_template( $template ) {
		$aliases = [];
    	$parts = explode( '/templates/', $template );
    	$path = str_replace( 'myaccount/', 'dashboard/', array_pop( $parts ) );

    	// Check if template is present in aliases list.
    	if ( isset( $aliases[ $path ] ) && ( $fullpath = locate_template( $aliases[ $path ] ) ) ) {
    		return $fullpath;
    	}

    	// Check if it exists in templates/
    	if ( $fullpath = locate_template( sprintf( 'templates/%s', $path ) ) ) {
    		return $fullpath;
    	}

    	// Default
    	return $template;
    }

	/**
	 * Generate a 'before' action hook for WooCommerce templates.
	 *
	 * @since 1.0
	 */
	public function before_template( $template ) {
		do_action( sprintf( 'case27_woocommerce_before_template_part_%s', $template ) ); // legacy
		do_action( sprintf( 'mylisting/woocommerce/templates/%s/before', $template ) );
	}

	/**
	 * Generate an 'after' action hook for WooCommerce templates.
	 *
	 * @since 1.0
	 */
	public function after_template( $template ) {
		do_action( sprintf( 'case27_woocommerce_after_template_part_%s', $template ) ); // legacy
		do_action( sprintf( 'mylisting/woocommerce/templates/%s/after', $template ) );
	}

	/**
	 * Wrap WooCommerce default templates in
	 * MyListing content blocks, columns, or section layouts.
	 *
	 * @since 2.0
	 */
	public function wrap_default_sections() {
		$sections = require_once locate_template( 'includes/extensions/woocommerce/layout.config.php' );
		array_map( [ $this, 'wrap_page_in_block' ], $sections['block'] );
		array_map( [ $this, 'wrap_page_in_column' ], $sections['column'] );
		array_map( [ $this, 'wrap_page_in_section' ], $sections['section'] );
		array_map( [ $this, 'wrap_page_in_div' ], $sections['div'] );
	}

	/**
	 * Wrap a WooCommerce page in a MyListing block layout.
	 *
	 * @since 1.0.0
	 * @param array $page {
	 *     An associative array with the page information.
	 *
	 *     @type string $start Action hook to insert the opening block markup.
	 *     @type string $end   Action hook to insert the closing block markup.
	 *     @type string $icon  Block icon to use.
	 *     @type string $title Block title to use.
	 * }
	 */
	public function wrap_page_in_block( $page ) {
		add_action($page['start'], function($args = []) use ($page) {
			if (!is_array($args)) $args = [];
			$page = c27()->merge_options($page, (array) $args);
			?>
			<div class="element">
				<div class="pf-head round-icon">
					<div class="title-style-1">
						<?php echo c27()->get_icon_markup($page['icon']) ?>
						<h5><?php echo esc_html( $page['title'] ) ?></h5>
					</div>
				</div>
				<div class="pf-body">
		<?php });

        add_action($page['end'], function() { ?>
                </div>
            </div>
        <?php });
    }

	/**
	 * Wrap a WooCommerce page in a MyListing column layout.
	 *
	 * @since 1.0.0
	 * @param array $page {
	 *     An associative array with the page information.
	 *
	 *     @type string $start   Action hook to insert the opening block markup.
	 *     @type string $end     Action hook to insert the closing block markup.
	 *     @type string $classes Custom section classes.
	 * }
	 */
	public function wrap_page_in_column( $page ) {
		add_action($page['start'], function($args = []) use ($page) {
			if (!is_array($args)) $args = [];
			$page = c27()->merge_options($page, (array) $args);
			?>
			<div class="container <?php echo esc_attr( $page['classes'] ) ?>">
				<div class="row">
					<div class="col-md-10 col-md-offset-1">
		<?php });

        add_action($page['end'], function() { ?>
                    </div>
                </div>
            </div>
        <?php });
    }

	/**
	 * Wrap a WooCommerce section in a MyListing block layout.
	 *
	 * @since 1.0.0
	 * @param array $page {
	 *     An associative array with the page information.
	 *
	 *     @type string $start   Action hook to insert the opening block markup.
	 *     @type string $end     Action hook to insert the closing block markup.
	 *     @type string $icon    Block icon to use.
	 *     @type string $title   Block title to use.
	 *     @type string $columns Column width.
	 *     @type string $classes Custom section classes.
	 * }
	 */
	public function wrap_page_in_section( $page ) {
		add_action($page['start'], function($args = []) use ($page) {
			if (!is_array($args)) $args = [];
			$page = c27()->merge_options($page, (array) $args);

            if ( empty( $page['columns'] ) ) {
                $page['columns'] = 'col-md-10 col-md-offset-1';
            }

            if ( empty( $page['classes'] ) ) {
                $page['classes'] = 'i-section';
            }
            ?>
            <section class="<?php echo esc_attr( $page['classes'] ) ?>">
                <div class="container">
                    <div class="row section-body">
                        <div class="<?php echo esc_attr( $page['columns'] ) ?>">
                            <div class="element">
                                <div class="pf-head round-icon">
                                    <div class="title-style-1">
                                        <?php echo c27()->get_icon_markup($page['icon']) ?>
                                        <h5><?php echo $page['title'] ? esc_html( $page['title'] ) : get_the_title() ?></h5>
                                    </div>
                                </div>
                                <div class="pf-body">
        <?php });

        add_action($page['end'], function() { ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <?php });
    }

	public function wrap_page_in_div( $page ) {
		add_action( $page['start'], function() use ( $page ) { ?>
			<div class="<?php echo esc_attr( $page['classes'] ) ?>">
		<?php } );

        add_action( $page['end'], function() { ?>
            </div>
        <?php } );
    }
}