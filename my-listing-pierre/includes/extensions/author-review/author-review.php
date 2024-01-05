<?php

namespace MyListing\Ext\Author_Review;

class Author_Review {

	public static function boot() {
    	new self;

    	add_action( 'wp_ajax_author_review_request', [ __CLASS__, 'handle_author_review_request' ] );
		add_action( 'wp_ajax_nopriv_author_review_request', [ __CLASS__, 'handle_author_review_request' ] );
    }

	public function __construct() {
		require_once get_stylesheet_directory() . '/includes/extensions/author-review/customers-list.php';

		$this->table_version = '0.1';
		$this->current_version = get_option( 'mylisting_author_review_table_version' );

		// Setup DB.
		$this->setup_tables();

		// add_filter( 'set-screen-option', array( $this, 'add_query_vars' ), 10, 3 );
		// add_action( 'admin_menu', [ $this, 'plugin_menu' ] );
	}

	public static function handle_author_review_request() {
		check_ajax_referer( 'c27_ajax_nonce', 'security' );

		try {
			if ( empty( $_POST['user_id'] ) || empty( $_POST['author_id'] ) || empty( $_POST['comment'] ) ) {
				throw new \Exception( _x( 'Please Try Again.', 'Comments list', 'my-listing' ) );
			}

			$is_already = self::is_already_review( $_POST );

			if ( $is_already ) {
				throw new \Exception( _x( 'Duplicate comment detected; it looks as though you’ve already said that!', 'my-listing' ) );
			}

			$review_id = self::wp_insert_review( $_POST );

			if ( empty( $review_id ) ) {
				throw new \Exception( _x( 'Please Try Again.', 'Comments list', 'my-listing' ) );
			}

			$categories = self::get_categories();
			$ratings = array();
			$ratings_total = 0;
			if ( $categories ) {
				foreach( $categories as $id => $category ) {
					$submitted_rating = self::sanitize_rating( isset( $_POST[ $category['id'] . '_star_rating' ] ) ? intval( $_POST[ $category['id'] . '_star_rating' ] ) : 0 );
					if ( $submitted_rating ) {
						$ratings[ $category['id'] ] = $submitted_rating;
						$ratings_total += $submitted_rating;
					}
				}
			}

			if ( $ratings ) {
				self::update_metadata( $review_id, '_case27_ratings', $ratings );
				self::update_metadata( $review_id, '_case27_post_rating', self::sanitize_rating( $ratings_total / count( $ratings ) ) );
			} else {
				delete_metadata( 'mylisting_author_review', $review_id, '_case27_ratings' );
				delete_metadata( 'mylisting_author_review', $review_id, '_case27_post_rating' );
			}

			update_user_meta( $_POST['author_id'], '_case27_author_average_rating', self::get_user_rating( $_POST['author_id'] ) );

			wp_send_json( [
				'status' => 'success',
				'message' => 'Your review has been submitted',
			] );
		} catch ( \Exception $e ) {
			wp_send_json( [
				'status' => 'error',
				'message' => $e->getMessage(),
			] );
		}
	}

	public static function get_metadata( $object_id, $meta_key, $single = true ) {
		global $wpdb;

		if ( ! is_numeric( $object_id ) ) {
			return false;
		}

		$object_id = absint( $object_id );
		if ( ! $object_id ) {
			return false;
		}

		$table = $wpdb->prefix . 'mylisting_author_reviewmeta';

		if ( ! $table ) {
			return false;
		}

		$column    = sanitize_key( 'review_id' );
		$id_column = 'meta_value';

		// expected_slashed ($meta_key)
		$raw_meta_key = $meta_key;
		$meta_key     = wp_unslash( $meta_key );

		$meta_ids = $wpdb->get_col( $wpdb->prepare( "SELECT $id_column FROM $table WHERE meta_key = %s AND $column = %d", $meta_key, $object_id ) );

		if ( ! $meta_ids ) {
			return null;
		}

		return maybe_unserialize( $meta_ids[0] );
	}

