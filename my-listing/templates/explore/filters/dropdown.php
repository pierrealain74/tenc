<?php
/**
 * Template for rendering a `dropdown` filter in Explore page.
 *
 * @since 1.0
 *
 * @var $filter
 * @var $location
 * @var $onchange
 */
if ( ! defined('ABSPATH') ) {
    exit;
}

$field = $filter->listing_type->get_field( $filter->get_prop('show_field') );
$is_multiple = $location === 'advanced-form' && $filter->get_prop('multiselect');

// make sure field is valid
if ( ! $field ) {
    return;
}

// for term select fields, use special filter templates
if ( $field->get_type() === 'term-select' ) {
    if ( $is_multiple ) {
        return require locate_template( 'templates/explore/filters/dropdown-terms.php' );
    }

    return require locate_template( 'templates/explore/filters/dropdown-hierarchy.php' );
} ?>

<dropdown-filter
    listing-type="<?php echo esc_attr( $filter->listing_type->get_slug() ) ?>"
    filter-key="<?php echo esc_attr( $filter->get_form_key() ) ?>"
    location="<?php echo esc_attr( $location ) ?>"
    label="<?php echo esc_attr( $filter->get_label() ) ?>"
    :multiple="<?php echo $is_multiple ? 'true' : 'false' ?>"
    :choices="<?php echo esc_attr( wp_json_encode( $filter->postmeta_get_choices() ) ) ?>"
    @input="<?php echo esc_attr( $onchange ) ?>"
    inline-template
>
    <div class="form-group explore-filter dropdown-filter md-group"
        :class="multiple ? 'dropdown-filter-multiselect' : ''">
        <select ref="select" required placeholder=" " :multiple="multiple" @select:change="handleChange">
            <option v-if="!multiple"></option>
            <option v-for="choice in choices" :value="choice.value" :selected="isSelected(choice.value)">
                {{choice.label}}
            </option>
        </select>
        <label>{{label}}</label>
    </div>
</dropdown-filter>
