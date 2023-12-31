<?php
/**
 * Template for rendering a `dropdown-terms` filter in Explore page.
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
$is_multiple = $filter->get_prop('multiselect');
?>

<double-checkbox-filter
    listing-type="<?php echo esc_attr( $filter->listing_type->get_slug() ) ?>"
    filter-key="<?php echo esc_attr( $filter->get_form_key() ) ?>"
    location="<?php echo esc_attr( $location ) ?>"
    label="<?php echo esc_attr( $filter->get_label() ) ?>"
    :multiple="<?php echo $is_multiple ? 'true' : 'false' ?>"
    ajax-params="<?php echo esc_attr( wp_json_encode( $filter->get_ajax_params() ) ) ?>"
    :pre-selected="<?php echo esc_attr( wp_json_encode( $filter->get_preselected_terms() ) ) ?>"
    @input="<?php echo esc_attr( $onchange ) ?>"
    inline-template
>
    <div class="form-group explore-filter dropdown-filter-multiselect dropdown-filter md-group dropdown-filter-double-checkbox">
        <div class="main-term">
            <select class="ignore-select" required ref="select" :multiple="multiple" data-placeholder="Rechercher une valeur"
                data-mylisting-ajax="true"
                data-mylisting-ajax-url="mylisting_list_terms"
                :data-mylisting-ajax-params="ajaxParams"
                @select:change="handleChange"
            >
                <option v-for="term in preSelected" :value="term.value" selected>
                    {{term.label}}
                </option>
            </select>
            <label>{{label}}</label>
        </div>
    </div>
</double-checkbox-filter>