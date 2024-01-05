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
}

if ( $filter->get_prop('format') === 'year' ) {
    return require locate_template( 'templates/explore/filters/date-year.php' );
} ?>

<date-filter
    listing-type="<?php echo esc_attr( $filter->listing_type->get_slug() ) ?>"
    filter-key="<?php echo esc_attr( $filter->get_form_key() ) ?>"
    location="<?php echo esc_attr( $location ) ?>"
    label="<?php echo esc_attr( $filter->get_label() ) ?>"
    type="<?php echo $filter->get_prop('option_type') === 'exact' ? 'exact' : 'range' ?>"
    :l10n="<?php echo esc_attr( wp_json_encode( [
    	'from' => __( 'From...', 'my-listing' ),
    	'to' => __( 'To...', 'my-listing' ),
    	'pick' => __( 'Pick a date...', 'my-listing' ),
    ] ) ) ?>"
    @input="<?php echo esc_attr( $onchange ) ?>"
    inline-template
>
	<div class="form-group explore-filter datepicker-form-group date-filter"
		:class="type === 'range' ? 'double-input' : ''">
		<label>{{label}}</label>
		<div class="datepicker-wrapper" :class="startDate ? 'active' : ''">
			<input type="text" ref="startpicker" :value="startDate"
				:placeholder="type === 'range' ? l10n.from : l10n.pick"
				@datepicker:change="startDate=$event.detail.value; updateInput();">
		</div>
		<div class="datepicker-wrapper" :class="endDate ? 'active' : ''" v-if="type === 'range'">
			<input type="text" ref="endpicker" :placeholder="l10n.to" :value="endDate"
				@datepicker:change="endDate=$event.detail.value; updateInput();">
		</div>
	</div>
</date-filter>
