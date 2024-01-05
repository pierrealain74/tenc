<?php

namespace MyListing\Src\Endpoints;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Endpoints {

	/**
	 * Register endpoints.
	 *
	 * @since 2.1
	 */
	public static function boot() {
		new File_Upload_Endpoint;
		new Restaurant_Menu_Endpoint;
		new Quick_View_Endpoint;
		new Posts_List_Endpoint;
		new Post_Details_Endpoint;
		new Products_List_Endpoint;
		new Package_List_Endpoint;
		new Users_List_Endpoint;
		new Explore_Terms_Endpoint;
		new Comments_List_Endpoint;
		new Compare_Listings_Endpoint;
		new Post_Duplicate_Endpoint;
		Term_List_Endpoint::instance();
	}

}