<?php
/**
 * Template for rendering a `related-listing` filter in Explore page.
 *
 * @since 2.2
 *
 * @var $filter
 * @var $location
 * @var $onchange
 */
if ( ! defined('ABSPATH') ) {
    exit;
} ?>

<related-listing-filter
    listing-type="<?php echo esc_attr( $filter->listing_type->get_slug() ) ?>"
    filter-key="<?php echo esc_attr( $filter->get_form_key() ) ?>"
    location="<?php echo esc_attr( $location ) ?>"
    label="<?php echo esc_attr( $filter->get_label() ) ?>"
    ajax-params="<?php echo esc_attr( wp_json_encode( $filter->get_ajax_params() ) ) ?>"
    :pre-selected="<?php echo esc_attr( wp_json_encode( $filter->get_preselected_terms() ) ) ?>"
    :multiple="<?php echo $location === 'advanced-form' && $filter->get_prop('multiselect') ? 'true' : 'false' ?>"
    @input="<?php echo esc_attr( $onchange ) ?>"
    inline-template
>
    <div class="form-group explore-filter related-listing-filter md-group">
		<select ref="select" required placeholder=" " :multiple="multiple" @select:change="handleChange">
	        <option v-for="term in preSelected" :value="term.value" selected>
	            {{term.label}}
	        </option>
		</select>
        <label>{{label}}</label>
    </div>
</related-listing-filter>
