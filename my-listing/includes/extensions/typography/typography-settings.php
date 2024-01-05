<?php

namespace MyListing\Ext\Typography;

if ( ! defined('ABSPATH') ) {
	exit;
}

return [


    'header' => [
        'label' => 'Header',
        'settings' => [
            'divide_header_search' => [
                'type' => 'divider',
                'label' => 'Quick search placeholder',
            ],

            'header_search_size' => [
                'label' => 'Font size',
                'selector' => '.header-search > input[type=search], div.search-shortcode.header-search input[type=search]',
                'option' => 'options_header-search-placeholder-options_font-size',
                'type' => 'font-size',
            ],

            'header_search_weight' => [
                'label' => 'Font weight',
                'selector' => '.header-search > input[type=search], header.header-light-skin:not(.header-scroll) .header-search input[type=search], header.header.header-scroll.header-scroll-light-skin .header-search input[type=search], div.search-shortcode.header-search input[type=search]',
                'option' => 'options_header-search-placeholder-options_font-weight',
                'type' => 'font-weight',
            ],

            'divide_quick_results' => [
                'type' => 'divider',
                'label' => 'Quick search results',
            ],

            'quick_search_font_size' => [
                'label' => 'Font size',
                'selector' => '.instant-results ul.instant-results-list li a span.category-name, .instant-results ul.instant-results-list li a span.category-name',
                'type' => 'font-size',
            ],

            'quick_search_font_weight' => [
                'label' => 'Font weight',
                'selector' => '.instant-results ul.instant-results-list li a span.category-name, .instant-results ul.instant-results-list li a span.category-name',
                'type' => 'font-weight',
            ],

            'divide_quick_section_title' => [
                'type' => 'divider',
                'label' => 'Quick search section label',
            ],

            'quick_search_section_title_size' => [
                'label' => 'Font size',
                'selector' => '.instant-results ul.instant-results-list li.ir-cat',
                'type' => 'font-size',
            ],

            'quick_search_section_title_weight' => [
                'label' => 'Font weight',
                'selector' => '.instant-results ul.instant-results-list li.ir-cat',
                'type' => 'font-weight',
            ],

            'divide_quick_view_results' => [
                'type' => 'divider',
                'label' => 'Quick search view results button',
            ],

            'quick_search_view_results_size' => [
                'label' => 'Font size',
                'selector' => '.instant-results button.view-all-results',
                'type' => 'font-size',
            ],

            'quick_search_view_results_weight' => [
                'label' => 'Font weight',
                'selector' => '.instant-results button.view-all-results',
                'type' => 'font-weight',
            ],

            'divide_header_menu' => [
                'type' => 'divider',
                'label' => 'Main menu',
            ],

            'header_menu_size' => [
                'label' => 'Font size',
                'selector' => 'div.i-nav > ul > li',
                'option' => 'options_menu-item-options_font-size',
                'type' => 'font-size',
            ],

            'header_menu_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.i-nav > ul > li',
                'option' => 'options_menu-item-options_font-weight',
                'type' => 'font-weight',
            ],

            'divide_header_dropdown_menu' => [
                'type' => 'divider',
                'label' => 'Dropdown menu',
            ],

            'header_dropdown_menu_size' => [
                'label' => 'Font size',
                'selector' => 'ul.sub-menu.i-dropdown li a, ul.sub-menu.i-dropdown li a, div.mobile-user-menu > ul > li > a,
                ul.i-dropdown li a',
                'type' => 'font-size',
            ],

            'header_dropdown_menu_weight' => [
                'label' => 'Font weight',
                'selector' => 'ul.sub-menu.i-dropdown li a, ul.sub-menu.i-dropdown li a, div.mobile-user-menu > ul > li > a,
                ul.i-dropdown li a',
                'type' => 'font-weight',
            ],

            'divide_user_area' => [
                'type' => 'divider',
                'label' => 'User area',
            ],

            'header_auth_size' => [
                'label' => 'Font size',
                'selector' => 'div.user-area .user-profile-name, header.header .user-area > a, div.user-profile-dropdown .user-profile-name',
                'option' => 'options_signin-register-options_font-size',
                'type' => 'font-size',
            ],

            'header_auth_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.user-area .user-profile-name, header.header .user-area > a, div.user-profile-dropdown .user-profile-name',
                'option' => 'options_signin-register-options_font-weight',
                'type' => 'font-weight',
            ],

            'divide_cta_header_button' => [
                'type' => 'divider',
                'label' => 'Call to action button',
            ],

            'header_cta_size' => [
                'label' => 'Font size',
                'selector' => '	div.header-right .header-button a.buttons',
                'option' => 'options_header-action-button-options_font-size',
                'type' => 'font-size',
            ],

            'header_cta_weight' => [
                'label' => 'Font weight',
                'selector' => '	div.header-right .header-button a.buttons',
                'option' => 'options_header-action-button-options_font-weight',
                'type' => 'font-weight',
            ],
        ],
    ],

    'single-listing' => [
        'label' => 'Single listing',
        'settings' => [
            'divide_single_listing_editor_paragraph' => [
                'type' => 'divider',
                'label' => 'Paragraphs',
            ],

            'single_listing_editor_paragraph_size' => [
                'label' => 'Font size',
                'selector' => 'div.wp-editor-content p, div.wp-editor-content ul li,
                div.wp-editor-content ol li, div.plain-text-content .pf-body p',
                'type' => 'font-size',
            ],

            'single_listing_editor_paragraph_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.wp-editor-content p, div.wp-editor-content ul li,
                div.wp-editor-content ol li, div.plain-text-content .pf-body p',
                'type' => 'font-weight',
            ],

            'single_listing_editor_paragraph_lineheight' => [
                'label' => 'Line height',
                'selector' => 'div.wp-editor-content p, div.wp-editor-content ul li,
                div.wp-editor-content ol li, div.plain-text-content .pf-body p',
                'type' => 'line-height',
            ],

            'single_listing_editor_paragraph_color' => [
                'label' => 'Color',
                'selector' => 'div.wp-editor-content p, div.wp-editor-content ul li,
                div.wp-editor-content ol li, div.plain-text-content .pf-body p, div.wp-editor-content a,
                div.wp-editor-content h1, div.wp-editor-content h2, div.wp-editor-content h3, div.wp-editor-content h4,
                div.wp-editor-content h5, div.wp-editor-content h6',
                'type' => 'color',
            ],

            'divide_single_listing_title' => [
                'type' => 'divider',
                'label' => 'Listing title',
            ],

            'listing_title_size' => [
                'label' => 'Font size',
                'selector' => 'div.profile-name h1',
                'option' => 'options_listing-name-options_font-size',
                'type' => 'font-size',
            ],

            'listing_title_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.profile-name h1',
                'option' => 'options_listing-name-options_font-weight',
                'type' => 'font-weight',
            ],

            'listing_title_color' => [
                'label' => 'Color',
                'selector' => 'div.profile-name h1',
                'type' => 'color',
            ],

            'divide_single_listing_tagline' => [
                'type' => 'divider',
                'label' => 'Tagline',
            ],


            'listing_tagline_size' => [
                'label' => 'Font size',
                'selector' => 'div.profile-name h2',
                'option' => 'options_listing-tagline-options_font-size',
                'type' => 'font-size',
            ],

            'listing_tagline_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.profile-name h2',
                'option' => 'options_listing-tagline-options_font-weight',
                'type' => 'font-weight',
            ],

            'listing_tagline_color' => [
                'label' => 'Color',
                'selector' => 'div.profile-name h2',
                'type' => 'color',
            ],

            'divide_single_listing_cta' => [
                'type' => 'divider',
                'label' => 'Call to action button',
            ],

            'listing_cta_size' => [
                'label' => 'Font size',
                'selector' => 'li.lmb-calltoaction > a',
                'option' => 'options_call-to-action-cover-button-options_font-size',
                'type' => 'font-size',
            ],

            'listing_cta_weight' => [
                'label' => 'Font weight',
                'selector' => 'li.lmb-calltoaction > a',
                'option' => 'options_call-to-action-cover-button-options_font-weight',
                'type' => 'font-weight',
            ],

            'divide_single_listing_cover_detail' => [
                'type' => 'divider',
                'label' => 'Cover detail',
            ],

            'listing_cover_detail_size' => [
                'label' => 'Font size',
                'selector' => 'li.price-or-date .value',
                'type' => 'font-size',
            ],

            'listing_cover_detail_weight' => [
                'label' => 'Font weight',
                'selector' => 'li.price-or-date .value',
                'type' => 'font-weight',
            ],

            'listing_cover_detail_color' => [
                'label' => 'Color',
                'selector' => 'li.price-or-date .value',
                'type' => 'color',
            ],

            'divide_single_listing_menu' => [
                'type' => 'divider',
                'label' => 'Listing menu',
            ],

            'listing_menu_size' => [
                'label' => 'Font size',
                'selector' => 'div.profile-header .profile-menu ul li a, div.profile-header .profile-menu ul li a.listing-tab-toggle',
                'option' => 'options_listing-menu-options_font-size',
                'type' => 'font-size',
            ],

            'listing_menu_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.profile-header .profile-menu ul li a, div.profile-header .profile-menu ul li a.listing-tab-toggle',
                'option' => 'options_listing-menu-options_font-weight',
                'type' => 'font-weight',
            ],

            'listing_menu_color' => [
                'label' => 'Color',
                'selector' => 'div.profile-header .profile-menu ul li a, div.profile-header .profile-menu ul li a.listing-tab-toggle',
                'type' => 'color',
            ],

            'divide_single_listing_quick_action' => [
                'type' => 'divider',
                'label' => 'Quick action title',
            ],

            'listing_qa_size' => [
                'label' => 'Font size',
                'selector' => 'div.quick-listing-actions > ul >li >a',
                'option' => 'options_quick-action-options_font-size',
                'type' => 'font-size',
            ],

            'listing_qa_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.quick-listing-actions > ul >li >a',
                'option' => 'options_quick-action-options_font-weight',
                'type' => 'font-weight',
            ],

            'listing_qa_text_color' => [
                'label' => 'Text color',
                'selector' => 'div.quick-listing-actions > ul >li >a span',
                'type' => 'color',
            ],

            'listing_qa_icon_color' => [
                'label' => 'Icon color',
                'selector' => 'div.quick-listing-actions > ul >li >a > i',
                'type' => 'color',
            ],

            'divide_single_listing_block_title' => [
                'type' => 'divider',
                'label' => 'Block title',
            ],

            'listing_block_title_size' => [
                'label' => 'Font size',
                'selector' => 'div.listing-tabs .title-style-1 h5',
                'option' => 'options_block-title-options_font-size',
                'type' => 'font-size',
            ],

            'listing_block_title_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.listing-tabs .title-style-1 h5',
                'option' => 'options_block-title-options_font-weight',
                'type' => 'font-weight',
            ],

            'listing_block_icon_color' => [
                'label' => 'Icon color',
                'selector' => 'body.single-listing div.title-style-1 i',
                'option' => 'options_single_listing_content_block_icon_color',
                'type' => 'color',
            ],

            'listing_block_title_color' => [
                'label' => 'Text color',
                'selector' => 'div.listing-tabs .title-style-1 h5',
                'type' => 'color',
            ],

            'divide_single_listing_term_title' => [
                'type' => 'divider',
                'label' => 'Term title (Category, tag, term block)',
            ],

            'listing_term_size' => [
                'label' => 'Font size',
                'selector' => '#c27-single-listing .element .pf-body div.listing-details li span.category-name, div.block-type-tags .element .social-nav li span, div.block-type-terms .element .social-nav li span',
                'option' => 'options_category-term-block-tag-options_font-size',
                'type' => 'font-size',
            ],

            'listing_term_weight' => [
                'label' => 'Font weight',
                'selector' => '#c27-single-listing .element .pf-body div.listing-details li span.category-name, div.block-type-tags .element .social-nav li span, div.block-type-terms .element .social-nav li span',
                'option' => 'options_category-term-block-tag-options_font-weight',
                'type' => 'font-weight',
            ],

            'listing_term_color' => [
                'label' => 'Color',
                'selector' => '#c27-single-listing .element .pf-body div.listing-details li span.category-name, div.block-type-tags .element .social-nav li span, div.block-type-terms .element .social-nav li span',
                'type' => 'color',
            ],

            'divide_single_listing_social_title' => [
                'type' => 'divider',
                'label' => 'Social networks title',
            ],

            'listing_socnet_size' => [
                'label' => 'Font size',
                'selector' => 'div.block-type-social_networks .element .social-nav li span',
                'option' => 'options_social-network-block-options_font-size',
                'type' => 'font-size',
            ],

            'listing_socnet_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.block-type-social_networks .element .social-nav li span',
                'option' => 'options_social-network-block-options_font-weight',
                'type' => 'font-weight',
            ],

            'listing_socnet_color' => [
                'label' => 'Color',
                'selector' => 'div.block-type-social_networks .element .social-nav li span',
                'type' => 'color',
            ],

            'divide_single_listing_table_font' => [
                'type' => 'divider',
                'label' => 'Table text',
            ],

            'listing_table_label_size' => [
                'label' => 'Font size',
                'selector' => 'div.table-block .extra-details .item-attr, ul.extra-details .item-property, ul.extra-details .item-property div',
                'option' => 'options_table_label_options_font-size',
                'type' => 'font-size',
            ],

            'listing_table_label_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.table-block .extra-details .item-attr, ul.extra-details .item-property',
                'option' => 'options_table_label_options_font-weight',
                'type' => 'font-weight',
            ],

            'listing_table_label_color' => [
                'label' => 'Color',
                'selector' => 'div.table-block .extra-details .item-attr, ul.extra-details .item-property',
                'type' => 'color',
            ],

            'divide_single_listing_author_related' => [
                'type' => 'divider',
                'label' => 'Related listing / Author / File title',
            ],

            'listing_single_related_author_block_size' => [
                'label' => 'Font size',
                'selector' => 'div.related-listing-block .event-host .host-name, div.files-block .file-name',
                'type' => 'font-size',
            ],

            'listing_single_related_author_block_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.related-listing-block .event-host .host-name, div.files-block .file-name',
                'type' => 'font-weight',
            ],

            'listing_single_related_author_block_color' => [
                'label' => 'Color',
                'selector' => 'div.related-listing-block .event-host .host-name, div.files-block .file-name',
                'type' => 'color',
            ],

            'divide_single_listing_tab_accordion' => [
                'type' => 'divider',
                'label' => 'Tab / Accordion title',
            ],

            'listing_single_tab_acc_size' => [
                'label' => 'Font size',
                'selector' => 'div.block-type-accordion .panel-title a, div.block-type-tabs .bl-tabs .nav-tabs>li>a',
                'type' => 'font-size',
            ],

            'listing_single_tab_acc_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.block-type-accordion .panel-title a, div.block-type-tabs .bl-tabs .nav-tabs>li>a',
                'type' => 'font-weight',
            ],

            'listing_single_tab_acc_color' => [
                'label' => 'Font color',
                'selector' => 'div.block-type-accordion .panel-title a, div.block-type-tabs .bl-tabs .nav-tabs>li>a',
                'type' => 'color',
            ],
        ],
    ],

    'search_forms' => [
        'label' => 'Search Forms',
        'settings' => [

            'divide_search_form_labels' => [
                'type' => 'divider',
                'label' => 'Basic and advanced form filter labels',
            ],

            'search_label_size' => [
                'label' => 'Font size (Recommmended values: 12 to 16)',
                'selector' => 'div.finder-search .form-group input, div.featured-search .form-group label, div.finder-search .form-group label, div.cts-term-hierarchy.form-group.md-group .go-back-btn,div.md-group input:focus ~ label,
                div.featured-search .radius .amount, div.radius .amount, div.featured-search input, div.featured-search
                .form-group .select2-container--default .select2-selection--single .select2-selection__rendered,
                div.finder-search .form-group .select2-container--default .select2-selection--single .select2-selection__rendered, div.featured-search .radius.proximity-slider .amount, div.finder-search .select2-container .select2-search--inline .select2-search__field, div.finder-search .select2-container--default .select2-selection--multiple .select2-selection__choice, div.pac-container .pac-item, ul.tags-nav li .md-checkbox label, div#finderSearch .datepicker-wrapper input',
                'type' => 'font-size',
            ],

            'search_label_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.finder-search .form-group input, div.featured-search .form-group label, div.finder-search .form-group label,
                div.cts-term-hierarchy.form-group.md-group .go-back-btn, div.md-group input:focus ~ label, div.featured-search, div.featured-search .radius.proximity-slider .amount, div.radius .amount,
                div.featured-search input, div.featured-search
                .form-group .select2-container--default .select2-selection--single .select2-selection__rendered,
                div.finder-search .form-group .select2-container--default .select2-selection--single .select2-selection__rendered, div.finder-search .select2-container .select2-search--inline .select2-search__field, div.finder-search .select2-container--default .select2-selection--multiple .select2-selection__choice,div.pac-container .pac-item,
                ul.tags-nav li .md-checkbox label, div#finderSearch .datepicker-wrapper input',
                'type' => 'font-weight',
            ],

            'divide_basic_tab' => [
                'type' => 'divider',
                'label' => 'Basic form tabs',
            ],

            'search_basic_tab_size' => [
                'label' => 'Font size',
                'selector' => 'div.fs-tabs .nav-tabs>li>a, div.transparent .fs-tabs .nav-tabs>li>a',
                'option' => 'options_search-tab-options_font-size',
                'type' => 'font-size',
            ],

            'search_basic_tab_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.fs-tabs .nav-tabs>li>a',
                'option' => 'options_search-tab-options_font-weight',
                'type' => 'font-weight',
            ],

            'divide_basic_search_button' => [
                'type' => 'divider',
                'label' => 'Basic form search button',
            ],

            'search_basic_btn_size' => [
                'label' => 'Font size',
                'selector' => 'div.featured-search .search',
                'option' => 'options_search-button-options_font-size',
                'type' => 'font-size',
            ],

            'search_basic_btn_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.featured-search .search',
                'option' => 'options_search-button-options_font-weight',
                'type' => 'font-weight',
            ],

            'divide_select2_dropdown_fonts' => [
                'type' => 'divider',
                'label' => 'Dropdown filter (Affects all select2 dropdowns throughout the site)',
            ],

            'select2_dropdown_font_size' => [
                'label' => 'Font size',
                'selector' => 'li.select2-results__option',
                'type' => 'font-size',
            ],

            'select2_dropdown_font_weight' => [
                'label' => 'Font weight',
                'selector' => 'li.select2-results__option',
                'type' => 'font-weight',
            ],

        ],
    ],

    'preview_card' => [
        'label' => 'Preview Card',
        'settings' => [

            'divide_default_template_title' => [
                'type' => 'divider',
                'label' => 'Default template title',
            ],

            'prevcard_title_size' => [
                'label' => 'Font size',
                'selector' => 'div.lf-item-default .lf-item-info > h4',
                'option' => 'options_default-template-listing-title-options_font-size',
                'type' => 'font-size',
            ],

            'prevcard_title_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.lf-item-default .lf-item-info > h4',
                'option' => 'options_default-template-listing-title-options_font-weight',
                'type' => 'font-weight',
            ],

            'prevcard_title_color' => [
                'label' => 'Color',
                'selector' => 'div.lf-item-default .lf-item-info > h4',
                'type' => 'color',
            ],

            'divide_alternate_template_title' => [
                'type' => 'divider',
                'label' => 'Alternate template title',
            ],

            'prevcard_alt_title_size' => [
                'label' => 'Font size',
                'selector' => 'div.lf-item-alternate .lf-item-info-2 h4',
                'option' => 'options_alternate_template_listing_title_options_font-size',
                'type' => 'font-size',
            ],

            'prevcard_alt_title_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.lf-item-alternate .lf-item-info-2 h4',
                'option' => 'options_alternate_template_listing_title_options_font-weight',
                'type' => 'font-weight',
            ],

            'prevcard_alt_title_color' => [
                'label' => 'Color',
                'selector' => 'div.lf-item-alternate .lf-item-info-2 h4',
                'type' => 'color',
            ],

            'divide_alternate_tagline' => [
                'type' => 'divider',
                'label' => 'Tagline (Alternate template)',
            ],

            'tagline_alternate_font_size' => [
                'label' => 'Font size',
                'selector' => 'div.lf-item-info-2 h6',
                'type' => 'font-size',
            ],

            'tagline_alternate_font_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.lf-item-info-2 h6',
                'type' => 'font-weight',
            ],

            'tagline_alternate_font_color' => [
                'label' => 'Font color',
                'selector' => 'div.lf-item-info-2 h6',
                'type' => 'color',
            ],


            'divide_list_template_title' => [
                'type' => 'divider',
                'label' => 'List template title',
            ],

            'prevcard_list_title_size' => [
                'label' => 'Font size',
                'selector' => 'div.lf-item.lf-item-list-view .lf-item-info > h4',
                'option' => 'options_list-template-listing-title-options_font-size',
                'type' => 'font-size',
            ],

            'prevcard_list_title_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.lf-item.lf-item-list-view .lf-item-info > h4',
                'option' => 'options_list-template-listing-title-options_font-weight',
                'type' => 'font-weight',
            ],

            'prevcard_list_title_color' => [
                'label' => 'Color',
                'selector' => 'div.lf-item.lf-item-list-view .lf-item-info > h4',
                'type' => 'color',
            ],

            'divide_quick_view_title' => [
                'type' => 'divider',
                'label' => 'Quick view title',
            ],

            'quickview_title_size' => [
                'label' => 'Font size',
                'selector' => 'div.quick-view-modal .lf-item-info h4',
                'option' => 'options_quick-view-title-options_font-size',
                'type' => 'font-size',
            ],

            'quickview_title_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.quick-view-modal .lf-item-info h4',
                'option' => 'options_quick-view-title-options_font-weight',
                'type' => 'font-weight',
            ],

            'divide_fields_below_title' => [
                'type' => 'divider',
                'label' => 'Fields below the title',
            ],

            'fields_below_title_font_size' => [
                'label' => 'Font size',
                'selector' => 'div.lf-item-info > ul li, div.lf-item-info-2 > ul.lf-contact li',
                'type' => 'font-size',
            ],

            'fields_below_title_font_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.lf-item-info > ul li, div.lf-item-info-2 > ul.lf-contact li',
                'type' => 'font-weight',
            ],

            'divide_preview_term_title' => [
                'type' => 'divider',
                'label' => 'Term title (Category, Region, Tags etc.)',
            ],

            'preview_card_term_title_fontsize' => [
                'label' => 'Font size',
                'selector' => 'div.c27-footer-section.listing-details .category-name,
                .listing-quick-view-container .element .listing-details li .category-name',
                'type' => 'font-size',
            ],

            'preview_card_term_title_fontweight' => [
                'label' => 'Font weight',
                'selector' => 'div.c27-footer-section.listing-details .category-name,
                .listing-quick-view-container .element .listing-details li .category-name',
                'type' => 'font-weight',
            ],

            'divide_preview_head_buttons' => [
                'type' => 'divider',
                'label' => 'Head buttons',
            ],

            'preview_card_head_buttons_fontsize' => [
                'label' => 'Font size',
                'selector' => 'div.lf-head-btn, div.lf-head .event-date span.e-month,
                div.lf-head .event-date span.e-day, div.listing-preview .lf-head-btn.formatted .rent-price span.value,
                div.listing-preview .lf-head-btn.formatted .rent-price sup.out-of, div.rating-preview-card i',
                'type' => 'font-size',
            ],

            'preview_card_head_buttons_fontweight' => [
                'label' => 'Font weight',
                'selector' => 'div.lf-head-btn, div.lf-head .event-date span.e-month,
                div.lf-head .event-date span.e-day, div.listing-preview .lf-head-btn.formatted .rent-price span.value,
                div.listing-preview .lf-head-btn.formatted .rent-price sup.out-of, div.rating-preview-card i',
                'type' => 'font-weight',
            ],

            'divide_preview_card_table_details' => [
                'type' => 'divider',
                'label' => 'Details table',
            ],

            'preview_card_details_table_fontsize' => [
                'label' => 'Font size',
                'selector' => 'div.listing-details-3 .details-list li span',
                'type' => 'font-size',
            ],

            'preview_card_details_table_fontweight' => [
                'label' => 'Font weight',
                'selector' => 'div.listing-details-3 .details-list li span',
                'type' => 'font-weight',
            ],

            'divide_preview_card_related_listing' => [
                'type' => 'divider',
                'label' => 'Author/related listing title',
            ],

            'preview_card_author_related_listings_fontsize' => [
                'label' => 'Font size',
                'selector' => 'div.lf-item-container .event-host .host-name',
                'type' => 'font-size',
            ],

            'preview_card_author_related_listings_fontweight' => [
                'label' => 'Font weight',
                'selector' => 'div.lf-item-container .event-host .host-name',
                'type' => 'font-weight',
            ],
        ],
    ],

    'explore' => [
        'label' => 'Explore page',
        'settings' => [

            'divide_explore_page_title' => [
                'type' => 'divider',
                'label' => 'Explore page title',
            ],

            'explore_title_size' => [
                'label' => 'Font size',
                'selector' => 'div.explore-head .explore-types .finder-title h2',
                'option' => 'options_explore-listing-type-menu-options_font-size',
                'type' => 'font-size',
            ],

            'explore_title_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.explore-head .explore-types .finder-title h2',
                'option' => 'options_explore-listing-type-menu-options_font-weight',
                'type' => 'font-weight',
            ],

            'divide_explore_page_types_title' => [
                'type' => 'divider',
                'label' => 'Listing type titles (navbar)',
            ],

            'explore_types_size' => [
                'label' => 'Font size',
                'selector' => 'div.explore-head .explore-types .type-info > h4',
                'option' => 'options_explore-listing-type-menu-heading-options_font-size',
                'type' => 'font-size',
            ],

            'explore_types_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.explore-head .explore-types .type-info > h4',
                'option' => 'options_explore-listing-type-menu-heading-options_font-weight',
                'type' => 'font-weight',
            ],

            'divide_explore_page_tabs_title' => [
                'type' => 'divider',
                'label' => 'Tab title (left sidebar)',
            ],

            'explore_tabs_size' => [
                'label' => 'Font size',
                'selector' => 'div.finder-tabs .sidebar-tabs li a',
                'type' => 'font-size',
            ],
            'explore_tabs_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.finder-tabs .sidebar-tabs li a',
                'type' => 'font-weight',
            ],

            'divide_explore_page_search_btn' => [
                'type' => 'divider',
                'label' => 'Search button',
            ],

            'explore_search_btn_size' => [
                'label' => 'Font size',
                'selector' => 'div.finder-search .tab-content .form-group .button-2',
                'type' => 'font-size',
            ],
            'explore_search_btn_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.finder-search .tab-content .form-group .button-2',
                'type' => 'font-weight',
            ],

            'divide_explore_page_reset_btn' => [
                'type' => 'divider',
                'label' => 'Reset filters',
            ],

            'explore_reset_btn_size' => [
                'label' => 'Font size',
                'selector' => 'a.reset-results-27',
                'type' => 'font-size',
            ],

            'explore_reset_btn_weight' => [
                'label' => 'Font weight',
                'selector' => 'a.reset-results-27',
                'type' => 'font-weight',
            ],

            'divide_explore_page_top_details' => [
                'type' => 'divider',
                'label' => 'Details at the top of the results (listing order, number of results etc.)',
            ],

            'explore_top_details_size' => [
                'label' => 'Font size',
                'selector' => 'span.fl-results-no span, div.fl-head a, div.fl-head p',
                'type' => 'font-size',
            ],
            'explore_top_details_weight' => [
                'label' => 'Font weight',
                'selector' => 'span.fl-results-no span, div.fl-head a, div.fl-head p',
                'type' => 'font-weight',
            ],

            'divide_explore_page_term_title' => [
                'type' => 'divider',
                'label' => 'Term tab: Term title',
            ],

            'explore_term_tab_title_size' => [
                'label' => 'Font size',
                'selector' => 'div.finder-search .lc-info h4, div.active-taxonomy-container h1.category-name',
                'type' => 'font-size',
            ],

            'explore_term_tab_title_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.finder-search .lc-info h4, div.active-taxonomy-container h1.category-name',
                'type' => 'font-weight',
            ],

            'divide_explore_page_term_description' => [
                'type' => 'divider',
                'label' => 'Term tab: Term description',
            ],

            'explore_term_tab_description_size' => [
                'label' => 'Font size',
                'selector' => 'div.active-taxonomy-container .category-description',
                'type' => 'font-size',
            ],

            'explore_term_tab_description_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.active-taxonomy-container .category-description',
                'type' => 'font-weight',
            ],

            'explore_term_tab_description_line_height' => [
                'label' => 'Line height',
                'selector' => 'div.active-taxonomy-container .category-description',
                'type' => 'line-height',
            ],

        ],
    ],

    'add-listing' => [
        'label' => 'Add Listing Page',
        'settings' => [

            'divide_addlisting_card_title' => [
                'type' => 'divider',
                'label' => 'Choose listing type card title',
            ],

            'add_listing_card_title_size' => [
                'label' => 'Font size',
                'selector' => 'div.elementor-widget-case27-add-listing-widget .ac-front-side .category-name',
                'type' => 'font-size',
            ],

            'add_listing_card_title_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.elementor-widget-case27-add-listing-widget .ac-front-side .category-name',
                'type' => 'font-weight',
            ],

            'divide_addlisting_step_title' => [
                'type' => 'divider',
                'label' => 'Inner step title',
            ],

            'add_listing_step_title_size' => [
                'label' => 'Font size',
                'selector' => 'div.add-listing-step .section-title h2',
                'type' => 'font-size',
            ],

            'add_listing_step_title_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.add-listing-step .section-title h2',
                'type' => 'font-weight',
            ],

            'divide_addlisting_nav_size' => [
                'type' => 'divider',
                'label' => 'Add listing form navigation',
            ],

            'addlist_nav_size' => [
                'label' => 'Font size',
                'selector' => 'div.add-listing-nav a',
                'option' => 'options_add-navigation-left-side_font-size',
                'type' => 'font-size',
            ],

            'addlist_nav_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.add-listing-nav a',
                'option' => 'options_add-navigation-left-side_font-weight',
                'type' => 'font-weight',
            ],

            'divide_addlisting_labels' => [
                'type' => 'divider',
                'label' => 'Form labels',
            ],

            'addlist_label_size' => [
                'label' => 'Font size',
                'selector' => 'form#submit-job-form .field-head label',
                'option' => 'options_field-label-options_font-size',
                'type' => 'font-size',
            ],

            'addlist_label_weight' => [
                'label' => 'Font weight',
                'selector' => 'form#submit-job-form .field-head label',
                'option' => 'options_field-label-options_font-weight',
                'type' => 'font-weight',
            ],

            'divide_addlisting_heading' => [
                'type' => 'divider',
                'label' => 'Form heading',
            ],

            'addlist_heading_size' => [
                'label' => 'Font size',
                'selector' => 'form.c27-submit-listing-form .form-section .title-style-1 h5',
                'option' => 'options_form-heading-options_font-size',
                'type' => 'font-size',
            ],

            'addlist_heading_weight' => [
                'label' => 'Font weight',
                'selector' => 'form.c27-submit-listing-form .form-section .title-style-1 h5',
                'option' => 'options_form-heading-options_font-weight',
                'type' => 'font-weight',
            ],

            'divide_addlisting_placeholder' => [
                'type' => 'divider',
                'label' => 'Input placeholder',
            ],

            'addlist_placeholder_size' => [
                'label' => 'Font size',
                'selector' => 'form.c27-submit-listing-form input, form.c27-submit-listing-form textarea,
                form.c27-submit-listing-form .select2-container .select2-search--inline .select2-search__field,
                form.c27-submit-listing-form .select2-container--default .select2-selection--single .select2-selection__rendered, form#submit-job-form .small,
                form.c27-submit-listing-form .select2-container--default .select2-selection--multiple .select2-selection__choice, form#submit-job-form ul.c27-term-checklist label',
                'type' => 'font-size',
            ],

            'addlist_placeholder_weight' => [
                'label' => 'Font weight',
                'selector' => 'form.c27-submit-listing-form input, form.c27-submit-listing-form textarea,
                form.c27-submit-listing-form .select2-container .select2-search--inline .select2-search__field,
                form.c27-submit-listing-form .select2-container--default .select2-selection--single .select2-selection__rendered, form#submit-job-form .small,
                form.c27-submit-listing-form .select2-container--default .select2-selection--multiple .select2-selection__choice, form#submit-job-form ul.c27-term-checklist label',
                'type' => 'font-weight',
            ],

            'divide_addlisting_button_preview' => [
                'type' => 'divider',
                'label' => 'Preview button',
            ],

            'addlist_preview_btn_size' => [
                'label' => 'Font size',
                'selector' => 'form div.listing-form-submit-btn button[type=submit]',
                'type' => 'font-size',
            ],

            'addlist_preview_btn_weight' => [
                'label' => 'Font weight',
                'selector' => 'form div.listing-form-submit-btn button[type=submit]',
                'type' => 'font-weight',
            ],
        ],
    ],

    'reviews-comments' => [
        'label' => 'Comments and reviews',
        'settings' => [
            'divide_comments_username' => [
                'type' => 'divider',
                'label' => 'Username',
            ],

            'comments_username_size' => [
                'label' => 'Font size',
                'selector' => 'div.comment-head h5 a',
                'type' => 'font-size',
            ],

            'comments_username_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.comment-head h5 a',
                'type' => 'font-weight',
            ],

            'divide_comments_body_text' => [
                'type' => 'divider',
                'label' => 'Comment body',
            ],

            'comments_body_size' => [
                'label' => 'Font size',
                'selector' => 'div.comment-body p',
                'type' => 'font-size',
            ],

            'comments_body_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.comment-body p',
                'type' => 'font-weight',
            ],

            'comments_body_height' => [
                'label' => 'Line height',
                'selector' => 'div.comment-body p',
                'type' => 'line-height',
            ],

            'divide_comments_review-categories' => [
                'type' => 'divider',
                'label' => 'Review categories',
            ],

            'comments_review_cat_size' => [
                'label' => 'Font size',
                'selector' => '.rating-category-label',
                'type' => 'font-size',
            ],

            'comments_review_cat_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.rating-category-label',
                'type' => 'font-weight',
            ],
        ],
    ],

    'blog-fonts' => [
        'label' => 'Blog feed',
        'settings' => [

            'divide_archive_page_title' => [
                'type' => 'divider',
                'label' => 'Archive title',
            ],

            'blog_post_archive_title_size' => [
                'label' => 'Font size',
                'selector' => 'section.archive-heading h1',
                'type' => 'font-size',
            ],

            'blog_post_archive_title_weight' => [
                'label' => 'Font weight',
                'selector' => 'section.archive-heading h1',
                'type' => 'font-weight',
            ],

            'blog_post_archive_title_color' => [
                'label' => 'Color',
                'selector' => 'section.archive-heading h1',
                'type' => 'color',
            ],

            'divide_blog_post_feed_title' => [
                'type' => 'divider',
                'label' => 'Blog post feed title',
            ],

            'blog_post_feed_title_size' => [
                'label' => 'Font size',
                'selector' => 'div.sbf-title a',
                'type' => 'font-size',
            ],

            'blog_post_feed_title_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.sbf-title a',
                'type' => 'font-weight',
            ],

            'blog_post_feed_title_line_height' => [
                'label' => 'Line height',
                'selector' => 'div.sbf-title a',
                'type' => 'line-height',
            ],

            'blog_post_feed_title_color' => [
                'label' => 'Color',
                'selector' => 'div.sbf-title a',
                'type' => 'color',
            ],

            'divide_blog_post_feed_desc' => [
                'type' => 'divider',
                'label' => 'Blog post feed description',
            ],

            'blog_post_feed_desc_size' => [
                'label' => 'Font size',
                'selector' => 'div.sbf-title p',
                'type' => 'font-size',
            ],

            'blog_post_feed_desc_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.sbf-title p',
                'type' => 'font-weight',
            ],

            'blog_post_feed_desc_line_height' => [
                'label' => 'Line height',
                'selector' => 'div.sbf-title p',
                'type' => 'line-height',
            ],

            'blog_post_feed_desc_color' => [
                'label' => 'Color',
                'selector' => 'div.sbf-title p',
                'type' => 'color',
            ],
        ],
    ],

    'single-post' => [
        'label' => 'Single post',
        'settings' => [

            'divide_single_post_title' => [
                'type' => 'divider',
                'label' => 'Post title',
            ],

            'single_post_title_size' => [
                'label' => 'Font size',
                'selector' => 'section.blogpost-section .blog-title h1',
                'type' => 'font-size',
            ],

            'single_post_title_weight' => [
                'label' => 'Font weight',
                'selector' => 'section.blogpost-section .blog-title h1',
                'type' => 'font-weight',
            ],

            'single_post_title_line_height' => [
                'label' => 'Line height',
                'selector' => 'section.blogpost-section .blog-title h1',
                'type' => 'line-height',
            ],

            'single_post_title_color' => [
                'label' => 'Color',
                'selector' => 'section.blogpost-section .blog-title h1',
                'type' => 'color',
            ],

            'divide_single_post_details' => [
                'type' => 'divider',
                'label' => 'Post details',
            ],

            'single_post_details_size' => [
                'label' => 'Font size',
                'selector' => 'div.post-cover-buttons > ul > li > a, div.post-cover-buttons > ul > li > div',
                'type' => 'font-size',
            ],

            'single_post_details_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.post-cover-buttons > ul > li > a, div.post-cover-buttons > ul > li > div',
                'type' => 'font-weight',
            ],

            'single_post_details_color' => [
                'label' => 'Color',
                'selector' => 'div.post-cover-buttons > ul > li > a, div.post-cover-buttons > ul > li > div',
                'type' => 'color',
            ],

            'divide_single_post_body' => [
                'type' => 'divider',
                'label' => 'Paragraphs',
            ],

            'single_post_paragraph_size' => [
                'label' => 'Font size',
                'selector' => 'section.blogpost-section .section-body p, section.blogpost-section .section-body ul, section.blogpost-section .section-body p, section.blogpost-section .section-body ol',
                'type' => 'font-size',
            ],

            'single_post_paragraph_weight' => [
                'label' => 'Font weight',
                'selector' => 'section.blogpost-section .section-body p, section.blogpost-section .section-body ul, section.blogpost-section .section-body p, section.blogpost-section .section-body ol',
                'type' => 'font-weight',
            ],

            'single_post_paragraph_line_height' => [
                'label' => 'Line height',
                'selector' => 'section.blogpost-section .section-body p, section.blogpost-section .section-body ul, section.blogpost-section .section-body p, section.blogpost-section .section-body ol',
                'type' => 'line-height',
            ],

            'single_post_paragraph_color' => [
                'label' => 'Color',
                'selector' => 'section.blogpost-section .section-body p, section.blogpost-section .section-body ul, section.blogpost-section .section-body p, section.blogpost-section .section-body ol',
                'type' => 'color',
            ],

            'divide_single_post_heading color' => [
                'type' => 'divider',
                'label' => 'Heading color',
            ],

            'single_post_heading_color' => [
                'label' => 'Color',
                'selector' => 'div.c27-content-wrapper h1, div.c27-content-wrapper h2, div.c27-content-wrapper h3,
                div.c27-content-wrapper h4, div.c27-content-wrapper h5, div.c27-content-wrapper h6',
                'type' => 'color',
            ],

            'divide_single_post_h1' => [
                'type' => 'divider',
                'label' => 'Heading 1',
            ],

            'single_post_h1_size' => [
                'label' => 'Font size',
                'selector' => 'div.c27-content-wrapper h1',
                'type' => 'font-size',
            ],

            'single_post_h1_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.c27-content-wrapper h1',
                'type' => 'font-weight',
            ],

            'single_post_h1_line_height' => [
                'label' => 'Line height',
                'selector' => 'div.c27-content-wrapper h1',
                'type' => 'line-height',
            ],

            'divide_single_post_h2' => [
                'type' => 'divider',
                'label' => 'Heading 2',
            ],

            'single_post_h2_size' => [
                'label' => 'Font size',
                'selector' => 'div.c27-content-wrapper h2',
                'type' => 'font-size',
            ],

            'single_post_h2_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.c27-content-wrapper h2',
                'type' => 'font-weight',
            ],

            'single_post_h2_line_height' => [
                'label' => 'Line height',
                'selector' => 'div.c27-content-wrapper h2',
                'type' => 'line-height',
            ],

            'divide_single_post_h3' => [
                'type' => 'divider',
                'label' => 'Heading 3',
            ],

            'single_post_h3_size' => [
                'label' => 'Font size',
                'selector' => 'div.c27-content-wrapper h3',
                'type' => 'font-size',
            ],

            'single_post_h3_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.c27-content-wrapper h3',
                'type' => 'font-weight',
            ],

            'single_post_h3_line_height' => [
                'label' => 'Line height',
                'selector' => 'div.c27-content-wrapper h3',
                'type' => 'line-height',
            ],

            'divide_single_post_h4' => [
                'type' => 'divider',
                'label' => 'Heading 4',
            ],

            'single_post_h4_size' => [
                'label' => 'Font size',
                'selector' => 'div.c27-content-wrapper h4',
                'type' => 'font-size',
            ],

            'single_post_h4_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.c27-content-wrapper h4',
                'type' => 'font-weight',
            ],

            'single_post_h4_line_height' => [
                'label' => 'Line height',
                'selector' => 'div.c27-content-wrapper h4',
                'type' => 'line-height',
            ],

            'divide_single_post_h5' => [
                'type' => 'divider',
                'label' => 'Heading 5',
            ],

            'single_post_h5_size' => [
                'label' => 'Font size',
                'selector' => 'div.c27-content-wrapper h5',
                'type' => 'font-size',
            ],

            'single_post_h5_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.c27-content-wrapper h5',
                'type' => 'font-weight',
            ],

            'single_post_h5_line_height' => [
                'label' => 'Line height',
                'selector' => 'div.c27-content-wrapper h5',
                'type' => 'line-height',
            ],

            'divide_single_post_h6' => [
                'type' => 'divider',
                'label' => 'Heading 6',
            ],

            'single_post_h6_size' => [
                'label' => 'Font size',
                'selector' => 'div.c27-content-wrapper h6',
                'type' => 'font-size',
            ],

            'single_post_h6_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.c27-content-wrapper h6',
                'type' => 'font-weight',
            ],

            'single_post_h6_line_height' => [
                'label' => 'Line height',
                'selector' => 'div.c27-content-wrapper h6',
                'type' => 'line-height',
            ],
        ],
    ],

    'user-dashboard' => [
        'label' => 'User dashboard',
        'settings' => [

            'divide_user_dashboard_menu' => [
                'type' => 'divider',
                'label' => 'Dashboard menu',
            ],

            'dash_menu_size' => [
                'label' => 'Font size',
                'selector' => 'nav.woocommerce-MyAccount-navigation ul li a',
                'option' => 'options_dashboard-menu-options_font-size',
                'type' => 'font-size',
            ],

            'dash_menu_weight' => [
                'label' => 'Font weight',
                'selector' => 'nav.woocommerce-MyAccount-navigation ul li a',
                'option' => 'options_dashboard-menu-options_font-weight',
                'type' => 'font-weight',
            ],

            'divide_user_dashboard_block_title' => [
                'type' => 'divider',
                'label' => 'Dashboard block title',
            ],

            'dash_block_size' => [
                'label' => 'Font size',
                'selector' => 'div.woocommerce-MyAccount-content .element:not(.form-section) .title-style-1 h5',
                'option' => 'options_dashboard-block-title-options_font-size',
                'type' => 'font-size',
            ],

            'dash_block_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.woocommerce-MyAccount-content .element:not(.form-section) .title-style-1 h5',
                'option' => 'options_dashboard-block-title-options_font-weight',
                'type' => 'font-weight',
            ],
        ],
    ],

    'shop-settings' => [
        'label' => 'Shop page',
        'settings' => [

            'divide_catalog_title' => [
                'type' => 'divider',
                'label' => 'Catalog product title',
            ],

            'shop_catalog_title_size' => [
                'label' => 'Font size',
                'selector' => '.woocommerce ul.products li.product h2.woocommerce-loop-product__title',
                'option' => 'options_catalog-product-title-options_font-size',
                'type' => 'font-size',
            ],

            'shop_catalog_title_weight' => [
                'label' => 'Font weight',
                'selector' => '.woocommerce ul.products li.product h2.woocommerce-loop-product__title',
                'option' => 'options_catalog-product-title-options_font-weight',
                'type' => 'font-weight',
            ],

            'shop_catalog_title_color' => [
                'label' => 'Color',
                'selector' => '.woocommerce ul.products li.product h2.woocommerce-loop-product__title',
                'type' => 'color',
            ],

            'divide_catalog_price' => [
                'type' => 'divider',
                'label' => 'Catalog product price',
            ],

            'shop_catalog_price_size' => [
                'label' => 'Font size',
                'selector' => '.woocommerce ul.products li.product span.price, .woocommerce ul.products li.product span.price *',
                'type' => 'font-size',
            ],

            'shop_catalog_price_weight' => [
                'label' => 'Font weight',
                'selector' => '.woocommerce ul.products li.product span.price, .woocommerce ul.products li.product span.price *',
                'type' => 'font-weight',
            ],

            'shop_catalog_price_color' => [
                'label' => 'Color',
                'selector' => '.woocommerce ul.products li.product span.price, .woocommerce ul.products li.product span.price *',
                'type' => 'color',
            ],

            'divide_product_title' => [
                'type' => 'divider',
                'label' => 'Single product page title',
            ],

            'product_title_size' => [
                'label' => 'Font size',
                'selector' => '.woocommerce div.product h1.product_title',
                'option' => 'options_single-catalog-product-title-options_font-size',
                'type' => 'font-size',
            ],

            'product_title_weight' => [
                'label' => 'Font weight',
                'selector' => '.woocommerce div.product h1.product_title',
                'option' => 'options_single-catalog-product-title-options_font-weight',
                'type' => 'font-weight',
            ],

            'product_title_color' => [
                'label' => 'Font weight',
                'selector' => '.woocommerce div.product h1.product_title',
                'type' => 'color',
            ],

            'divide_product_description' => [
                'type' => 'divider',
                'label' => 'Single product description',
            ],

            'product_body_size' => [
                'label' => 'Font size',
                'selector' => 'div.woocommerce-product-details__short-description p, div#tab-description p',
                'type' => 'font-size',
            ],

            'product_body_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.woocommerce-product-details__short-description p, div#tab-description p',
                'type' => 'font-weight',
            ],

            'product_body_height' => [
                'label' => 'Line height',
                'selector' => 'div.woocommerce-product-details__short-description p, div#tab-description p',
                'type' => 'line-height',
            ],

            'divide_single_product_price' => [
                'type' => 'divider',
                'label' => 'Single product price',
            ],

            'shop_single_price_size' => [
                'label' => 'Font size',
                'selector' => 'body.single-product div.product p.price .woocommerce-Price-amount',
                'type' => 'font-size',
            ],

            'shop_single_price_weight' => [
                'label' => 'Font weight',
                'selector' => 'body.single-product div.product p.price .woocommerce-Price-amount',
                'type' => 'font-weight',
            ],

            'shop_single_price_color' => [
                'label' => 'Color',
                'selector' => 'body.single-product div.product p.price .woocommerce-Price-amount,
                .woocommerce div.product .summary p.price del, .woocommerce div.product .summary p.price ins',
                'type' => 'color',
            ],
        ],
    ],

    'categories-widget' => [
        'label' => 'Categories widget',
        'settings' => [

            'divide_categories_default_title' => [
                'type' => 'divider',
                'label' => 'Category widget default template title',
            ],

            'categories_title_size' => [
                'label' => 'Font size',
                'selector' => 'div.listing-cat .lc-info h4',
                'option' => 'options_default-template-options_font-size',
                'type' => 'font-size',
            ],

            'categories_title_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.listing-cat .lc-info h4',
                'option' => 'options_default-template-options_font-weight',
                'type' => 'font-weight',
            ],

            'divide_categories_alternate_title' => [
                'type' => 'divider',
                'label' => 'Category widget alternate template title',
            ],

            'categories_alt_title_size' => [
                'label' => 'Font size',
                'selector' => 'div.one-region h2',
                'option' => 'options_alternate-template-options_font-size',
                'type' => 'font-size',
            ],

            'categories_alt_title_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.one-region h2',
                'option' => 'options_alternate-template-options_font-weight',
                'type' => 'font-weight',
            ],

            'divide_categories_cards_title' => [
                'type' => 'divider',
                'label' => 'Category widget cards template title',
            ],

            'categories_cards_title_size' => [
                'label' => 'Font size',
                'selector' => 'div.elementor-widget-case27-listing-categories-widget .ac-front-side .category-name',
                'option' => 'options_cards-template-options_font-size',
                'type' => 'font-size',
            ],

            'categories_cards_title_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.elementor-widget-case27-listing-categories-widget .ac-front-side .category-name',
                'option' => 'options_cards-template-options_font-weight',
                'type' => 'font-weight',
            ],

            'divide_categories_cards_alt_title' => [
                'type' => 'divider',
                'label' => 'Category widget cards alternate template title',
            ],

            'categories_cards_alt_title_size' => [
                'label' => 'Font size',
                'selector' => 'div.car-item-details h3',
                'option' => 'options_cards_alternate_options_font-size',
                'type' => 'font-size',
            ],

            'categories_cards_alt_title_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.car-item-details h3',
                'option' => 'options_cards_alternate_options_font-weight',
                'type' => 'font-weight',
            ],
        ],
    ],

    'wp-widget-styles' => [
        'label' => 'Footer and sidebar widgets',
        'settings' => [

            'divide_fs_widgets_title' => [
                'type' => 'divider',
                'label' => 'Widget title',
            ],

            'fs_widgets_title_size' => [
                'label' => 'Font size',
                'selector' => 'div.sidebar-widgets .c_widget .title-style-1 h5, div.c_widget_title h5',
                'type' => 'font-size',
            ],

            'fs_widgets_title_weight' => [
                'label' => 'Font weight',
                'selector' => 'div.sidebar-widgets .c_widget .title-style-1 h5, div.c_widget_title h5',
                'type' => 'font-weight',
            ],

            'fs_widgets_title_color' => [
                'label' => 'Font weight',
                'selector' => 'div.sidebar-widgets .c_widget .title-style-1 h5, div.c_widget_title h5',
                'type' => 'color',
            ],

        ],
    ],

];