<?php
/**
 * Template for rendering the alternate `date` filter in Explore page.
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

<date-year-filter
    listing-type="<?php echo esc_attr( $filter->listing_type->get_slug() ) ?>"
    filter-key="<?php echo esc_attr( $filter->get_form_key() ) ?>"
    location="<?php echo esc_attr( $location ) ?>"
    label="<?php echo esc_attr( $filter->get_label() ) ?>"
    type="<?php echo $filter->get_prop('option_type') === 'exact' ? 'exact' : 'range' ?>"
    :choices="<?php echo esc_attr( wp_json_encode( $filter->get_postmeta_choices() ) ) ?>"
    :l10n="<?php echo esc_attr( wp_json_encode( [
        'from' => __( 'From...', 'my-listing' ),
        'to' => __( 'To...', 'my-listing' ),
        'pick' => __( 'Choose year...', 'my-listing' ),
    ] ) ) ?>"
    @input="<?php echo esc_attr( $onchange ) ?>"
    inline-template
>
    <div class="form-group explore-filter date-filter dateyear-filter"
        :class="type === 'range' ? 'double-input' : ''">
        <label>{{label}}</label>

        <select ref="startpicker" :placeholder="type==='range'?l10n.from:l10n.pick"
            :value="startDate" @select:change="startDate=$event.detail.value; updateInput();">
            <option></option>
            <option v-for="choice in choices" :value="choice">{{choice}}</option>
        </select>

        <select v-if="type === 'range'" ref="endpicker" :placeholder="l10n.to"
            :value="endDate" @select:change="endDate=$event.detail.value; updateInput();">
            <option></option>
            <option v-for="choice in choices" :value="choice">{{choice}}</option>
        </select>
    </div>
</date-year-filter>
