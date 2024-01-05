<?php

namespace MyListing\Ext\Author_Review;

class Author_Review {

	public static function boot() {
    	new self;
    }

	public function __construct() {

		require_once get_stylesheet_directory() . '/includes/extensions/author-review/wp-review.php';
		require_once get_stylesheet_directory() . '/includes/extensions/author-review/customers-list.php';

		$this->table_version = '0.1';
		$this->current_version = get_option( 'mylisting_author_review_table_version' );

		// Setup DB.
		$this->setup_tables();

		add_filter( 'set-screen-option', array( $this, 'add_query_vars' ), 10, 3 );
		add_action( 'admin_menu', [ $this, 'plugin_menu' ] );
	}

	public static function set_screen( $status, $option, $value ) {
		return $value;
	}

	public function plugin_menu() {

		$hook = add_menu_page(
			'Sitepoint WP_List_Table Example',
			'SP WP_List_Table',
			'manage_options',
			'wp_list_table_class',
			[ $this, 'plugin_settings_page' ]
		);

		add_action( "load-$hook", [ $this, 'screen_option' ] );
	}


	/**
	 * Plugin settings page
	 */
	public function plugin_settings_page() {
		?>
		<div class="wrap">
			<h2>WP_List_Table Class Example</h2>

			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">
							<form method="post">
								<?php
								$this->customers_obj->prepare_items();
								$this->customers_obj->display(); ?>
							</form>
						</div>
					</div>
				</div>
				<br class="clear">
			</div>
		</div>
	<?php
	}

	/**
	 * Screen options
	 */
	public function screen_option() {

		$option = 'per_page';
		$args   = [
			'label'   => 'Customers',
			'default' => 5,
			'option'  => 'customers_per_page'
		];

		add_screen_option( $option, $args );

		$this->customers_obj = new Customers_List();
	}

	public function setup_tables() {
		if ( $this->table_version === $this->current_version ) {
			return;
		}

		global $wpdb;
		$table_name = $wpdb->prefix . 'mylisting_author_reviews';
		$sql = "CREATE TABLE $table_name (
			review_ID bigint(20) unsigned NOT NULL auto_increment,
			review_author_ID bigint(20) unsigned NOT NULL default '0',
			review_author tinytext NOT NULL,
			review_author_email varchar(100) NOT NULL default '',
			review_author_url varchar(200) NOT NULL default '',
			review_author_IP varchar(100) NOT NULL default '',
			review_date datetime NOT NULL default '0000-00-00 00:00:00',
			review_date_gmt datetime NOT NULL default '0000-00-00 00:00:00',
			review_content text NOT NULL,
			review_karma int(11) NOT NULL default '0',
			review_approved varchar(20) NOT NULL default '1',
			review_agent varchar(255) NOT NULL default '',
			review_type varchar(20) NOT NULL default 'review',
			review_parent bigint(20) unsigned NOT NULL default '0',
			user_id bigint(20) unsigned NOT NULL default '0',
			PRIMARY KEY  (review_ID),
			KEY review_author_ID (review_author_ID),
			KEY review_approved_date_gmt (review_approved,review_date_gmt),
			KEY review_date_gmt (review_date_gmt),
			KEY review_parent (review_parent),
			KEY review_author_email (review_author_email(10))
		);";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		update_option( 'mylisting_author_review_table_version', $this->table_version );
	}
}