	public static function update_metadata( $object_id, $meta_key, $meta_value, $prev_value = '' ) {
		global $wpdb;

		if ( ! $meta_key || ! is_numeric( $object_id ) ) {
			return false;
		}

		$object_id = absint( $object_id );
		if ( ! $object_id ) {
			return false;
		}

		$table = $wpdb->prefix . 'mylisting_author_reviewmeta';

		if ( ! $table ) {
			return false;
		}

		$meta_type = 'mylisting_author_reviews';

		$column    = sanitize_key( 'review_id' );
		$id_column = 'meta_id';

		// expected_slashed ($meta_key)
		$raw_meta_key = $meta_key;
		$meta_key     = wp_unslash( $meta_key );
		$passed_value = $meta_value;
		$meta_value   = wp_unslash( $meta_value );
		$meta_value   = sanitize_meta( $meta_key, $meta_value, $meta_type );

		// Compare existing value to new value if no prev value given and the key exists only once.
		if ( empty( $prev_value ) ) {
			$old_value = get_metadata_raw( $meta_type, $object_id, $meta_key );
			if ( is_countable( $old_value ) && count( $old_value ) === 1 ) {
				if ( $old_value[0] === $meta_value ) {
					return false;
				}
			}
		}

		$meta_ids = $wpdb->get_col( $wpdb->prepare( "SELECT $id_column FROM $table WHERE meta_key = %s AND $column = %d", $meta_key, $object_id ) );
		if ( empty( $meta_ids ) ) {
			$_meta_value = $meta_value;
		    $meta_value  = maybe_serialize( $meta_value );

			$result = $wpdb->insert(
		        $table,
		        array(
		            $column      => $object_id,
		            'meta_key'   => $meta_key,
		            'meta_value' => $meta_value,
		        )
		    );
		 
		    if ( ! $result ) {
		        return false;
		    }

		    return (int) $wpdb->insert_id;
		}

		$_meta_value = $meta_value;
		$meta_value  = maybe_serialize( $meta_value );

		$data  = compact( 'meta_value' );
		$where = array(
			$column    => $object_id,
			'meta_key' => $meta_key,
		);

		if ( ! empty( $prev_value ) ) {
			$prev_value          = maybe_serialize( $prev_value );
			$where['meta_value'] = $prev_value;
		}

		$result = $wpdb->update( $table, $data, $where );
		if ( ! $result ) {
			return false;
		}

		wp_cache_delete( $object_id, $meta_type . '_meta' );

		return true;
	}
	public static function get_user_rating( $author_id ) {
		global $wpdb;

		$table = $wpdb->prefix . 'mylisting_author_reviews';
		$table_meta = $wpdb->prefix . 'mylisting_author_reviewmeta';

		$rating = (float) $wpdb->get_var( $wpdb->prepare("
			SELECT AVG(meta_value) AS avg_rating
			FROM $table_meta
			WHERE meta_key = '_case27_post_rating'
			AND review_id IN (
				SELECT review_id
				FROM $table
				WHERE author_id = %s
				AND approved_status = 1
			)", $author_id ) );

		// Sanitize.
		$rating = self::sanitize_rating( $rating );

		// Count listing reviews.
		self::count_reviews( $author_id );

		// Round it.
		if ( $rating ) {
			return round( $rating, 1 );
		}

		return 0;
	}

	/**
	 * Count the amount of listing reviews (first level comments),
	 * and store it in listing meta for more efficiency.
	 *
	 * @since 1.6.3
	 * @param int $listing_id Post ID.
	 */
	public static function count_reviews( $author_id ) {
		global $wpdb;

		$table = $wpdb->prefix . 'mylisting_author_reviews';

		$count = (int) $wpdb->get_var( $wpdb->prepare( "
			SELECT COUNT(author_id) AS count
			FROM $table
			WHERE approved_status = 1
			AND author_id = %s
		", $author_id ) );

		if ( $count < 1 ) {
			$count = 0;
		}

		// Update listing meta.
		update_user_meta( $author_id, '_case27_review_count', $count );

		return $count;
	}

	public static function is_already_review( $commentdata ) {
		global $wpdb;
	    $data = wp_unslash( $commentdata );

	    $dupe = $wpdb->prepare(
			"SELECT id FROM {$wpdb->prefix}mylisting_author_reviews WHERE author_id = %d AND user_id = %d LIMIT 1",
			wp_unslash( $commentdata['author_id'] ),
			wp_unslash( $commentdata['user_id'] )
		);

		$dupe_id = $wpdb->get_var( $dupe );
		
		if ( $dupe_id ) {
			return true;
		}

		return false;
	}

	public static function wp_insert_review( $commentdata ) {
	    global $wpdb;
	    $data = wp_unslash( $commentdata );

	    $comment_author       = ! isset( $data['author_id'] ) ? '' : $data['author_id'];
	    $user_email = ! isset( $data['user_email'] ) ? '' : $data['user_email'];
	 
	    $posted_date     = ! isset( $data['posted_date'] ) ? current_time( 'mysql' ) : $data['posted_date'];
	    $posted_date_gmt = ! isset( $data['posted_date_gmt'] ) ? get_gmt_from_date( $posted_date ) : $data['posted_date_gmt'];
	 
	    $author_id  = ! isset( $data['author_id'] ) ? 0 : $data['author_id'];
	    $comments  = ! isset( $data['comment'] ) ? '' : $data['comment'];
	    $approved_status = ! isset( $data['approved_status'] ) ? 1 : $data['approved_status'];
	 
	    $user_id = ! isset( $data['user_id'] ) ? 0 : $data['user_id'];

	    $compacted = compact( 'author_id', 'user_email', 'posted_date', 'posted_date_gmt', 'comments', 'approved_status', 'user_id' );

	    if ( ! $wpdb->insert( $wpdb->prefix . 'mylisting_author_reviews', $compacted ) ) {
	        return false;
	    }

	    $id = (int) $wpdb->insert_id;
	 
	    clean_comment_cache( $id );
	 
	    return $id;
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

	public static function review_form( $args = [] ) {
		$commenter     = wp_get_current_commenter();
	    $user          = wp_get_current_user();
	    $user_identity = $user->exists() ? $user->display_name : '';
	 
	    $args = wp_parse_args( $args );
	    if ( ! isset( $args['format'] ) ) {
	        $args['format'] = current_theme_supports( 'html5', 'comment-form' ) ? 'html5' : 'xhtml';
	    }
	 
	    $req   = get_option( 'require_name_email' );
	    $html5 = 'html5' === $args['format'];
	 
	    // Define attributes in HTML5 or XHTML syntax.
	    $required_attribute = ( $html5 ? ' required' : ' required="required"' );
	    $checked_attribute  = ( $html5 ? ' checked' : ' checked="checked"' );
	 
	    // Identify required fields visually.
	    $required_indicator = ' <span class="required" aria-hidden="true">*</span>';
	 	
	 	$required_text = sprintf(
	        /* translators: %s: Asterisk symbol (*). */
	        ' <span class="required-field-message" aria-hidden="true">' . __( 'Required fields are marked %s' ) . '</span>',
	        trim( $required_indicator )
	    );

	    $fields = array(
	        'author' => sprintf(
	            '<p class="comment-form-author">%s %s</p>',
	            sprintf(
	                '<label for="author">%s%s</label>',
	                __( 'Name' ),
	                ( $req ? $required_indicator : '' )
	            ),
	            sprintf(
	                '<input id="author" name="author" type="text" value="%s" size="30" maxlength="245"%s />',
	                esc_attr( $commenter['comment_author'] ),
	                ( $req ? $required_attribute : '' )
	            )
	        ),
	        'email'  => sprintf(
	            '<p class="comment-form-email">%s %s</p>',
	            sprintf(
	                '<label for="email">%s%s</label>',
	                __( 'Email' ),
	                ( $req ? $required_indicator : '' )
	            ),
	            sprintf(
	                '<input id="email" name="email" %s value="%s" size="30" maxlength="100" aria-describedby="email-notes"%s />',
	                ( $html5 ? 'type="email"' : 'type="text"' ),
	                ( isset( $commenter['user_email'] ) ? esc_attr( $commenter['user_email'] ) : '' ),
	                ( $req ? $required_attribute : '' )
	            )
	        )
	    );

	    $defaults = array(
	        'fields'               => $fields,
	        'comment_field'        => sprintf(
	            '<p class="comment-form-comment">%s %s</p>',
	            sprintf(
	                '<label for="comment">%s%s</label>',
	                _x( 'Comment', 'noun' ),
	                $required_indicator
	            ),
	            '<textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525"' . $required_attribute . '></textarea>'
	        ),
	        'must_log_in'          => sprintf(
	            '<p class="must-log-in">%s</p>',
	            sprintf(
	                /* translators: %s: Login URL. */
	                __( 'You must be <a href="%s">logged in</a> to post a comment.' ),
	                /** This filter is documented in wp-includes/link-template.php */
	                wp_login_url()
	            )
	        ),
	        'logged_in_as'         => sprintf(
	            '<p class="logged-in-as">%s%s</p>',
	            sprintf(
	                /* translators: 1: Edit user link, 2: Accessibility text, 3: User name, 4: Logout URL. */
	                __( '<a href="%1$s" aria-label="%2$s">Logged in as %3$s</a>. <a href="%4$s">Log out?</a>' ),
	                get_edit_user_link(),
	                /* translators: %s: User name. */
	                esc_attr( sprintf( __( 'Logged in as %s. Edit your profile.' ), $user_identity ) ),
	                $user_identity,
	                /** This filter is documented in wp-includes/link-template.php */
	                wp_logout_url()
	            ),
	            $required_text
	        ),
	        'comment_notes_before' => sprintf(
	            '<p class="comment-notes">%s%s</p>',
	            sprintf(
	                '<span id="email-notes">%s</span>',
	                __( 'Your email address will not be published.' )
	            ),
	            $required_text
	        ),
	        'comment_notes_after'  => '',
	        'action'               => '#',
	        'id_form'              => 'commentform',
	        'id_submit'            => 'submit',
	        'class_container'      => 'comment-respond',
	        'class_form'           => 'comment-form',
	        'class_submit'         => 'submit',
	        'name_submit'          => 'submit',
	        'title_reply'          => __( 'Leave a Reply' ),
	        /* translators: %s: Author of the comment being replied to. */
	        'title_reply_to'       => __( 'Leave a Reply to %s' ),
	        'title_reply_before'   => '<h3 id="reply-title" class="comment-reply-title">',
	        'title_reply_after'    => '</h3>',
	        'cancel_reply_before'  => ' <small>',
	        'cancel_reply_after'   => '</small>',
	        'cancel_reply_link'    => __( 'Cancel reply' ),
	        'label_submit'         => __( 'Post Comment' ),
	        'submit_button'        => '<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" />',
	        'submit_field'         => '<p class="form-submit">%1$s %2$s</p>',
	        'format'               => 'xhtml',
	    );
	 
	    /**
	     * Filters the comment form default arguments.
	     *
	     * Use {@see 'comment_form_default_fields'} to filter the comment fields.
	     *
	     * @since 3.0.0
	     *
	     * @param array $defaults The default comment form arguments.
	     */
	    $args = wp_parse_args( $args, apply_filters( 'review_form_defaults', $defaults ) );
	 
	    // Ensure that the filtered arguments contain all required default values.
	    $args = array_merge( $defaults, $args );
	 
	    // Remove `aria-describedby` from the email field if there's no associated description.
	    if ( isset( $args['fields']['email'] ) && false === strpos( $args['comment_notes_before'], 'id="email-notes"' ) ) {
	        $args['fields']['email'] = str_replace(
	            ' aria-describedby="email-notes"',
	            '',
	            $args['fields']['email']
	        );
	    }

	    ?>
	    	<div id="respond" class="<?php echo esc_attr( $args['class_container'] ); ?>">
		        <?php
		        echo $args['title_reply_before'];
		 
		        comment_form_title( $args['title_reply'], $args['title_reply_to'] );
		 
		        if ( get_option( 'thread_comments' ) ) {
		            echo $args['cancel_reply_before'];
		 
		            cancel_comment_reply_link( $args['cancel_reply_link'] );
		 
		            echo $args['cancel_reply_after'];
		        }
		 
		        echo $args['title_reply_after'];
		 
		        if ( get_option( 'comment_registration' ) && ! is_user_logged_in() ) :
		 
		            echo $args['must_log_in'];
		            /**
		             * Fires after the HTML-formatted 'must log in after' message in the comment form.
		             *
		             * @since 3.0.0
		             */
		            do_action( 'comment_form_must_log_in_after' );
		 
		        else :
		 
		            printf(
		                '<form action="%s" method="post" id="%s" class="%s"%s>',
		                esc_url( $args['action'] ),
		                esc_attr( $args['id_form'] ),
		                esc_attr( $args['class_form'] ),
		                ( $html5 ? ' novalidate' : '' )
		            );
		 
		            /**
		             * Fires at the top of the comment form, inside the form tag.
		             *
		             * @since 3.0.0
		             */
		            do_action( 'comment_form_top' );
		 
		            if ( is_user_logged_in() ) :
		 
		                /**
		                 * Filters the 'logged in' message for the comment form for display.
		                 *
		                 * @since 3.0.0
		                 *
		                 * @param string $args_logged_in The logged-in-as HTML-formatted message.
		                 * @param array  $commenter      An array containing the comment author's
		                 *                               username, email, and URL.
		                 * @param string $user_identity  If the commenter is a registered user,
		                 *                               the display name, blank otherwise.
		                 */
		                echo apply_filters( 'ml_review_form_logged_in', $args['logged_in_as'], $commenter, $user_identity );
		 
		                /**
		                 * Fires after the is_user_logged_in() check in the comment form.
		                 *
		                 * @since 3.0.0
		                 *
		                 * @param array  $commenter     An array containing the comment author's
		                 *                              username, email, and URL.
		                 * @param string $user_identity If the commenter is a registered user,
		                 *                              the display name, blank otherwise.
		                 */
		                do_action( 'comment_form_logged_in_after', $commenter, $user_identity );
		 
		            else :
		 
		                echo $args['comment_notes_before'];
		 
		            endif;
		 
		            // Prepare an array of all fields, including the textarea.
		            $comment_fields = array( 'comment' => $args['comment_field'] ) + (array) $args['fields'];
		 
		            /**
		             * Filters the comment form fields, including the textarea.
		             *
		             * @since 4.4.0
		             *
		             * @param array $comment_fields The comment fields.
		             */
		            $comment_fields = apply_filters( 'ml_review_form_fields', $comment_fields );
		 
		            // Get an array of field names, excluding the textarea.
		            $comment_field_keys = array_diff( array_keys( $comment_fields ), array( 'comment' ) );
		 
		            // Get the first and the last field name, excluding the textarea.
		            $first_field = reset( $comment_field_keys );
		            $last_field  = end( $comment_field_keys );
		 
		            foreach ( $comment_fields as $name => $field ) {
		 
		                if ( 'comment' === $name ) {
		 
		                    /**
		                     * Filters the content of the comment textarea field for display.
		                     *
		                     * @since 3.0.0
		                     *
		                     * @param string $args_comment_field The content of the comment textarea field.
		                     */
		                    echo apply_filters( 'ml_review_form_field_comment', $field );
		 
		                    echo $args['comment_notes_after'];
		 
		                } elseif ( ! is_user_logged_in() ) {
		 
		                    if ( $first_field === $name ) {
		                        /**
		                         * Fires before the comment fields in the comment form, excluding the textarea.
		                         *
		                         * @since 3.0.0
		                         */
		                        do_action( 'comment_form_before_fields' );
		                    }
		 
		                    /**
		                     * Filters a comment form field for display.
		                     *
		                     * The dynamic portion of the hook name, `$name`, refers to the name
		                     * of the comment form field.
		                     *
		                     * Possible hook names include:
		                     *
		                     *  - `comment_form_field_comment`
		                     *  - `comment_form_field_author`
		                     *  - `comment_form_field_email`
		                     *  - `comment_form_field_url`
		                     *  - `comment_form_field_cookies`
		                     *
		                     * @since 3.0.0
		                     *
		                     * @param string $field The HTML-formatted output of the comment form field.
		                     */
		                    echo apply_filters( "comment_form_field_{$name}", $field ) . "\n";
		 
		                    if ( $last_field === $name ) {
		                        /**
		                         * Fires after the comment fields in the comment form, excluding the textarea.
		                         *
		                         * @since 3.0.0
		                         */
		                        do_action( 'comment_form_after_fields' );
		                    }
		                }
		            }
		 
		            $submit_button = sprintf(
		                $args['submit_button'],
		                esc_attr( $args['name_submit'] ),
		                esc_attr( $args['id_submit'] ),
		                esc_attr( $args['class_submit'] ),
		                esc_attr( $args['label_submit'] )
		            );
		 
		            /**
		             * Filters the submit button for the comment form to display.
		             *
		             * @since 4.2.0
		             *
		             * @param string $submit_button HTML markup for the submit button.
		             * @param array  $args          Arguments passed to comment_form().
		             */
		            $submit_button = apply_filters( 'ml_review_form_submit_button', $submit_button, $args );
		 
		            $submit_field = sprintf(
		                $args['submit_field'],
		                $submit_button,
		                self::get_review_id_fields()
		            );
		 
		            /**
		             * Filters the submit field for the comment form to display.
		             *
		             * The submit field includes the submit button, hidden fields for the
		             * comment form, and any wrapper markup.
		             *
		             * @since 4.2.0
		             *
		             * @param string $submit_field HTML markup for the submit field.
		             * @param array  $args         Arguments passed to comment_form().
		             */
		            echo apply_filters( 'ml_review_form_submit_field', $submit_field, $args );
		
		            echo '</form>';
		 
		        endif;
		        ?>
		    </div><!-- #respond -->
	    <?php
	}

	public static function get_review_id_fields() {
	    $author_id = get_the_author_meta('ID');

	    $result      = "<input type='hidden' name='author_id' value='$author_id' id='author_id' />\n";

	    if ( is_user_logged_in() ) {
	    	$user =  get_user_by( 'ID', get_current_user_id() );
		    $result      .= "<input type='hidden' name='user_id' value='$user->ID' id='user_id' />\n";
	    }

	 	return $result;
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
		// if ( $this->table_version === $this->current_version ) {
		// 	return;
		// }

		global $wpdb;
		$table_name = $wpdb->prefix . 'mylisting_author_reviews';
		$sql = "CREATE TABLE $table_name (
			id bigint(20) unsigned NOT NULL auto_increment,
			author_id bigint(20) unsigned NOT NULL,
			user_email varchar(100) NOT NULL,
			user_id tinytext NOT NULL,
			posted_date datetime NOT NULL default '0000-00-00 00:00:00',
			posted_date_gmt datetime NOT NULL default '0000-00-00 00:00:00',
			comments text NOT NULL,
			rating_stars int(10) NOT NULL,
			approved_status int(1) NOT NULL,
			PRIMARY KEY  (id),
			KEY author_id (author_id)
		);";

		$table_name_meta = $wpdb->prefix . 'mylisting_author_reviewmeta';
		$sql2 = "CREATE TABLE $table_name_meta (
			meta_id bigint(20) unsigned NOT NULL auto_increment,
			review_id bigint(20) unsigned NOT NULL,
			meta_key tinytext NOT NULL,
			meta_value text NOT NULL,
			PRIMARY KEY (meta_id)
		);";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
		dbDelta( $sql2 );

		update_option( 'mylisting_author_review_table_version', $this->table_version );
	}

	public static function get_reviews() {
		$per_page  = 10;
		$page_number = 1;

		global $wpdb;
		$author_id = get_the_author_meta('ID');

		$sql = "SELECT * FROM {$wpdb->prefix}mylisting_author_reviews";

		$sql .= ' WHERE author_id='.$author_id.' and approved_status = 1';

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		return $result;
	}

	public static function get_review( $id ) {
		global $wpdb;

		$review_id = (int) $id;
		if ( ! $review_id ) {
			return false;
		}

		$_comment = wp_cache_get( $review_id, 'mlreviews' );

		if ( ! $_comment ) {
			$_comment = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}mylisting_author_reviews WHERE id = %d LIMIT 1", $review_id ), ARRAY_A );

			if ( ! $_comment ) {
				return false;
			}

			wp_cache_add( $_comment['id'], $_comment, 'mlreviews' );
		}

		return $_comment;
	}

	public static function get_review_time( $review, $format = '', $gmt = false, $translate = true ) {
	 
	    $posted_date = $gmt ? $review['posted_date_gmt'] : $review['posted_date'];
	 
	    $_format = ! empty( $format ) ? $format : get_option( 'time_format' );
	 
	    return mysql2date( $_format, $posted_date, $translate );
	}

	public static function get_review_date( $review, $format = '' ) {

	    $_format = ! empty( $format ) ? $format : get_option( 'date_format' );
	 
	    return mysql2date( $_format, $review['posted_date'] );
	}

	public static function sanitize_rating( $rating ) {
		// Make sure it's numeric. Do not use absint() or intval(). It will round the value.
		$rating = is_numeric( $rating ) ? ( $rating + 0 ) : 0;

		// Need to be more than 1 and less than 10.
		if ( $rating && $rating >= 1 && $rating <= 10 ) {
			return $rating;
		}

		// Invalid. Set as zero.
		return 0;
	}

	public static function get_rating( $review_id ) {
		$comment = self::get_review( $review_id );
		$max_rating = 5;
		$rating = self::sanitize_rating( self::get_metadata( $review_id, '_case27_post_rating', true ) );
		return round( 5 === $max_rating ? $rating / 2 : $rating, 1 );
	}

	public static function get_categories() {
		return [
			'globale' => [
				'id' => 'globale',
				'label' => 'Globale'
			],
			'ponctualite' => [
				'id' => 'ponctualite',
				'label' => 'Ponctualite'
			]
		];
	}

	public static function get_ratings_field( $comment = false, $post_id = false ) {

		$categories = self::get_categories();

		// Rating options.
		$max_rating = 5;
		$rating_options = array(
			'1' => 2,
			'2' => 4,
			'3' => 6,
			'4' => 8,
			'5' => 10,
		);

		$rating_options = array_reverse( $rating_options, true );

		ob_start();
		?>

		<?php if ( $categories ) : ?>
		<div class="form-group form-group-review-ratings <?php echo esc_attr( "rating-mode-{$max_rating}" );?>">
			<?php foreach( $categories as $rating => $category ) :
					$value = isset( $ratings[ $category['id'] ] ) ? self::sanitize_rating( $ratings[ $category['id'] ] ) : 0;

					// Rating mode 5 star.
					$value = 5 === $max_rating ? round( $value / 2 ) : $value;
			?>

				<div class="rating-category-field rating-category-field-<?php echo esc_attr( $category['id'] ); ?>">
					<div class="rating-category-label"><?php echo esc_html( $category['label'] ); ?> </div>

					<div class="rating-number form-group c27-rating-field">
						<p class="clasificacion">
							<?php foreach ( $rating_options as $k => $v ):
								$label_class = '';
								if ( $max_rating !== 5 ) {
									$label_class = ( $v % 2 === 0 ) ? 'right-half' : 'left-half';
								}
							?><input id="rating_<?php echo esc_attr( "{$category['id']}_{$v}" ); ?>" type="radio" name="<?php echo esc_attr( $category['id'] ); ?>_star_rating" value="<?php echo esc_attr( $v ); ?>" <?php checked( $k, $value );?>><!--
							 --><label for="rating_<?php echo esc_attr( "{$category['id']}_{$v}" ); ?>" class="<?php echo esc_attr( $label_class ) ?>"><i class="mi star_border <?php echo $max_rating === 5 && $v % 2 ?>"></i></label><!--
						 --><?php endforeach; ?>
						</p>
					</div><!-- .rating-number -->

				</div><!-- .rating-category-field -->

			<?php endforeach; ?>
		</div><!-- .form-group.form-group-review-ratings -->
		<?php endif; // End categories. ?>

		<?php
		return ob_get_clean();
	}

	public static function get_review_author( $comment_ID = 0 ) {
	    $comment = self::get_review( $comment_ID );
	    if ( empty( $comment['user_id'] ) ) {
	        $user = $comment['user_email'] ? get_userdata( $comment['user_email'] ) : false;
	        if ( $user ) {
	            $author = $user->display_name;
	        } else {
	            $author = __( 'Anonymous' );
	        }
	    } else {
	    	$user = get_userdata( $comment['user_id'] );
	        $author = $user->display_name;
	    }

	    return $author;
	}

	public static function get_review_author_link( $comment_ID = 0 ) {
	    $comment = self::get_review( $comment_ID );
	    $author  = self::get_review_author( $comment_ID );
	    return $author;
	}

	public static function get_user_rating_optimized( $author_id ) {
		$rating = self::sanitize_rating( get_user_meta( $author_id, '_case27_author_average_rating', true ) );
		$max_rating = 5;

		// No meta rating stored. Get from DB.
		if ( ! $rating ) {
			$rating = self::sanitize_rating( self::get_user_rating( $author_id ) );

			// Save it as post meta as cache.
			if ( $rating ) {
				update_post_meta( $author_id, '_case27_author_average_rating', $rating );
			}
		}

		return round( 5 === $max_rating ? $rating / 2 : $rating, 1 );
	}

	public static function get_reviews_html( $reviews ) {
		ob_start();
		?>
			<ul class="comments-list">
				<?php foreach ( $reviews as $review ) : ?>
					<li class="comment single-comment" id="comment-<?php echo $review['id']; ?>">
						<div class="comment-container">
							<div class="comment-head">

								<div class="c27-user-avatar">
									<?php echo get_avatar( $review['user_id'] ); ?>
								</div>

								<h5 class="case27-primary-text"><?php echo self::get_review_author_link( $review['id'] ) ?></h5>
								<span class="comment-date"><?php echo self::get_review_date( $review ) ?> <?php _e( 'at', 'my-listing' ) ?> <?php echo self::get_review_time( $review ) ?></span>

								<?php if ( $review_rating = self::get_rating( $review['id'] ) ): ?>
									<?php mylisting_locate_template( 'partials/star-ratings.php', [
			                            'rating' => $review_rating,
			                            'max-rating' => 5,
			                            'class' => 'listing-rating listing-review-rating',
			                        ] ) ?>
								<?php endif ?>
							</div>

							<div class="comment-body">
								<?php if( ! $review['approved_status'] ) : ?>
									<p><em class="comment-awaiting-moderation"><?php esc_html_e('Your comment is awaiting moderation.', 'my-listing') ?></em></p>
								<?php else: ?>
									<?php echo wp_kses( $review['comments'], 'post' ); ?>
								<?php endif; ?>
							</div>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php

		return ob_get_clean();
	}
}