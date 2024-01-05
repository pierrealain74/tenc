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
		if ( isset( $_REQUEST['delete_all'] ) || isset( $_REQUEST['delete_all2'] ) ) {
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

		if ( ! $post_id ) {
			/* translators: Column name or table row header. */
			$columns['response'] = __( 'In response to' );
		}

		$columns['date'] = _x( 'Submitted on', 'column name' );

		return $columns;
	}

	/**
	 * @param WP_Comment $comment     The comment object.
	 * @param string     $column_name The custom column's name.
	 */
	public function column_default( $item, $column_name ) {
// print_r( [ $column_name, $item ] );exit();
		switch ( $column_name ) {
			case 'author':
				// return $item[ $column_name ];
			default:
				// return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}

	/**
	 * @global WP_Post    $post    Global post object.
	 * @global WP_Comment $comment Global comment object.
	 *
	 * @param WP_Comment $item
	 */
	public function single_row( $item ) {
		global $post, $comment;

		$comment = $item;

		$the_comment_class = 'approved';

		if ( ! $the_comment_class ) {
			$the_comment_class = '';
		}

		$the_comment_class = implode( ' ', get_comment_class( $the_comment_class, $comment, $comment[ 'review_author_ID' ] ) );

		if ( $comment[ 'review_author_ID' ] > 0 ) {
			$post = get_post( $comment[ 'review_author_ID' ] );
		}

		$this->user_can = current_user_can( 'edit_comment', $comment['review_ID'] );
		$comment_id = $comment['review_ID'];

		echo "<tr id='comment-$comment_id' class='$the_comment_class'>";
		$this->single_row_columns( $comment );
		echo "</tr>\n";

		unset( $GLOBALS['post'], $GLOBALS['comment'] );
	}

	/**
	 * Generate and display row actions links.
	 *
	 * @since 4.3.0
	 *
	 * @global string $comment_status Status for the current listed comments.
	 *
	 * @param WP_Comment $comment     The comment object.
	 * @param string     $column_name Current column name.
	 * @param string     $primary     Primary column name.
	 * @return string Row actions output for comments. An empty string
	 *                if the current column is not the primary column,
	 *                or if the current user cannot edit the comment.
	 */
	protected function handle_row_actions( $comment, $column_name, $primary ) {
		global $comment_status;

		if ( $primary !== $column_name ) {
			return '';
		}

		if ( ! $this->user_can ) {
			return '';
		}

		$the_comment_status = 'approved';
		$comment_id = $comment['review_ID'];

		$out = '';

		$del_nonce     = esc_html( '_wpnonce=' . wp_create_nonce( "delete-comment_$comment_id" ) );
		$approve_nonce = esc_html( '_wpnonce=' . wp_create_nonce( "approve-comment_$comment_id" ) );

		$url = "admin.php?page=wp_list_table_class&c=$comment_id";

		$approve_url   = esc_url( $url . "&action=approvecomment&$approve_nonce" );
		$unapprove_url = esc_url( $url . "&action=unapprovecomment&$approve_nonce" );
		$spam_url      = esc_url( $url . "&action=spamcomment&$del_nonce" );
		$unspam_url    = esc_url( $url . "&action=unspamcomment&$del_nonce" );
		$trash_url     = esc_url( $url . "&action=trashcomment&$del_nonce" );
		$untrash_url   = esc_url( $url . "&action=untrashcomment&$del_nonce" );
		$delete_url    = esc_url( $url . "&action=deletecomment&$del_nonce" );

		// Preorder it: Approve | Reply | Quick Edit | Edit | Spam | Trash.
		$actions = array(
			'approve'   => '',
			'unapprove' => '',
			'reply'     => '',
			'quickedit' => '',
			'edit'      => '',
			'spam'      => '',
			'unspam'    => '',
			'trash'     => '',
			'untrash'   => '',
			'delete'    => '',
		);

		// Not looking at all comments.
		if ( $comment_status && 'all' !== $comment_status ) {
			if ( 'approved' === $the_comment_status ) {
				$actions['unapprove'] = sprintf(
					'<a href="%s" data-wp-lists="%s" class="vim-u vim-destructive aria-button-if-js" aria-label="%s">%s</a>',
					$unapprove_url,
					"delete:the-comment-list:comment-{$comment->comment_ID}:e7e7d3:action=dim-comment&amp;new=unapproved",
					esc_attr__( 'Unapprove this comment' ),
					__( 'Unapprove' )
				);
			} elseif ( 'unapproved' === $the_comment_status ) {
				$actions['approve'] = sprintf(
					'<a href="%s" data-wp-lists="%s" class="vim-a vim-destructive aria-button-if-js" aria-label="%s">%s</a>',
					$approve_url,
					"delete:the-comment-list:comment-{$comment_id}:e7e7d3:action=dim-comment&amp;new=approved",
					esc_attr__( 'Approve this comment' ),
					__( 'Approve' )
				);
			}
		} else {
			$actions['approve'] = sprintf(
				'<a href="%s" data-wp-lists="%s" class="vim-a aria-button-if-js" aria-label="%s">%s</a>',
				$approve_url,
				"dim:the-comment-list:comment-{$comment_id}:unapproved:e7e7d3:e7e7d3:new=approved",
				esc_attr__( 'Approve this comment' ),
				__( 'Approve' )
			);

			$actions['unapprove'] = sprintf(
				'<a href="%s" data-wp-lists="%s" class="vim-u aria-button-if-js" aria-label="%s">%s</a>',
				$unapprove_url,
				"dim:the-comment-list:comment-{$comment_id}:unapproved:e7e7d3:e7e7d3:new=unapproved",
				esc_attr__( 'Unapprove this comment' ),
				__( 'Unapprove' )
			);
		}

		if ( 'spam' !== $the_comment_status ) {
			$actions['spam'] = sprintf(
				'<a href="%s" data-wp-lists="%s" class="vim-s vim-destructive aria-button-if-js" aria-label="%s">%s</a>',
				$spam_url,
				"delete:the-comment-list:comment-{$comment_id}::spam=1",
				esc_attr__( 'Mark this comment as spam' ),
				/* translators: "Mark as spam" link. */
				_x( 'Spam', 'verb' )
			);
		} elseif ( 'spam' === $the_comment_status ) {
			$actions['unspam'] = sprintf(
				'<a href="%s" data-wp-lists="%s" class="vim-z vim-destructive aria-button-if-js" aria-label="%s">%s</a>',
				$unspam_url,
				"delete:the-comment-list:comment-{$comment_id}:66cc66:unspam=1",
				esc_attr__( 'Restore this comment from the spam' ),
				_x( 'Not Spam', 'comment' )
			);
		}

		if ( 'trash' === $the_comment_status ) {
			$actions['untrash'] = sprintf(
				'<a href="%s" data-wp-lists="%s" class="vim-z vim-destructive aria-button-if-js" aria-label="%s">%s</a>',
				$untrash_url,
				"delete:the-comment-list:comment-{$comment_id}:66cc66:untrash=1",
				esc_attr__( 'Restore this comment from the Trash' ),
				__( 'Restore' )
			);
		}

		if ( 'spam' === $the_comment_status || 'trash' === $the_comment_status || ! EMPTY_TRASH_DAYS ) {
			$actions['delete'] = sprintf(
				'<a href="%s" data-wp-lists="%s" class="delete vim-d vim-destructive aria-button-if-js" aria-label="%s">%s</a>',
				$delete_url,
				"delete:the-comment-list:comment-{$comment_id}::delete=1",
				esc_attr__( 'Delete this comment permanently' ),
				__( 'Delete Permanently' )
			);
		} else {
			$actions['trash'] = sprintf(
				'<a href="%s" data-wp-lists="%s" class="delete vim-d vim-destructive aria-button-if-js" aria-label="%s">%s</a>',
				$trash_url,
				"delete:the-comment-list:comment-{$comment_id}::trash=1",
				esc_attr__( 'Move this comment to the Trash' ),
				_x( 'Trash', 'verb' )
			);
		}

		if ( 'spam' !== $the_comment_status && 'trash' !== $the_comment_status ) {
			$actions['edit'] = sprintf(
				'<a href="%s" aria-label="%s">%s</a>',
				"admin.php?page=wp_list_table_class&action=editcomment&amp;c={$comment_id}",
				esc_attr__( 'Edit this comment' ),
				__( 'Edit' )
			);

			$format = '<button type="button" data-comment-id="%d" data-post-id="%d" data-action="%s" class="%s button-link" aria-expanded="false" aria-label="%s">%s</button>';

			$actions['quickedit'] = sprintf(
				$format,
				$comment_id,
				$comment[ 'review_author_ID' ],
				'edit',
				'vim-q comment-inline',
				esc_attr__( 'Quick edit this comment inline' ),
				__( 'Quick&nbsp;Edit' )
			);

			$actions['reply'] = sprintf(
				$format,
				$comment_id,
				$comment[ 'review_author_ID' ],
				'replyto',
				'vim-r comment-inline',
				esc_attr__( 'Reply to this comment' ),
				__( 'Reply' )
			);
		}

		/** This filter is documented in wp-admin/includes/dashboard.php */
		$actions = apply_filters( 'comment_row_actions', array_filter( $actions ), $comment );

		$always_visible = false;

		$mode = get_user_setting( 'posts_list_mode', 'list' );

		if ( 'excerpt' === $mode ) {
			$always_visible = true;
		}

		$out .= '<div class="' . ( $always_visible ? 'row-actions visible' : 'row-actions' ) . '">';

		$i = 0;

		foreach ( $actions as $action => $link ) {
			++$i;

			if ( ( ( 'approve' === $action || 'unapprove' === $action ) && 2 === $i )
				|| 1 === $i
			) {
				$sep = '';
			} else {
				$sep = ' | ';
			}

			// Reply and quickedit need a hide-if-no-js span when not added with Ajax.
			if ( ( 'reply' === $action || 'quickedit' === $action ) && ! wp_doing_ajax() ) {
				$action .= ' hide-if-no-js';
			} elseif ( ( 'untrash' === $action && 'trash' === $the_comment_status )
				|| ( 'unspam' === $action && 'spam' === $the_comment_status )
			) {
				if ( '1' === get_comment_meta( $comment_id, '_wp_trash_meta_status', true ) ) {
					$action .= ' approve';
				} else {
					$action .= ' unapprove';
				}
			}

			$out .= "<span class='$action'>$sep$link</span>";
		}

		$out .= '</div>';

		$out .= '<button type="button" class="toggle-row"><span class="screen-reader-text">' . __( 'Show more details' ) . '</span></button>';

		return $out;
	}

	/**
	 * @param WP_Comment $comment The comment object.
	 */
	public function column_cb( $comment ) {
		if ( $this->user_can ) {
			?>
		<label class="screen-reader-text" for="cb-select-<?php echo $comment['review_ID']; ?>"><?php _e( 'Select comment' ); ?></label>
		<input id="cb-select-<?php echo $comment['review_ID']; ?>" type="checkbox" name="delete_comments[]" value="<?php echo $comment['review_ID']; ?>" />
			<?php
		}
	}


	/**
	 * @param WP_Comment $comment The comment object.
	 */
	public function column_comment( $comment ) {
		echo '<div class="comment-author">';
			$this->column_author( $comment );
		echo '</div>';

		if ( $comment['review_parent'] ) {
			$parent = $this->get_review( $comment['review_parent'] );

			if ( $parent ) {
				$parent_link = esc_url( get_comment_link( $parent ) );
				$name        = $this->ml_get_comment_author( $parent );
				printf(
					/* translators: %s: Comment link. */
					__( 'In reply to %s.' ),
					'<a href="' . $parent_link . '">' . $name . '</a>'
				);
			}
		}

		echo $comment['review_content'];

		if ( $this->user_can ) {
			/** This filter is documented in wp-admin/includes/comment.php */
			$comment_content = apply_filters( 'comment_edit_pre', $comment['review_content'] );
			?>
		<div id="inline-<?php echo $comment->comment_ID; ?>" class="hidden">
			<textarea class="comment" rows="1" cols="1"><?php echo esc_textarea( $comment_content ); ?></textarea>
			<div class="author-email"><?php echo esc_attr( $comment['review_author_email' ] ); ?></div>
			<div class="author"><?php echo esc_attr( $comment['review_author' ] ); ?></div>
			<div class="author-url"><?php echo esc_attr( $comment['review_author_url' ] ); ?></div>
			<div class="comment_status"><?php echo $comment['review_approved' ]; ?></div>
		</div>
			<?php
		}
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

	function get_comment_author_url( $comment_ID = 0 ) {
	    $comment = $this->get_review( $comment_ID );
	    $url     = '';
	    $id      = 0;
	 
	    if ( ! empty( $comment ) ) {
	        $author_url = ( 'http://' === $comment->comment_author_url ) ? '' : $comment->comment_author_url;
	        $url        = esc_url( $author_url, array( 'http', 'https' ) );
	        $id         = $comment->comment_ID;
	    }
	 
	    /**
	     * Filters the comment author's URL.
	     *
	     * @since 1.5.0
	     * @since 4.1.0 The `$comment_ID` and `$comment` parameters were added.
	     *
	     * @param string          $url        The comment author's URL, or an empty string.
	     * @param string|int      $comment_ID The comment ID as a numeric string, or 0 if not found.
	     * @param WP_Comment|null $comment    The comment object, or null if not found.
	     */
	    return apply_filters( 'get_comment_author_url', $url, $id, $comment );
	}

	function ml_get_comment_author( $comment_ID = 0 ) {
	    $comment = $this->get_review( $comment_ID );

	    if ( empty( $comment->review_author ) ) {
	        $user = $comment->user_id ? get_userdata( $comment->user_id ) : false;
	        if ( $user ) {
	            $author = $user->display_name;
	        } else {
	            $author = __( 'Anonymous' );
	        }
	    } else {
	        $author = $comment->review_author;
	    }
	 
	    /**
	     * Filters the returned comment author name.
	     *
	     * @since 1.5.0
	     * @since 4.1.0 The `$comment_ID` and `$comment` parameters were added.
	     *
	     * @param string     $author     The comment author's username.
	     * @param string     $comment_ID The comment ID as a numeric string.
	     * @param WP_Comment $comment    The comment object.
	     */
	    return apply_filters( 'get_comment_author', $author, $comment->comment_ID, $comment );
	}

	function ml_comment_author( $comment_ID = 0 ) {
	    $comment = $this->get_review( $comment_ID );
	    $author  = $this->ml_get_comment_author( $comment );
	 
	    /**
	     * Filters the comment author's name for display.
	     *
	     * @since 1.2.0
	     * @since 4.1.0 The `$comment_ID` parameter was added.
	     *
	     * @param string $author     The comment author's username.
	     * @param string $comment_ID The comment ID as a numeric string.
	     */
	    echo apply_filters( 'comment_author', $author, $comment->comment_ID );
	}

	/**
	 * @global string $comment_status
	 *
	 * @param WP_Comment $comment The comment object.
	 */
	public function column_author( $comment ) {
		global $comment_status;

		$author_url = $this->get_comment_author_url( $comment );

		$author_url_display = untrailingslashit( preg_replace( '|^http(s)?://(www\.)?|i', '', $author_url ) );

		if ( strlen( $author_url_display ) > 50 ) {
			$author_url_display = wp_html_excerpt( $author_url_display, 49, '&hellip;' );
		}

		echo '<strong>';
		$this->ml_comment_author( $comment );
		echo '</strong><br />';

		if ( ! empty( $author_url_display ) ) {
			printf( '<a href="%s">%s</a><br />', esc_url( $author_url ), esc_html( $author_url_display ) );
		}

		if ( $this->user_can ) {
			if ( ! empty( $comment->comment_author_email ) ) {
				/** This filter is documented in wp-includes/comment-template.php */
				$email = apply_filters( 'comment_email', $comment->comment_author_email, $comment );

				if ( ! empty( $email ) && '@' !== $email ) {
					printf( '<a href="%1$s">%2$s</a><br />', esc_url( 'mailto:' . $email ), esc_html( $email ) );
				}
			}

			$author_ip = $this->ml_get_comment_author_IP( $comment );

			if ( $author_ip ) {
				$author_ip_url = add_query_arg(
					array(
						's'    => $author_ip,
						'mode' => 'detail',
					),
					admin_url( 'edit-comments.php' )
				);

				if ( 'spam' === $comment_status ) {
					$author_ip_url = add_query_arg( 'comment_status', 'spam', $author_ip_url );
				}

				printf( '<a href="%1$s">%2$s</a>', esc_url( $author_ip_url ), esc_html( $author_ip ) );
			}
		}
	}

	/**
	 * @param WP_Comment $comment The comment object.
	 */
	public function column_date( $comment ) {
		$submitted = sprintf(
			/* translators: 1: Comment date, 2: Comment time. */
			__( '%1$s at %2$s' ),
			/* translators: Comment date format. See https://www.php.net/manual/datetime.format.php */
			get_comment_date( __( 'Y/m/d' ), $comment ),
			/* translators: Comment time format. See https://www.php.net/manual/datetime.format.php */
			get_comment_date( __( 'g:i a' ), $comment )
		);

		echo '<div class="submitted-on">';

		if ( 'approved' === wp_get_comment_status( $comment ) && ! empty( $comment->comment_post_ID ) ) {
			printf(
				'<a href="%s">%s</a>',
				esc_url( get_comment_link( $comment ) ),
				$submitted
			);
		} else {
			echo $submitted;
		}

		echo '</div>';
	}

	/**
	 * @param WP_Comment $comment The comment object.
	 */
	public function column_response( $comment ) {
		$post = get_post();

		if ( ! $post ) {
			return;
		}

		// if ( isset( $this->pending_count[ $post->ID ] ) ) {
		// 	$pending_comments = $this->pending_count[ $post->ID ];
		// } else {
		// 	$_pending_count_temp              = get_pending_comments_num( array( $post->ID ) );
		// 	$pending_comments                 = $_pending_count_temp[ $post->ID ];
		// 	$this->pending_count[ $post->ID ] = $pending_comments;
		// }

		// if ( current_user_can( 'edit_post', $post->ID ) ) {
		// 	$post_link  = "<a href='" . get_edit_post_link( $post->ID ) . "' class='comments-edit-item-link'>";
		// 	$post_link .= esc_html( get_the_title( $post->ID ) ) . '</a>';
		// } else {
		// 	$post_link = esc_html( get_the_title( $post->ID ) );
		// }

		// echo '<div class="response-links">';

		// if ( 'attachment' === $post->post_type ) {
		// 	$thumb = wp_get_attachment_image( $post->ID, array( 80, 60 ), true );
		// 	if ( $thumb ) {
		// 		echo $thumb;
		// 	}
		// }

		// echo $post_link;

		// $post_type_object = get_post_type_object( $post->post_type );
		// echo "<a href='" . get_permalink( $post->ID ) . "' class='comments-view-item-link'>" . $post_type_object->labels->view_item . '</a>';

		// echo '<span class="post-com-count-wrapper post-com-count-', $post->ID, '">';
		// $this->comments_bubble( $post->ID, $pending_comments );
		// echo '</span> ';

		// echo '</div>';
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
	 * @return array
	 */
	protected function get_sortable_columns() {
		return array(
			'author'   => 'comment_author',
			'response' => 'review_author_ID',
			'date'     => 'comment_date',
		);
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