<?php

namespace MyListing\Ext\Simple_Products;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Product_Controller extends \WC_REST_Products_Controller {
	public function c27_create_product( $data ) {

		$product = $this->prepare_object_for_database( $data, ! empty( $data['id'] ) );

		if ( ! is_wp_error( $product ) ) {
			$product->save();
			return $product;
		}

		return false;
	}
}