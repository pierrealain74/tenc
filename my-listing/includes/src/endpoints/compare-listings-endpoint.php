<?php

namespace MyListing\Src\Endpoints;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Compare_Listings_Endpoint {

    public function __construct() {

        add_action( 'mylisting_ajax_compare_listings', [ $this, 'handle' ] );
        add_action( 'mylisting_ajax_nopriv_compare_listings', [ $this, 'handle' ] );
    }

    public function handle() {
        mylisting_check_ajax_referrer();

        $post_id = '';
        if ( empty( $_REQUEST['listing_ids'] ) ) {
            return;
        } else {
            $post_ids = array_map( 'absint', (array) $_REQUEST['listing_ids'] );
        }

        $result = [];
        foreach ( $post_ids as $post_id ) {
        	$listing = \MyListing\Src\Listing::get( $post_id );
            if ( ! $listing || $listing->get_status() !== 'publish' ) {
                continue;
            }

            $result[ $post_id ][ 'title' ] = [
                'label' => '',
                'value' => '',
            ];
            if ( $logo = $listing->get_logo() ) {
                $result[ $post_id ]['title']['value'] .= sprintf(
                    '<img src="%s">',
                    esc_url( $logo )
                );
            }

            $result[ $post_id ]['title']['value'] .= sprintf( '<strong>%s</strong>', $listing->get_title() );

            foreach ( $listing->get_fields() as $field ) {
                if ( ! $field->get_prop('show_in_compare') ) {
                    continue;
                }

                if ( $field->get_key() === 'job_title' || $field->get_key() === 'job_logo' ) {
                    continue;
                }

                $value = $field->get_string_value();
                if ( ! ( is_string( $value ) || is_numeric( $value ) ) ) {
                    $value = '';
                }

                if ( $field->get_type() === 'file' ) {
                    $value = '';
                    foreach ( (array) $field->get_value() as $single_file ) {
                        $url = c27()->get_resized_image( $single_file, 'full' );
                        if ( $url ) {
                            $value .= sprintf(
                                '<a href="%s" target="_blank">%s</a><br>',
                                esc_url( $url ),
                                _x( 'View attachment', 'comparison modal', 'my-listing' )
                            );
                        }
                    }
                }

                $result[ $post_id ][ $field->get_key() ] = [
                    'label' => $field->get_label(),
                    'value' => $value,
                ];
            }
        }

		$html = '<table border="1" class="compare-table" cellpadding="5" cellspacing="5">';
        foreach ( $result[ $post_id ] as $field_key => $field ) {
            $html .= '<tr class="compare-row"><th  class="compare-head">'.$field['label'].'</th>';
            foreach( $result as $postdata ) {
                $val = isset( $postdata[ $field_key ], $postdata[ $field_key ]['value'] ) ? $postdata[ $field_key ]['value'] : '';
                $html .= '<td class="compare-cell">'.$val.'</td>';
            }
            $html .= '</tr>';
        }
		$html .= '</table>';

        return wp_send_json( [
            'success' => true,
            'html' => $html,
        ]);
    }
}