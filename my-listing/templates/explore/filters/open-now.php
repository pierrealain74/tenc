<?php
/**
 * Template for rendering a `heading` UI form elemnent in Explore page.
 *
 * @since 2.8
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
	:multiple="false"
	@input="<?php echo esc_attr( $onchange ) ?>"
	inline-template
>
	<div v-if="location==='advanced-form'" class="form-group form-group-tags explore-filter open-now-filter">
		<label>{{label}}</label>
		<div class="range-list" ref="workHourRanges">
			<a
				href="#"
				:class="{active: selected !== '1'}"
				class="single-range"
				@click.prevent="selected = ''; updateInput();"
			><?php echo _x( 'All', 'open now filter', 'my-listing' ) ?></a>
			<a
				href="#"
				:class="{active: selected === '1'}"
				class="single-range"
				@click.prevent="selected = '1'; updateInput();"
			><?php echo _x( 'Open Now', 'open now filter', 'my-listing' ) ?></a>
		</div>
	</div>
    <div v-else class="form-group explore-filter dropdown-filter md-group open-now-filter">
		<select ref="select" required @select:change="selected = ( $event.detail.value === 'open-now' ) ? '1' : ''; updateInput();">
			<option></option>
			<option>
				<?php echo _x( 'All', 'open now filter', 'my-listing' ) ?>
			</option>
			<option value="open-now" :selected="selected === '1'">
				<?php echo _x( 'Open Now', 'open now filter', 'my-listing' ) ?>
			</option>
		</select>
		<label>{{label}}</label>
    </div>
</checkboxes-filter>