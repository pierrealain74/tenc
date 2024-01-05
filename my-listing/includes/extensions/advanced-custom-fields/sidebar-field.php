<?php

namespace MyListing\Ext\Advanced_Custom_Fields;

if ( ! defined('ABSPATH') ) {
    exit;
}

class Sidebar_Field extends \acf_field {
    public $name = 'sidebar';
    public $label = 'Sidebar';
    public $category = 'relational';
    public $defaults = [];

    function __construct() {
        parent::__construct();
    }

    public function render_field_settings( $field ) {
        $options = [];

        foreach ( $GLOBALS['wp_registered_sidebars'] as $sidebar ) {
            $options[ $sidebar['id'] ] = $sidebar['name'];
        }

        if ( empty( $options ) ) {
            $options[] = esc_html__( 'No Sidebar', 'my-listing' );
        }

        acf_render_field_setting( $field, [
            'label'         => esc_html__( 'Sidebar','my-listing' ),
            'instructions'  => esc_html__( 'Select Default sidebar','my-listing' ),
            'type'          => 'select',
            'name'          => 'sidebar_list',
            'choices'       => $options,
            'default_value' => esc_html__( 'No Sidebar', 'my-listing' ),
        ] );
    }

    public function render_field( $field ) {
        $options = [];
        foreach ( $GLOBALS['wp_registered_sidebars'] as $sidebar ) {
            $options[ $sidebar['id'] ] = $sidebar['name'];
        }
        ?>

        <select name="<?php echo esc_attr( $field['name'] ); ?>">
            <option value="<?php echo ( esc_attr( $field['value'] ) == '' ) ? 'selected': ''; ?>"><?php echo esc_html__( 'Select Sidebar', 'my-listing' ); ?></option>
            <?php foreach( $options as $key => $option ): ?>
                <option value="<?php echo esc_attr( $key ); ?>" <?php echo ( esc_attr( $field['value'] ) == $key ) ? "selected" : ''; ?>>
                    <?php echo esc_html( $option ); ?>
                </option>
            <?php endforeach;?>
        </select>
        <?php
    }
}
