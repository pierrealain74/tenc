<?php
/**
 * Template for rendering a `proximity` filter in Explore page.
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

<proximity-filter
    listing-type="<?php echo esc_attr( $filter->listing_type->get_slug() ) ?>"
    filter-key="<?php echo esc_attr( $filter->get_form_key() ) ?>"
    location="<?php echo esc_attr( $location ) ?>"
    label="<?php echo esc_attr( $filter->get_label() ) ?>"
    units="<?php echo $filter->get_prop('units') === 'imperial' ? 'mi' : 'km' ?>"
    :max="<?php echo esc_attr( $filter->get_prop('max') ) ?>"
    :step="<?php echo esc_attr( $filter->get_prop('step') ) ?>"
    :default="<?php echo esc_attr( $filter->get_prop('default') ) ?>"
    @input="<?php echo esc_attr( $onchange ) ?>"
    ref="<?php echo esc_attr( sprintf( '%s_proximity', $filter->listing_type->get_slug() ) ) ?>"
    inline-template
>
    <div class="form-group radius radius1 proximity-slider explore-filter proximity-filter"
        v-show="location==='basic-form'||(filters.lat && filters.lng && filters.search_location)">
        <div class="mylisting-range-slider">
            <div class="amount">{{displayValue}}</div>
            <div class="slider-range" ref="slider"></div>
        </div>
    </div>
</proximity-filter>
