<?php

namespace MyListing\Ext\Data_Exporters;

class Exporter_Data extends Base_Exporters {
	use \MyListing\Src\Traits\Instantiatable;

	public function __construct() {
		// Initialize data exporters and erasers.
		add_action( 'admin_init', array( $this, 'register_erasers_exporters' ) );

		add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'register_exporters' ), -1 );
		add_filter( 'wp_privacy_personal_data_erasers', array( $this, 'register_erasers' ), -1 );
	}

	public function register_erasers_exporters() {
		$this->add_exporter( 'job_listing', __( 'Listings', 'my-listing' ), array( $this, 'listing_data_exporter' ) );
		$this->add_exporter( 'claim', __( 'Claims', 'my-listing' ), array( $this, 'claims_data_exporter' ) );

		$this->add_eraser( 'job_listing', __( 'Listings', 'my-listing' ), array( $this, 'listing_data_remover' ) );
	}

	public static function claims_data_exporter( $email_address, $page ) {
		$page           = (int) $page;
		$user           = get_user_by( 'email', $email_address ); // Check if user has an ID in the DB to load stored personal data.
		$data 			= array();
		$args    		= array(
			'posts_per_page'    => -1,
			'page'     			=> $page,
			'author' 			=> $user->ID,
		);

		$claims = self::get_user_claims( $args );

		if ( $claims->posts ) {
			foreach ( $claims->posts as $claim_id ) {
				$data[] = array(
					'group_id'          => 'claim',
					'group_label'       => __( 'Claims', 'my-listing' ),
					'group_description' => __( 'Users Claims data.', 'my-listing' ),
					'item_id'           => 'claim-' . $claim_id,
					'data'              => self::get_user_claim_personal_data( $claim_id ),
				);
			}
		}

		return array(
			'data' => $data,
			'done' => true,
		);
	}

	protected static function get_user_claim_personal_data( $claim_id ) {
		$personal_data = [];

		$export_meta = [
			'_listing_id'       => __( 'Listing ID', 'my-listing' ),
			'_user_id'          => __( 'User ID', 'my-listing' ),
			'_user_package_id'	=> __( 'User Package ID', 'my-listing' ),
			'_status'           => __( 'Claim Status', 'my-listing' ),
		];

		foreach ( $export_meta as $meta_key => $meta_name ) {
			$value = get_post_meta( $claim_id, $meta_key, true );

			if ( ! $value ) {
				continue;
			}

			$personal_data[] = [
				'name'  => $meta_name,
				'value' => $value,
			];
		}

		return $personal_data;
	}

	public static function get_user_claims( $args ) {
		$args = wp_parse_args( $args, [
			'posts_per_page'    => -1,
			'author'            => null,
			'page'				=> ''
		] );

		$query_args = [
			'post_type'         => 'claim',
			'post_status'       => 'publish',
			'posts_per_page'    => $args['posts_per_page'],
			'author'            => $args['author'],
			'fields' 			=> 'ids',
			'page'				=> $args['page']
		];

		if ( ! $query_args['author'] ) {
			unset( $query_args['author'] );
		}

		$result = new \WP_Query( $query_args );

		return $result;
	}

	public static function listing_data_remover( $email_address, $page ) {

		$response = array(
			'items_removed'  => false,
			'items_retained' => false,
			'messages'       => array(),
			'done'           => true,
		);
		
		$user = get_user_by( 'email', $email_address );

		$args    		= array(
			'posts_per_page'    => -1,
			'page'     			=> $page,
			'author' 			=> $user->ID,
		);

		$listings = self::get_job_listings( $args );
		$messages = self::get_user_messages( $user->ID );

		if ( $listings->posts ) {
			foreach ( $listings->posts as $listing_id ) {
				wp_trash_post( $listing_id );
				$response['messages'][]    = sprintf( __( 'Removed Listing "%s"', 'my-listing' ), get_the_title( $listing_id ) );
			}
		}

		if ( $messages ) {
			global $wpdb;
			foreach ( $messages as $message ) {
				$wpdb->delete( \MyListing\Ext\Messages\Reply::get_table_name(), ['message_id' => $message->message_id ], ['%d'] );
			}

			$response['messages'][] = __( 'Removed direct messages', 'my-listing' );
		}

		return $response;
	}

	public static function get_user_messages( $user_id ) {
		global $wpdb;

		$messages = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}mylisting_messages WHERE sender_id = %d", $user_id ) );

		return $messages;
	}

	public static function listing_data_exporter( $email_address, $page ) {
		$page           = (int) $page;
		$user           = get_user_by( 'email', $email_address ); // Check if user has an ID in the DB to load stored personal data.
		$data 			= array();
		$args    		= array(
			'posts_per_page'    => -1,
			'page'     			=> $page,
			'author' 			=> $user->ID,
		);

		$listings = self::get_job_listings( $args );

		if ( $listings->posts ) {
			foreach ( $listings->posts as $listing_id ) {
				$data[] = array(
					'group_id'          => 'job_listing',
					'group_label'       => __( 'Listings', 'my-listing' ),
					'group_description' => __( 'User&#8217;s Job Listing data.', 'my-listing' ),
					'item_id'           => 'listing-' . $listing_id,
					'data'              => self::get_listing_personal_data( $listing_id ),
				);
			}
		}

		return array(
			'data' => $data,
			'done' => true,
		);
	}

	protected static function get_listing_personal_data( $listing_id ) {
		$listing = \MyListing\Src\Listing::get( $listing_id );
		$personal_data = [];

		if ( ! ( $listing && $listing->type ) ) {
		    return $personal_data;
		}

		foreach ( $listing->get_fields() as $key => $field ) {
			$personal_data[] = array(
				'name'  => $field->get_key(),
				'value' => $field->get_string_value(),
			);
		}

		return $personal_data;
	}

	public static function get_job_listings( $args ) {

		$args = wp_parse_args( $args, [
			'posts_per_page'    => -1,
			'author'            => null,
			'page'				=> ''
		] );

		$query_args = [
			'post_type'         => 'job_listing',
			'post_status'       => 'publish',
			'posts_per_page'    => $args['posts_per_page'],
			'author'            => $args['author'],
			'fields' 			=> 'ids',
			'page'				=> $args['page']
		];

		if ( ! $query_args['author'] ) {
			unset( $query_args['author'] );
		}

		$result = new \WP_Query( $query_args );

		return $result;
	}
}