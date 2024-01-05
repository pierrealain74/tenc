<?php
/**
 * Template for rendering a `location` filter in Explore page.
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

<location-filter
    listing-type="<?php echo esc_attr( $filter->listing_type->get_slug() ) ?>"
    filter-key="<?php echo esc_attr( $filter->get_form_key() ) ?>"
    location="<?php echo esc_attr( $location ) ?>"
    label="<?php echo esc_attr( $filter->get_label() ) ?>"
    @input="<?php echo esc_attr( $onchange ) ?>"
    inline-template
>
    <div v-if="location === 'primary-filter'" class="explore-head-search ehs-location">
        <i class="mi search"></i>
        <input required ref="input" type="text" :placeholder="label" :value="filters[filterKey]"
			@autocomplete:change="handleAutocomplete">
    	<i class="icon-location-user geocode-location" @click="updateWithUserLocation"></i>
    </div>
    <div v-else class="form-group location-wrapper explore-filter location-filter md-group">
        <input required ref="input" type="text" :placeholder="label" :value="filters[filterKey]"
			@autocomplete:change="handleAutocomplete">
    	<i class="icon-location-user geocode-location" @click="updateWithUserLocation"></i>
    </div>
</location-filter>
