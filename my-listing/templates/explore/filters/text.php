<?php
/**
 * Template for rendering a `text` filter in Explore page.
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

<text-filter
    listing-type="<?php echo esc_attr( $filter->listing_type->get_slug() ) ?>"
    filter-key="<?php echo esc_attr( $filter->get_form_key() ) ?>"
    location="<?php echo esc_attr( $location ) ?>"
    label="<?php echo esc_attr( $filter->get_label() ) ?>"
    @input="<?php echo esc_attr( $onchange ) ?>"
    inline-template
>
    <div v-if="location === 'primary-filter'" class="explore-head-search">
        <i class="mi search"></i>
        <input required ref="input" type="text" :placeholder="label"
            :value="filters[filterKey]" @input="updateInput">
    </div>
    <div v-else class="form-group explore-filter text-filter md-group">
        <input required ref="input" type="text"
            :value="filters[filterKey]" @input="updateInput">
        <label>{{label}}</label>
    </div>
</text-filter>
