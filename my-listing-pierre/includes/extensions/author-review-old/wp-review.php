<?php

namespace MyListing\Ext\Author_Review;

final class WP_Review {

	public $review_ID;

	/**
	 * ID of the post the comment is associated with.
	 *
	 * A numeric string, for compatibility reasons.
	 *
	 * @since 4.4.0
	 * @var string
	 */
	public $review_post_ID = 0;

	/**
	 * Comment author name.
	 *
	 * @since 4.4.0
	 * @var string
	 */
	public $review_author = '';

	/**
	 * Comment author email address.
	 *
	 * @since 4.4.0
	 * @var string
	 */
	public $review_author_email = '';

	/**
	 * Comment author URL.
	 *
	 * @since 4.4.0
	 * @var string
	 */
	public $review_author_url = '';

	/**
	 * Comment author IP address (IPv4 format).
	 *
	 * @since 4.4.0
	 * @var string
	 */
	public $review_author_IP = '';

	/**
	 * Comment date in YYYY-MM-DD HH:MM:SS format.
	 *
	 * @since 4.4.0
	 * @var string
	 */
	public $review_date = '0000-00-00 00:00:00';

	/**
	 * Comment GMT date in YYYY-MM-DD HH::MM:SS format.
	 *
	 * @since 4.4.0
	 * @var string
	 */
	public $review_date_gmt = '0000-00-00 00:00:00';

	/**
	 * Comment content.
	 *
	 * @since 4.4.0
	 * @var string
	 */
	public $review_content;

	/**
	 * Comment karma count.
	 *
	 * A numeric string, for compatibility reasons.
	 *
	 * @since 4.4.0
	 * @var string
	 */
	public $review_karma = 0;

	/**
	 * Comment approval status.
	 *
	 * @since 4.4.0
	 * @var string
	 */
	public $review_approved = '1';

	/**
	 * Comment author HTTP user agent.
	 *
	 * @since 4.4.0
	 * @var string
	 */
	public $review_agent = '';

	/**
	 * Comment type.
	 *
	 * @since 4.4.0
	 * @since 5.5.0 Default value changed to `comment`.
	 * @var string
	 */
	public $review_type = 'comment';

	/**
	 * Parent comment ID.
	 *
	 * A numeric string, for compatibility reasons.
	 *
	 * @since 4.4.0
	 * @var string
	 */
	public $review_parent = 0;

	/**
	 * Comment author ID.
	 *
	 * A numeric string, for compatibility reasons.
	 *
	 * @since 4.4.0
	 * @var string
	 */
	public $user_id = 0;

	/**
	 * Comment children.
	 *
	 * @since 4.4.0
	 * @var array
	 */
	protected $children;

	/**
	 * Whether children have been populated for this comment object.
	 *
	 * @since 4.4.0
	 * @var bool
	 */
	protected $populated_children = false;

	/**
	 * Post fields.
	 *
	 * @since 4.4.0
	 * @var array
	 */
	protected $post_fields = array( 'post_author', 'post_date', 'post_date_gmt', 'post_content', 'post_title', 'post_excerpt', 'post_status', 'review_status', 'ping_status', 'post_name', 'to_ping', 'pinged', 'post_modified', 'post_modified_gmt', 'post_content_filtered', 'post_parent', 'guid', 'menu_order', 'post_type', 'post_mime_type', 'review_count' );

	/**
	 * Retrieves a WP_Review instance.
	 *
	 * @since 4.4.0
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @param int $id Comment ID.
	 * @return WP_Review|false Comment object, otherwise false.
	 */
	public static function get_instance( $id ) {
		global $wpdb;

		$review_id = (int) $id;
		if ( ! $review_id ) {
			return false;
		}

		$_comment = wp_cache_get( $review_id, 'mlreviews' );

		if ( ! $_comment ) {
			$_comment = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}mylisting_author_reviews WHERE review_ID = %d LIMIT 1", $review_id ) );

			if ( ! $_comment ) {
				return false;
			}

			wp_cache_add( $_comment->review_ID, $_comment, 'mlreviews' );
		}

		return new WP_Review( $_comment );
	}

	/**
	 * Constructor.
	 *
	 * Populates properties with object vars.
	 *
	 * @since 4.4.0
	 *
	 * @param WP_Review $comment Comment object.
	 */
	public function __construct( $comment ) {
		foreach ( get_object_vars( $comment ) as $key => $value ) {
			$this->$key = $value;
		}
	}

	/**
	 * Convert object to array.
	 *
	 * @since 4.4.0
	 *
	 * @return array Object as array.
	 */
	public function to_array() {
		return get_object_vars( $this );
	}

	public function get_children( $args = array() ) {
		$defaults = array(
			'format'       => 'tree',
			'status'       => 'all',
			'hierarchical' => 'threaded',
			'orderby'      => '',
		);

		$_args           = wp_parse_args( $args, $defaults );
		$_args['parent'] = $this->review_ID;

		if ( is_null( $this->children ) ) {
			if ( $this->populated_children ) {
				$this->children = array();
			} else {
				$this->children = get_comments( $_args );
			}
		}

		if ( 'flat' === $_args['format'] ) {
			$children = array();
			foreach ( $this->children as $child ) {
				$child_args           = $_args;
				$child_args['format'] = 'flat';
				// get_children() resets this value automatically.
				unset( $child_args['parent'] );

				$children = array_merge( $children, array( $child ), $child->get_children( $child_args ) );
			}
		} else {
			$children = $this->children;
		}

		return $children;
	}

	/**
	 * Add a child to the comment.
	 *
	 * Used by `WP_Review_Query` when bulk-filling descendants.
	 *
	 * @since 4.4.0
	 *
	 * @param WP_Review $child Child comment.
	 */
	public function add_child( WP_Review $child ) {
		$this->children[ $child->review_ID ] = $child;
	}

	/**
	 * Get a child comment by ID.
	 *
	 * @since 4.4.0
	 *
	 * @param int $child_id ID of the child.
	 * @return WP_Review|false Returns the comment object if found, otherwise false.
	 */
	public function get_child( $child_id ) {
		if ( isset( $this->children[ $child_id ] ) ) {
			return $this->children[ $child_id ];
		}

		return false;
	}

	/**
	 * Set the 'populated_children' flag.
	 *
	 * This flag is important for ensuring that calling `get_children()` on a childless comment will not trigger
	 * unneeded database queries.
	 *
	 * @since 4.4.0
	 *
	 * @param bool $set Whether the comment's children have already been populated.
	 */
	public function populated_children( $set ) {
		$this->populated_children = (bool) $set;
	}

	/**
	 * Check whether a non-public property is set.
	 *
	 * If `$name` matches a post field, the comment post will be loaded and the post's value checked.
	 *
	 * @since 4.4.0
	 *
	 * @param string $name Property name.
	 * @return bool
	 */
	public function __isset( $name ) {
		if ( in_array( $name, $this->post_fields, true ) && 0 !== (int) $this->review_author_ID ) {
			$post = get_post( $this->review_author_ID );
			return property_exists( $post, $name );
		}
	}

	/**
	 * Magic getter.
	 *
	 * If `$name` matches a post field, the comment post will be loaded and the post's value returned.
	 *
	 * @since 4.4.0
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function __get( $name ) {
		if ( in_array( $name, $this->post_fields, true ) ) {
			$post = get_post( $this->review_author_ID );
			return $post->$name;
		}
	}
}
