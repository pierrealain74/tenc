<?php
/**
 * Template for rendering a `checkboxes` filter in Explore page.
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

<checkboxes-filter
    listing-type="<?php echo esc_attr( $filter->listing_type->get_slug() ) ?>"
    filter-key="<?php echo esc_attr( $filter->get_form_key() ) ?>"
    location="<?php echo esc_attr( $location ) ?>"
    label="<?php echo esc_attr( $filter->get_label() ) ?>"
	:choices="<?php echo esc_attr( wp_json_encode( $filter->get_choices() ) ) ?>"
    :multiple="<?php echo $filter->get_prop('multiselect') ? 'true' : 'false' ?>"
    @input="<?php echo esc_attr( $onchange ) ?>"
    inline-template
>
    <div class="form-group form-group-tags explore-filter checkboxes-filter">
		<label>{{label}}</label>
		<ul class="tags-nav">
			<li v-for="choice, key in choices">
				<div class="md-checkbox">
					<input :id="filterId+key" :type="multiple ? 'checkbox' : 'radio'"
						:value="choice.value" v-model="selected" @change="updateInput">
					<label :for="filterId+key">{{choice.label}}</label>
				</div>
			</li>
		</ul>
    </div>
</checkboxes-filter>
