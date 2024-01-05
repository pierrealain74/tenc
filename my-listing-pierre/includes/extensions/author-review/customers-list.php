<?php

namespace MyListing\Ext\Author_Review;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Customers_List extends \WP_List_Table {

	public $checkbox = true;

	public $extra_items;

	private $user_can;

	public function __construct( $args = array() ) {
		global $post_id;

		$post_id = isset( $_REQUEST['p'] ) ? absint( $_REQUEST['p'] ) : 0;

		parent::__construct(
			array(
				'plural'   => 'Author Reviews',
				'singular' => 'Author Review',
				'ajax'     => true,
				'screen'   => isset( $args['screen'] ) ? $args['screen'] : null,
			)
		);
	}

	/**
	 * @return bool
	 */
	public function ajax_user_can() {
		return current_user_can( 'edit_posts' );
	}

	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {
		$this->_column_headers = $this->get_column_info();

		/** Process bulk action */
		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( 'customers_per_page', 5 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		] );

		$this->items = self::get_customers( $per_page, $current_page );
	}

	/**
	 * Retrieve customers data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_customers( $per_page = 5, $page_number = 1 ) {

		global $wpdb;

		$sql = "SELECT * FROM {$wpdb->prefix}mylisting_author_reviews";

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		return $result;
	}

	/**
	 * Delete a customer record.
	 *
	 * @param int $id customer ID
	 */
	public static function delete_customer( $id ) {
		global $wpdb;

		$wpdb->delete(
			"{$wpdb->prefix}mylisting_author_reviews",
			[ 'ID' => $id ],
			[ '%d' ]
		);
	}

	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count() {
		global $wpdb;

		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}mylisting_author_reviews";

		return $wpdb->get_var( $sql );
	}

	/** Text displayed when no customer data is available */
	public function no_items() {
		_e( 'No customers avaliable.', 'sp' );
	}

	/**
	 * @return string|false
	 */
	public function current_action() {
		if ( isset( $_REQUEST['delete_all'] ) ) {
			return 'delete_all';
		}

		return parent::current_action();
	}

	/**
	 * @global int $post_id
	 *
	 * @return array
	 */
	public function get_columns() {
		global $post_id;

		$columns = array();

		if ( $this->checkbox ) {
			$columns['cb'] = '<input type="checkbox" />';
		}

		$columns['author']  = __( 'Author' );
		$columns['comment'] = _x( 'Comment', 'column name' );
		$columns['rating'] = _x( 'Rating', 'column name' );
		$columns['date'] = _x( 'Submitted on', 'column name' );
		$columns['status'] = _x( 'Status', 'column name' );

		return $columns;
	}

	/**
	 * @global WP_Post    $post    Global post object.
	 * @global WP_Comment $comment Global comment object.
	 *
	 * @param WP_Comment $item
	 */
	public function single_row( $item ) {
		$comment = $item;
		$the_comment_class = 'approved';

		if ( ! $the_comment_class ) {
			$the_comment_class = '';
		}

		$this->user_can = current_user_can( 'edit_comment', $comment['id'] );
		$comment_id = $comment['id'];

		echo "<tr id='comment-$comment_id' class='$the_comment_class'>";
		$this->single_row_columns( $comment );
		echo "</tr>\n";

		unset( $GLOBALS['post'], $GLOBALS['comment'] );
	}

	/**
	 * @param WP_Comment $comment The comment object.
	 */
	public function column_cb( $comment ) {
		if ( $this->user_can ) {
			?>
		<label class="screen-reader-text" for="cb-select-<?php echo $comment['id']; ?>"><?php _e( 'Select comment' ); ?></label>
		<input id="cb-select-<?php echo $comment['id']; ?>" type="checkbox" name="delete_comments[]" value="<?php echo $comment['id']; ?>" />
			<?php
		}
	}

	/**
	 * @param WP_Comment $comment The comment object.
	 */
	public function column_rating( $comment ) {
		echo wp_kses( $comment['rating_stars'], 'post' );
	}

	public function column_status( $comment ) {
		echo wp_kses( $comment['status'], 'post' );
	}

	public function column_comment( $comment ) {
		echo '<div class="comment-author">';
			$this->column_author( $comment );
		echo '</div>';

		echo wp_kses( $comment['comments'], 'post' );
	}

	function get_review( $comment = null, $output = OBJECT ) {
	    if ( empty( $comment ) && isset( $GLOBALS['comment'] ) ) {
	        $comment = $GLOBALS['comment'];
	    }

	    if ( $comment instanceof WP_Review ) {
	        $_comment = $comment;
	    } elseif ( is_object( $comment ) ) {
	        $_comment = new WP_Review( $comment );
	    } else {
	        $_comment = WP_Review::get_instance( $comment );
	    }
	 
	    if ( ! $_comment ) {
	        return null;
	    }
	 
	    /**
	     * Fires after a comment is retrieved.
	     *
	     * @since 2.3.0
	     *
	     * @param WP_Comment $_comment Comment data.
	     */
	    $_comment = apply_filters( 'get_comment', $_comment );
	 
	    if ( OBJECT === $output ) {
	        return $_comment;
	    } elseif ( ARRAY_A === $output ) {
	        return $_comment->to_array();
	    } elseif ( ARRAY_N === $output ) {
	        return array_values( $_comment->to_array() );
	    }
	    return $_comment;
	}

	/**
	 * @global string $comment_status
	 *
	 * @param WP_Comment $comment The comment object.
	 */
	public function column_author( $comment ) {
		echo wp_kses( $comment['user_name'], 'post' );
	}

	/**
	 * @param WP_Comment $comment The comment object.
	 */
	public function column_date( $comment ) {
		echo '<div class="submitted-on">';
			echo $comment['posted_date'];
		echo '</div>';
	}

	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_name( $item ) {

		$delete_nonce = wp_create_nonce( 'sp_delete_customer' );

		$title = '<strong>' . $item['name'] . '</strong>';

		$actions = [
			'delete' => sprintf( '<a href="?page=%s&action=%s&customer=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['ID'] ), $delete_nonce )
		];

		return $title . $this->row_actions( $actions );
	}

	/**
	 * Get the name of the default primary column.
	 *
	 * @since 4.3.0
	 *
	 * @return string Name of the default primary column, in this case, 'comment'.
	 */
	protected function get_default_primary_column_name() {
		return 'comment';
	}

	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	// function get_columns() {
	// 	$columns = [
	// 		'cb'      => '<input type="checkbox" />',
	// 		'name'    => __( 'Name', 'sp' ),
	// 		'address' => __( 'Address', 'sp' ),
	// 		'city'    => __( 'City', 'sp' )
	// 	];

	// 	return $columns;
	// }


	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	// public function get_sortable_columns() {
	// 	$sortable_columns = array(
	// 		'name' => array( 'name', true ),
	// 		'city' => array( 'city', false )
	// 	);

	// 	return $sortable_columns;
	// }

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = [
			'bulk-delete' => 'Delete'
		];

		return $actions;
	}

	public function process_bulk_action() {

		//Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'sp_delete_customer' ) ) {
				die( 'Go get a life script kiddies' );
			}
			else {
				self::delete_customer( absint( $_GET['customer'] ) );

		                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
		                // add_query_arg() return the current url
		                wp_redirect( esc_url_raw(add_query_arg()) );
				exit;
			}

		}

		// If the delete bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
		     || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		) {

			$delete_ids = esc_sql( $_POST['bulk-delete'] );

			// loop over the array of record IDs and delete them
			foreach ( $delete_ids as $id ) {
				self::delete_customer( $id );

			}

			// esc_url_raw() is used to prevent converting ampersand in url to "#038;"
		        // add_query_arg() return the current url
		        wp_redirect( esc_url_raw(add_query_arg()) );
			exit;
		}
	}
}

// new \Customers_List();