<?php
/**
 * Handle the "Sort By" dropdown in top of search results.
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

<order-filter
    listing-type="<?php echo esc_attr( $filter->listing_type->get_slug() ) ?>"
    filter-key="<?php echo esc_attr( $filter->get_form_key() ) ?>"
    location="<?php echo esc_attr( $location ) ?>"
    label="<?php echo esc_attr( $filter->get_label() ) ?>"
	:choices="<?php echo esc_attr( wp_json_encode( $filter->get_choices() ) ) ?>"
    @input="<?php echo esc_attr( $onchange ) ?>"
	inline-template
>
    <div v-if="location === 'primary-filter'" class="explore-head-search">
        <i class="mi format_list_bulleted"></i>
        <select :value="filters[filterKey]" @select:change="updateInput" ref="select" required>
            <option v-for="choice in choices" :value="choice.key">{{choice.label}}</option>
        </select>
    </div>
	<div v-else class="form-group explore-filter orderby-filter md-group" :class="wrapperClasses">
        <select :value="filters[filterKey]" @select:change="updateInput" ref="select" required>
            <option v-for="choice in choices" :value="choice.key">{{choice.label}}</option>
        </select>
    	<label>{{label}}</label>
    	<div class="orderby-filter-notes"
            v-if="location==='advanced-form' && hasNote(filters[filterKey], 'has-proximity-clause')">
            <p>{{locationDetails}}</p>
    	</div>
	</div>
</order-filter>
