<?php
/**
 * Template for rendering a `dropdown-hierarchy` filter in Explore page.
 *
 * @since 1.0
 *
 * @var $filter
 * @var $location
 * @var $onchange
 */
if ( ! defined('ABSPATH') ) {
    exit;
} ?>

<dropdown-hierarchy-filter
    listing-type="<?php echo esc_attr( $filter->listing_type->get_slug() ) ?>"
    filter-key="<?php echo esc_attr( $filter->get_form_key() ) ?>"
    location="<?php echo esc_attr( $location ) ?>"
    label="<?php echo esc_attr( $filter->get_label() ) ?>"
    pre-selected="<?php echo esc_attr( wp_json_encode( $filter->get_preselected_terms() ) ) ?>"
    ajax-params="<?php echo esc_attr( wp_json_encode( $filter->get_ajax_params() ) ) ?>"
    @input="<?php echo esc_attr( $onchange ) ?>"
    inline-template
>
    <div class="cts-term-hierarchy form-group md-group">
        <input type="text" required ref="input" data-placeholder=" "
            class="hide"
            :data-template="location==='advanced-form'?'default':'alternate'"
            :data-selected="preSelected"
            :data-mylisting-ajax-params="ajaxParams"
            @termhierarchy:change="handleChange"
        >
        <label>{{label}}</label>
    </div>
</dropdown-hierarchy-filter>