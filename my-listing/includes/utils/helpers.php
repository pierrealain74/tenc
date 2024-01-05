<?php

namespace MyListing\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Helpers {
	use \MyListing\Src\Traits\Instantiatable;

	private $media_cache = [];

    // Get theme template path, with the given $path appended to it.
	public function template_path( $path ) {
		return get_template_directory() . "/$path";
	}

    // Get theme template uri, with the given $uri appended to it.
	public function template_uri( $uri = '' ) {
		return get_template_directory_uri() . "/$uri";
	}

    // URI to asset folder.
	public function asset( $asset ) {
		return $this->template_uri( "assets/$asset" );
	}

    // URI to images folder.
	public function image( $image ) {
		if ( file_exists( trailingslashit( get_stylesheet_directory() ) . 'assets/images/' . $image ) ) {
			return trailingslashit( get_stylesheet_directory_uri() ) . 'assets/images/' . $image;
		}

		return $this->asset( 'images/'.$image );
	}

    // Retrieve the featured_image url for the given post, on the given size.
	public function featured_image( $postID, $size = 'large' ) {
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $postID ), $size );
		return $image ? array_shift( $image ) : false;
	}

    // Get post terms from the given taxonomy.
	public function get_terms( $postID, $taxonomy = 'category' ) {
		$raw_terms = (array) wp_get_post_terms( $postID, $taxonomy );

		$terms = [];
		if ( ! empty( $raw_terms['errors'] ) ) {
			return $terms;
		}

		foreach ( $raw_terms as $raw_term ) {
			$terms[] = [
				'name' => $raw_term->name,
				'link' => get_term_link( $raw_term )
			];
		}

		return $terms;
	}

    // Print the post excerpt, limiting it to a given number of characters.
	public function the_excerpt( $charlength, $after = "&hellip;" ) {
		$excerpt = get_the_excerpt();
		$charlength++;

		if ( mb_strlen( $excerpt ) > $charlength ) {
			$subex = mb_substr( $excerpt, 0, $charlength - 5 );
			$exwords = explode( ' ', $subex );
			$excut = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) ) - 1;
			if ( $excut < 0 ) {
				echo mb_substr( $subex, 0, $excut );
			} else {
				echo $subex;
			}
			echo $after;
		} else {
			echo $excerpt;
		}
	}

	public function the_text_excerpt( $text, $charlength, $after = "&hellip;", $echo = true ) {
		$charlength++;
		$output = '';

		if ( mb_strlen( $text ) > $charlength ) {
			$subex = mb_substr( $text, 0, $charlength - 5 );
			$exwords = explode( ' ', $subex );
			$excut = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) ) - 1;
			if ( $excut < 0 ) {
				$output .= mb_substr( $subex, 0, $excut );
			} else {
				$output .= $subex;
			}
			$output .= $after;
		} else {
			$output .= $text;
		}

		if ( $echo ) {
			echo $output;
			return;
		}

		return $output;
	}

	public function merge_options( $defaults, $options ) {
		return array_replace_recursive( $defaults, $options );
	}

	public function get_partial( $template, $data = [] ) {
		if (!locate_template("partials/{$template}.php")) return;

		require locate_template("partials/{$template}.php");
	}

	public function get_section( $template, $data = [] ) {
		if (!locate_template("sections/{$template}.php")) return;

		require locate_template("sections/{$template}.php");
	}

	public function get_terms_dropdown_array($args = [], $key = 'term_id', $value = 'name') {
		$options = [];
		$terms = get_terms($args);

		if (is_wp_error($terms)) {
			return [];
		}

		foreach ((array) $terms as $term) {
			$options[$term->{$key}] = $term->{$value};
		}

		return $options;
	}

	public function get_icon_markup( $icon_string ) {
		if (strpos($icon_string, '://') !== false) {
			$icon_arr = explode('://', $icon_string);

			return "<i class=\"{$icon_arr[0]}\">{$icon_arr[1]}</i>";
		}

		return "<i class=\"{$icon_string}\"></i>";
	}

	public function get_setting( $setting, $default = '' ) {

		if ( function_exists('get_field') ) {
			if ( get_field($setting, 'option') !== null ) {
				return get_field($setting, 'option');
			} else {
				return get_option( 'option_'.$setting, $default );
			}
		}
	}

	public function get_site_logo() {
		if ($logo_obj = c27()->get_setting('general_site_logo')) {
			return $logo_obj['sizes']['large'];
		}

		return '';
	}

	public function get_site_logo_alt_text() {
		if ($logo_obj = c27()->get_setting('general_site_logo')) {
			return $logo_obj['alt'];
		}

		return '';
	}

	public function upload_file( $file, $allowed_mime_types = [] ) {
		include_once( ABSPATH . 'wp-admin/includes/file.php' );
		include_once( ABSPATH . 'wp-admin/includes/media.php' );
		include_once( ABSPATH . 'wp-admin/includes/image.php' );

		$uploaded_file = new \stdClass();

		if ( ! in_array( $file['type'], $allowed_mime_types ) ) {
			return new \WP_Error( 'upload', sprintf( __( 'Uploaded files need to be one of the following file types: %s', 'my-listing' ), implode( ', ', array_keys( $allowed_mime_types ) ) ) );
		}

		$upload = wp_handle_upload($file, ['test_form' => false]);

		if ( ! empty( $upload['error'] ) ) {
			return new \WP_Error( 'upload', $upload['error'] );
		}

		$wp_filetype = wp_check_filetype($upload['file']);
		$attach_id = wp_insert_attachment([
			'post_mime_type' => $wp_filetype['type'],
			'post_title' => sanitize_file_name($upload['file']),
			'post_content' => '',
			'post_status' => 'inherit'
			], $upload['file']);

		$attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
		wp_update_attachment_metadata( $attach_id, $attach_data );

		return $attach_id;
	}

	public function get_gradients() {
		return [
    		'gradient1' => ['from' => '#7dd2c7', 'to' => '#f04786'],
			'gradient2' => ['from' => '#71d68b', 'to' => '#00af9c'],
			'gradient3' => ['from' => '#FF5F6D', 'to' => '#FFC371'],
			'gradient4' => ['from' => '#EECDA3', 'to' => '#EF629F'],
			'gradient5' => ['from' => '#114357', 'to' => '#F29492'],
			'gradient6' => ['from' => '#52EDC7', 'to' => '#F29492'],
			'gradient7' => ['from' => '#C644FC', 'to' => '#5856D6'],
    	];
	}

	public function new_admin_page( $type = 'menu', $args = [] ) {
		if ( ! in_array( $type, [ 'menu', 'submenu', 'theme' ] ) ) return;

		call_user_func_array('add_' . $type . '_page', $args);
	}

	public function hexToRgb( $hex, $alpha = 1 ) {
		$rgb = [];

		if ( strpos( $hex, 'rgb' ) !== false ) {
			$hex = str_replace( ['rgba', 'rgb', '(', ')', ' '], '', $hex );
			$hexArr = explode( ',', $hex );

			$rgb['r'] = isset( $hexArr[0] ) ? absint( $hexArr[0] ) : 0;
			$rgb['g'] = isset( $hexArr[1] ) ? absint( $hexArr[1] ) : 0;
			$rgb['b'] = isset( $hexArr[2] ) ? absint( $hexArr[2] ) : 0;
			$rgb['a'] = isset( $hexArr[3] ) ? (float) $hexArr[3] : 1;

			return $rgb;
		}

		$hex      = str_replace( '#', '', $hex );
		$length   = strlen( $hex );
		$rgb['r'] = hexdec( $length == 6 ? substr( $hex, 0, 2 ) : ( $length == 3 ? str_repeat( substr( $hex, 0, 1 ), 2 ) : 0 ) );
		$rgb['g'] = hexdec( $length == 6 ? substr( $hex, 2, 2 ) : ( $length == 3 ? str_repeat( substr( $hex, 1, 1 ), 2 ) : 0 ) );
		$rgb['b'] = hexdec( $length == 6 ? substr( $hex, 4, 2 ) : ( $length == 3 ? str_repeat( substr( $hex, 2, 1 ), 2 ) : 0 ) );
		$rgb['a'] = $alpha;

		return $rgb;
	}

	public static function get_video_embed_details( $url ) {
		// Check if youtube
		preg_match('%(youtu.*be.*)\/(watch\?v=|embed\/|v|shorts|)(.*?((?=[&#?])|$))%i', $url, $matches);
		if ( isset( $matches[3] ) ) {
		    return ['url' => "https://www.youtube.com/embed/{$matches[3]}?origin=*", 'type' => 'external', 'service' => 'youtube', 'video_id' => $matches[3]];
		}

		// Check if vimeo
		$rx = "/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*(?<id>[0-9]{6,11})[?]?.*/";
		preg_match($rx, $url, $matches);
		if (isset($matches['id']) && trim($matches['id']) != "") {
			return ['url' => "https://player.vimeo.com/video/{$matches['id']}?api=1&player_id=".$matches['id'], 'type' => 'external', 'service' => 'vimeo', 'video_id' => $matches['id']];
		}

		// Check if dailymotion
		$rx = "/^.+dailymotion.com\/(video|hub)\/(?<id>[^_]+)[^#]*(#video=(?<id2>[^_&]+))?/";
		preg_match($rx, $url, $matches);
		if (isset($matches['id']) && trim($matches['id']) != "") {
			return ['url' => "https://www.dailymotion.com/embed/video/{$matches['id']}", 'type' => 'external', 'service' => 'dailymotion', 'video_id'=>$matches['id']];
		}

		return false;
	}

	/**
	 * Safely output encoded data as html attribute.
	 *
	 * @since 1.6.2
	 */
	public function encode_attr( $string ) {
		return htmlspecialchars( json_encode( $string ), ENT_QUOTES, 'UTF-8' );
	}

	/**
	 * Escape WordPress shortcode brackets.
	 * Used mainly to sanitize user input.
	 *
	 * @since 1.5.1
	 * @param string $value String to escape.
	 * @return string
	 */
	public function esc_shortcodes( $value ) {
		return str_replace( [ "[" , "]" ] , [ "&#91;" , "&#93;" ] , $value );
	}

	public function get_timezone() {
		return new \DateTimeZone( $this->get_timezone_string() );
	}

	public function get_timezone_string() {
		$timezone_string = get_option( 'timezone_string' );

		if ( $timezone_string ) {
			return $timezone_string;
		}

		$offset  = (float) get_option( 'gmt_offset' );
		$hours   = (int) $offset;
		$minutes = ( $offset - $hours );

		$sign      = ( $offset < 0 ) ? '-' : '+';
		$abs_hour  = abs( $hours );
		$abs_mins  = abs( $minutes * 60 );
		$tz_offset = sprintf( '%s%02d:%02d', $sign, $abs_hour, $abs_mins );

		return $tz_offset;
	}

	/**
	 * Retrieve object class name.
	 *
	 * @since 1.7.2
	 * @param bool $namespaced Whether to include the namespace or only the basename.
	 */
	public function get_class_name( $object, $namespaced = false ) {
		if ( $namespaced ) {
			return get_class( $object );
		}

		$parts = explode( '\\', get_class( $object ) );
		return end( $parts );
	}

	/**
	 * Receive a date object with current UTC time.
	 *
	 * @since 2.0
	 */
	public function utc() {
		return new \DateTime( 'now', new \DateTimeZone( 'UTC' ) );
	}

	public function get_flag( $country_code ) {
		if ( ! wp_style_is( 'flag-sprites-css', 'enqueued' ) ) {
			wp_enqueue_style( 'flag-sprites-css', $this->asset( 'vendor/flag-sprites/flags.min.css' ), [], \MyListing\get_assets_version() );
		}

		if ( ! ( $country = \MyListing\get_country_name_by_code( strtoupper( $country_code ) ) ) ) {
			$country = '';
			$country_code = 'unknown';
		}

		return sprintf(
			'<div class="flag-wrapper">' .
				'<img src="%s" class="flag flag-%s" alt="%s">' .
			'</div>',
			esc_url( $this->asset( 'vendor/flag-sprites/blank.gif' ) ),
			esc_attr( strtolower( $country_code ) ),
			esc_attr( $country )
		);
	}

	/**
	 * Determine the current listing type in submit listing form.
	 *
	 * @since 2.0
	 * @todo move to wp-job-manager integration dir as a util.
	 */
	public function get_submission_listing_type() {
		if ( ! empty( $_GET['listing_type'] ) ) {
			return $_GET['listing_type'];
		} elseif ( ! empty( $_GET['type'] ) ) {
			return $_GET['type'];
		} elseif ( ! empty( $_REQUEST['listing_type'] ) ) {
			return $_REQUEST['listing_type'];
		}

		return '';
	}

	public function class2file( $classname, $with_namespace = false ) {
		$parts = explode( '\\', $classname );
		return strtolower( str_replace( '_', '-', $with_namespace ? $classname : array_pop( $parts ) ) );
	}

	public function file2class( $filename ) {
		return str_replace( ' ', '_', ucwords( str_replace( '-', ' ', $filename ) ) );
	}

	/**
	 * Modified version of get_job_listing_pagination function in WP Job Manager.
	 *
	 * @since 2.1
	 */
	public function get_listing_pagination( $max_num_pages, $current_page = 1, $link_url = '#' ) {
		if ( $max_num_pages <= 1 ) {
			return;
		}

		$end_size = 3; $mid_size = 3;
		$start_pages = range( 1, $end_size );
		$end_pages = range( $max_num_pages - $end_size + 1, $max_num_pages );
		$mid_pages = range( $current_page - $mid_size, $current_page + $mid_size );
		$pages = array_intersect( range( 1, $max_num_pages ), array_merge( $start_pages, $end_pages, $mid_pages ) );
		$prev_page = 0;
		$links = [];

		// prev link
		if ( $current_page && $current_page > 1 ) {
			$links[] = sprintf(
				'<li><a href="%s" rel="prev" data-page="%s">&larr;</a></li>',
				esc_url( str_replace( '{page}', ( $current_page - 1 ), $link_url ) ),
				esc_attr( $current_page - 1 )
			);
		}

		// page numbers
		foreach ( $pages as $page ) {
			if ( $prev_page != $page - 1 ) {
				$links[] = '<li><span class="gap">...</span></li>';
			}

			$links[] = ( $current_page == $page )
				? sprintf( '<li><span class="current" data-page="%s">%s</span></li>', esc_attr( $page ), esc_html( $page ) )
				: sprintf(
					'<li><a href="%s" data-page="%s">%s</a></li>',
					esc_url( str_replace( '{page}', ( $page ), $link_url ) ),
					esc_attr( $page ),
					esc_html( $page )
				);

			$prev_page = $page;
		}

		// next link
		if ( $current_page && $current_page < $max_num_pages ) {
			$links[] = sprintf(
				'<li><a href="%s" rel="next" data-page="%s">&rarr;</a></li>',
				esc_url( str_replace( '{page}', ( $current_page + 1 ), $link_url ) ),
				esc_attr( $current_page + 1 )
			);
		}

		return sprintf(
			'<nav class="job-manager-pagination"><ul>%s</ul></nav>',
			join( '', $links )
		);
	}

	public function get_attachment_by_guid( $guid ) {
		if ( is_array( $guid ) ) {
			$guid = array_shift( $guid );
		}

		// cached query
		if ( ! empty( $this->media_cache[ $guid ] ) ) {
			// mlog()->info( 'Attachment retrieved from cache: '.basename( $guid ) );
		}

		// db query
		if ( ! isset( $this->media_cache[ $guid ] ) ) {
			global $wpdb;

			// mlog()->note( 'Querying attachment id for '.basename( $guid ) );
			$attachment_id = $wpdb->get_var( $wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts} WHERE post_type = 'attachment' AND guid = %s LIMIT 1", $guid
			) );

			$this->media_cache[ $guid ] = ! empty( $attachment_id ) ? $attachment_id : false;
		}

		// validate
		if ( empty( $this->media_cache[ $guid ] ) ) {
			// mlog()->warn( 'Could not retrieve attachment: '.basename( $guid ) );
			return false;
		}

		return $this->media_cache[ $guid ];

		// return wp_attachment_is_image( $this->media_cache[ $guid ] )
		// 	? wp_get_attachment_image_url( $this->media_cache[ $guid ], $size )
		// 	: wp_get_attachment_url( $this->media_cache[ $guid ] );
	}

	public function get_resized_image( $logo, $size = 'full' ) {
		if ( is_array( $logo ) ) {
			$logo = array_shift( $logo );
		}

		if ( $attachment_id = $this->get_attachment_by_guid( $logo, $size ) ) {
			return wp_get_attachment_image_url( $attachment_id, $size );
		}

		// no attachment found, fallback to the old wpjm image locator
		// mlog()->note( 'Fallback to wpjm locator for attachment: '.basename( $logo ) );

		global $_wp_additional_image_sizes;

		if ( 'full' !== $size
			 && strstr( $logo, WP_CONTENT_URL )
			 && ( isset( $_wp_additional_image_sizes[ $size ] ) || in_array( $size, array( 'thumbnail', 'medium', 'large', 'medium_large' ), true ) )
		) {

			if ( in_array( $size, array( 'thumbnail', 'medium', 'large', 'medium_large' ), true ) ) {
				$img_width  = get_option( $size . '_size_w' );
				$img_height = get_option( $size . '_size_h' );
				$img_crop   = get_option( $size . '_size_crop' );
			} else {
				$img_width  = $_wp_additional_image_sizes[ $size ]['width'];
				$img_height = $_wp_additional_image_sizes[ $size ]['height'];
				$img_crop   = $_wp_additional_image_sizes[ $size ]['crop'];
			}

			$upload_dir        = wp_upload_dir();
			$logo_path         = str_replace( array( $upload_dir['baseurl'], $upload_dir['url'], WP_CONTENT_URL ), array( $upload_dir['basedir'], $upload_dir['path'], WP_CONTENT_DIR ), $logo );
			$path_parts        = pathinfo( $logo_path );
			$dims              = $img_width . 'x' . $img_height;
			$resized_logo_path = str_replace( '.' . $path_parts['extension'], '-' . $dims . '.' . $path_parts['extension'], $logo_path );

			if ( strstr( $resized_logo_path, 'http:' ) || strstr( $resized_logo_path, 'https:' ) ) {
				return $logo;
			}

			if ( ! file_exists( $resized_logo_path ) ) {
				ob_start();

				$image = wp_get_image_editor( $logo_path );
				if ( ! is_wp_error( $image ) ) {
					$resize = $image->resize( $img_width, $img_height, $img_crop );
					if ( ! is_wp_error( $resize ) ) {
						$save = $image->save( $resized_logo_path );
						if ( ! is_wp_error( $save ) ) {
							$logo = dirname( $logo ) . '/' . basename( $resized_logo_path );
						}
					}
				}

				ob_get_clean();
			} else {
				$logo = dirname( $logo ) . '/' . basename( $resized_logo_path );
			}
		}

		return $logo;
	}

	public function set_terms_order( $post_id, $terms, $taxonomy = '' ) {
		global $wpdb;

		$counter = 0;
		foreach ( (array) $terms as $term ) {
			$wpdb->query( sprintf(
				"UPDATE {$wpdb->term_relationships} SET term_order = '%d' WHERE object_id = '%d' AND term_taxonomy_id = '%d'",
				++$counter,
				(int) $post_id,
				(int) $term
			) );
		}
	}

	/**
	 * Similar to `get_edit_post_link`, but is not restricted
	 * to certain roles.
	 *
	 * @since 2.1
	 */
	public function get_edit_post_link( $id = 0 ) {
	    if ( ! $post = get_post( $id ) ) {
	        return;
	    }

	    $action = '&amp;action=edit';

	    $post_type_object = get_post_type_object( $post->post_type );
	    if ( ! $post_type_object ) {
	        return;
	    }

	    if ( $post_type_object->_edit_link ) {
	        $link = admin_url( sprintf( $post_type_object->_edit_link . $action, $post->ID ) );
	    } else {
	        $link = '';
	    }

	    return $link;
	}

	/**
	 * Similar to `get_edit_user_link`, but is not restricted
	 * to certain roles.
	 *
	 * @since 2.1
	 */
	public function get_edit_user_link( $user_id = null ) {
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		if ( empty( $user_id ) ) {
			return '';
		}

		$user = get_userdata( $user_id );
		if ( ! $user ) {
			return '';
		}

		$link = add_query_arg( 'user_id', $user->ID, self_admin_url( 'user-edit.php' ) );

		return $link;
	}

	public function get_user_by_id_or_email( $id_or_email ) {
		// Process the user identifier.
		if ( is_numeric( $id_or_email ) ) {
		    $user = get_user_by( 'id', absint( $id_or_email ) );
		} elseif ( $id_or_email instanceof \WP_User ) {
		    // User Object
		    $user = $id_or_email;
		} elseif ( $id_or_email instanceof \WP_Post ) {
		    // Post Object
		    $user = get_user_by( 'id', (int) $id_or_email->post_author );
		} elseif ( $id_or_email instanceof \WP_Comment && ! empty( $id_or_email->user_id ) ) {
	    	$user = get_user_by( 'id', (int) $id_or_email->user_id );
		} elseif ( is_string( $id_or_email ) && is_email( $id_or_email ) ) {
			$user = get_user_by( 'email', $id_or_email );
		} else {
			$user = false;
		}

		if ( ! ( $user instanceof \WP_User ) ) {
			return false;
		}

		return $user;
	}

	/**
	 * Get a user role nice name based on it's slug.
	 *
	 * @since 2.1.6
	 */
	public function get_role_name( $role ) {
		global $wp_roles;
		return isset( $wp_roles->role_names[ $role ] ) ? translate_user_role( $wp_roles->role_names[ $role ] ) : false;
	}

	public static function get_extension_icon( $extension, $default = 'fa fa-file-o' ) {
		$icons = [
			'pdf'  => 'fa fa-file-pdf-o',
			'jpg'  => 'fa fa-file-image-o',
			'jpeg' => 'fa fa-file-image-o',
			'png'  => 'fa fa-file-image-o',
			'gif'  => 'fa fa-file-image-o',
			'flv'  => 'fa fa-file-video-o',
			'mp4'  => 'fa fa-file-video-o',
			'm3u8' => 'fa fa-file-video-o',
			'ts'   => 'fa fa-file-video-o',
			'3gp'  => 'fa fa-file-video-o',
			'mov'  => 'fa fa-file-video-o',
			'avi'  => 'fa fa-file-video-o',
			'wmv'  => 'fa fa-file-video-o',
		];

		return ! empty( $icons[ $extension ] ) ? $icons[ $extension ] : $default;
	}

	public static function add_custom_style( $stylestring ) {
		if ( ! isset( $GLOBALS['case27_custom_styles'] ) ) {
			$GLOBALS['case27_custom_styles'] = '';
		}

		$GLOBALS['case27_custom_styles'] .= $stylestring;
	}

	/**
	 * Get the SQL query for getting listings within a given proximity.
	 *
	 * @link  https://wordpress.stackexchange.com/a/206560/123815
	 * @since 1.0
	 */
	public static function get_proximity_sql() {
		global $wpdb;

		return "
			SELECT $wpdb->posts.ID,
				( %s * IFNULL( acos(
					cos( radians(%s) ) *
					cos( radians( location.lat ) ) *
					cos( radians( location.lng ) - radians(%s) ) +
					sin( radians(%s) ) *
					sin( radians( location.lat ) )
				), 0 ) )
				AS distance, location.lat AS latitude, location.lng AS longitude
				FROM $wpdb->posts
				INNER JOIN {$wpdb->prefix}mylisting_locations
					AS location
					ON $wpdb->posts.ID = location.listing_id
				WHERE 1=1
					AND ($wpdb->posts.post_status = 'publish' )
				HAVING distance < %s
				ORDER BY distance ASC";
	}

	public static function get_open_ranges( $schedule ) {
		$ranges = [];
		if ( ! is_array( $schedule ) ) {
			return [];
		}

		$indexes = [
			'Monday' => 0,
			'Tuesday' => 1,
			'Wednesday' => 2,
			'Thursday' => 3,
			'Friday' => 4,
			'Saturday' => 5,
			'Sunday' => 6,
		];

		unset( $schedule['timezone'] );

		// day length in minutes
		$day_length = 1440;
		foreach ( $schedule as $day => $data ) {
			$index = $indexes[ $day ];
			$day_start = $day_length * $index;

 			if ( $data['status'] === 'open-all-day' ) {
				$data['status'] = 'enter-hours';
				$data[] = [
					'from' => '00:00',
					'to' => '00:00',
				];
			}

			if ( $data['status'] === 'enter-hours' ) {
				unset( $data['status'] );
				foreach ( $data as $slot ) {
					$from = \DateTime::createFromFormat( 'H:i', $slot['from'] ?? null );
					$to = \DateTime::createFromFormat( 'H:i', $slot['to'] ?? null );
					if ( $from && $to ) {
						$from_minute = $day_start + ( absint( $from->format('H') ) * 60 ) + absint( $from->format('i') );
						$to_minute = $day_start + ( absint( $to->format('H') ) * 60 ) + absint( $to->format('i') );

						// handle overnight schedules, e.g. 19:00 - 03:00
						if ( $to_minute <= $from_minute ) {
							$to_minute += $day_length;
						}

						// handle overnight schedules going from sunday to monday
						if ( $to_minute > 10080 ) {
							$monday_minutes = $to_minute - 10080;
							$ranges[] = [ 0, $monday_minutes ];

							$to_minute = 10080;
						}

						$ranges[] = [ $from_minute, $to_minute ];
					}
				}
			}
		}

		usort( $ranges, function( $a, $b ) {
			return $a[0] - $b[0];
		} );

		$n = 0;
		$len = count( $ranges );
		for ( $i = 1; $i < $len; ++$i ) {
			if ( $ranges[$i][0] > $ranges[$n][1] + 1 ) {
				$n = $i;
			} else {
				if ( $ranges[$n][1] < $ranges[$i][1] ) {
					$ranges[$n][1] = $ranges[$i][1];
				}

				unset( $ranges[$i] );
			}
		}

		return array_values( $ranges );
	}
}
