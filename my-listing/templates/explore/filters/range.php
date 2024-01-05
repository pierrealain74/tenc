<?php
/**
 * Template for rendering a `range` filter in Explore page.
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

<range-filter
    listing-type="<?php echo esc_attr( $filter->listing_type->get_slug() ) ?>"
    filter-key="<?php echo esc_attr( $filter->get_form_key() ) ?>"
    location="<?php echo esc_attr( $location ) ?>"
    label="<?php echo esc_attr( $filter->get_label() ) ?>"
    value="<?php echo esc_attr( $filter->get_request_value() ) ?>"
    type="<?php echo $filter->get_prop('option_type') === 'simple' ? 'single' : 'range' ?>"
    prefix="<?php echo esc_attr( $filter->get_prop('prefix') ) ?>"
    suffix="<?php echo esc_attr( $filter->get_prop('suffix') ) ?>"
    behavior="<?php echo esc_attr( $filter->get_prop('behavior') ) ?>"
    :min="<?php echo esc_attr( $filter->get_range_min() ) ?>"
    :max="<?php echo esc_attr( $filter->get_range_max() ) ?>"
    :step="<?php echo esc_attr( $filter->get_prop('step') ) ?>"
    :format-value="<?php echo $filter->get_prop('format_value') ? 'true' : 'false' ?>"
    @input="<?php echo esc_attr( $onchange ) ?>"
    inline-template
>
    <div v-if="location === 'primary-filter'" class="explore-head-search form-group radius">
        <i class="mi search"></i>
        <div class="mylisting-range-slider">
            <div class="amount">{{label}}: {{displayValue}}</div>
            <div class="slider-range" ref="slider"></div>
        </div>
    </div>
    <div v-else class="form-group radius radius1 range-slider explore-filter range-filter">
    	<label>{{label}}</label>
        <div class="mylisting-range-slider">
            <div class="amount">{{displayValue}}</div>
            <div class="slider-range" ref="slider"></div>
        </div>
    </div>
</range-filter>
