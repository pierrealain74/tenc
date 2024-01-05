<?php

namespace MyListing\Src;

class User extends \WP_User {

	public function get_link() {
		if ( function_exists( 'bp_core_get_user_domain' ) ) {
			return bp_core_get_user_domain( $this->ID );
		}

		return get_author_posts_url( $this->ID );
	}

	public function get_avatar() {
		return get_avatar_url( $this->ID );
	}

	public function get_id() {
		return $this->ID;
	}

	public function get_display_name() {
		return $this->display_name;
	}

	public function get_name() {
		return $this->display_name;
	}

	public function get_description() {
		return $this->description;
	}

	public function get_social_links() {
		$links = get_user_meta( $this->ID, 'social_links', true );
		if ( empty( $links ) ) {
			return [];
		}

		$networks = [];
		$allowed_networks = \MyListing\Src\Forms\Fields\Links_Field::allowed_networks();

		foreach ( (array) $links as $link ) {
            if ( ! is_array( $link ) || empty( $link['network'] ) ) {
            	continue;
        	}

        	if ( empty( $link['url'] ) || ! isset( $allowed_networks[ $link['network'] ] ) ) {
        		continue;
        	}

        	$network = $allowed_networks[ $link['network'] ];
        	$network['link'] = $link['url'];

        	$networks[] = $network;
		}

		return array_filter( $networks );
	}
}
