<?php

namespace MyListing\Src\Forms\Fields;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Presets {

	private $fields = [];

	public static function get( $type ) {
		if ( isset( $fields[ $type->get_slug() ] ) ) {
			return $fields[ $type->get_slug() ];
		}

		$fields[ $type->get_slug() ] = apply_filters( 'mylisting/types/fields/presets', [
			'job_title' => new Text_Field( [
				'slug' => 'job_title',
				'label' => __( 'Title', 'my-listing' ),
				'required' => true,
				'priority' => 1,
				'is_custom' => false,
			] ),

			'job_description' => new Texteditor_Field( [
				'slug' => 'job_description',
				'label' => __( 'Description', 'my-listing' ),
				'required' => true,
				'priority' => 7,
				'is_custom' => false,
			] ),

			'job_tagline' => new Text_Field( [
				'slug' => 'job_tagline',
				'label' => __( 'Tagline', 'my-listing' ),
				'required' => false,
				'priority' => 2,
				'is_custom' => false,
				'maxlength' => 90,
			] ),

			'job_location' => new Location_Field( [
				'slug' => 'job_location',
				'label' => __( 'Location', 'my-listing' ),
				'placeholder' => __( 'e.g. "London"', 'my-listing' ),
				'required' => false,
				'priority' => 3,
				'is_custom' => false,
			] ),

			'job_category' => new Term_Select_Field( [
				'slug' => 'job_category',
				'label' => __( 'Category', 'my-listing' ),
				'required' => false,
				'priority' => 4,
				'taxonomy' => 'job_listing_category',
				'is_custom' => false,
				'terms-template' => 'multiselect',
			] ),

			'region' => new Term_Select_Field( [
				'slug' => 'region',
				'label' => __( 'Region', 'my-listing' ),
				'required' => false,
				'priority' => 5,
				'taxonomy' => 'region',
				'is_custom' => false,
				'terms-template' => 'multiselect',
			] ),

			'job_tags' => new Term_Select_Field( [
				'slug' => 'job_tags',
				'label' => __( 'Tags', 'my-listing' ),
				'required' => false,
				'priority' => 6,
				'taxonomy' => 'case27_job_listing_tags',
				'is_custom' => false,
				'terms-template' => 'multiselect',
			] ),

			'job_email' => new Email_Field( [
				'slug' => 'job_email',
				'label' => __( 'Contact Email', 'my-listing' ),
				'required' => false,
				'priority' => 8,
				'is_custom' => false,
			] ),

			'job_logo' => new File_Field( [
				'slug' => 'job_logo',
				'label' => __( 'Logo', 'my-listing' ),
				'required' => true,
				'priority' => 9,
				'ajax' => true,
				'multiple' => false,
				'is_custom' => false,
				'allowed_mime_types' => [
					'jpg' => 'image/jpeg',
					'jpeg' => 'image/jpeg',
					'gif' => 'image/gif',
					'png' => 'image/png',
					'webp' => 'image/webp',
				],
			] ),

			'job_cover' => new File_Field( [
				'slug' => 'job_cover',
				'label' => __( 'Cover Image', 'my-listing' ),
				'required' => false,
				'priority' => 10,
				'ajax' => true,
				'multiple' => false,
				'is_custom' => false,
				'allowed_mime_types' => [
					'jpg' => 'image/jpeg',
					'jpeg' => 'image/jpeg',
					'gif' => 'image/gif',
					'png' => 'image/png',
					'webp' => 'image/webp',
				],
			] ),

			'job_gallery' => new File_Field( [
				'slug' => 'job_gallery',
				'label' => __( 'Gallery Images', 'my-listing' ),
				'required' => false,
				'priority' => 11,
				'ajax' => true,
				'multiple' => true,
				'is_custom' => false,
				'allowed_mime_types' => [
					'jpg' => 'image/jpeg',
					'jpeg' => 'image/jpeg',
					'gif' => 'image/gif',
					'png' => 'image/png',
					'webp' => 'image/webp',
				],
			] ),

			'job_website' => new URL_Field( [
				'slug' => 'job_website',
				'label' => __( 'Website', 'my-listing' ),
				'required' => false,
				'priority' => 12,
				'is_custom' => false,
			] ),

			'job_phone' => new Text_Field( [
				'slug' => 'job_phone',
				'label' => __( 'Phone Number', 'my-listing' ),
				'required' => false,
				'priority' => 13,
				'is_custom' => false,
			] ),

			'job_video_url' => new Url_Field( [
				'slug' => 'job_video_url',
				'label' => __( 'Video URL', 'my-listing' ),
				'required' => false,
				'priority' => 14,
				'is_custom' => false,
			] ),

			'event_date' => new Recurring_Date_Field( [
				'slug' => 'event_date',
				'label' => __( 'Event Date', 'my-listing' ),
				'required' => false,
				'priority' => 14.5,
				'is_custom' => false,
			] ),

			'job_date' => new Date_Field( [
				'slug' => 'job_date',
				'label' => __( 'Date', 'my-listing' ),
				'required' => false,
				'priority' => 15,
				'format' => 'date',
				'is_custom' => false,
			] ),

			'related_listing' => new Related_Listing_Field( [
				'slug' => 'related_listing',
				'label' => __( 'Related Listing', 'my-listing' ),
				'required' => false,
				'priority' => 16,
				'listing_type' => [],
				'is_custom' => false,
			] ),

			'work_hours' => new Work_Hours_Field( [
				'slug' => 'work_hours',
				'label' => __( 'Work Hours', 'my-listing' ),
				'required' => false,
				'priority' => 17,
				'is_custom' => false,
			] ),

			'select_products' => new Select_Products_Field( [
				'slug' => 'select_products',
				'label' => __( 'Products', 'my-listing' ),
				'required' => false,
				'priority' => 18,
				'is_custom' => false,
			] ),

			'links' => new Links_Field( [
				'slug' => 'links',
				'label' => __( 'Social Networks', 'my-listing' ),
				'required' => false,
				'priority' => 19,
				'is_custom' => false,
			] ),

			'price_range' => new Select_Field( [
				'slug' => 'price_range',
				'label' => __( 'Price Range', 'my-listing' ),
				'required' => false,
				'priority' => 20,
				'options' => is_array( $type->get_field('price_range') ) && ! empty( $type->get_field('price_range')['options'] )
					? $type->get_field('price_range')['options']
					: ['$' => '$', '$$' => '$$', '$$$' => '$$$'],
				'is_custom' => false,
			] ),

			'form_heading' => new Form_Heading_Field( [
				'slug' => 'form_heading',
				'label' => __( 'Form Heading', 'my-listing' ),
				'required' => false,
				'priority' => 21,
				'icon' => 'icon-pencil-2',
				'is_custom' => false,
			] ),

			'repeater' => new General_Repeater_Field( [
				'slug' => 'repeater',
				'label' => __( 'General Repeater', 'my-listing' ),
				'required' => false,
				'priority' => 22,
				'is_custom' => false,
			] ),

			'restaurant_menu' => new General_Repeater_Field( [
				'slug' => 'restaurant_menu',
				'label' => __( 'Restaurant Menu', 'my-listing' ),
				'required' => false,
				'priority' => 23,
				'is_custom' => false,
			] ),
		] );

		foreach ( $fields[ $type->get_slug() ] as $key => $field ) {
			$fields[ $type->get_slug() ][ $key ]['default_label'] = $fields[ $type->get_slug() ][ $key ]['label'];
		}

		return $fields[ $type->get_slug() ];
	}
}
