<?php

// Enqueue child theme style.css
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style( 'child-style', get_stylesheet_uri() );

    if ( is_rtl() ) {
    	wp_enqueue_style( 'mylisting-rtl', get_template_directory_uri() . '/rtl.css', [], wp_get_theme()->get('Version') );
    }

  wp_enqueue_script('custom-js', get_stylesheet_directory_uri() . '/assets/custom.js', ['jquery'], uniqid(), true );

    // wp_enqueue_script( 'double-checkbox-filter', get_stylesheet_directory_uri() . '/assets/filters/select-checkbox.js', [ 'vuejs', 'c27-main' ], uniqid(), true );
}, 500 );
add_shortcode( 'showauthors', 'show_authors' );
// usage [showauthors]
function show_authors(){
$query = new WP_Query(array(
    'post_type' => 'job_listing',
    'post_status' => 'publish',
	'posts_per_page' => -1
));


while ($query->have_posts()) {
    $query->the_post();
    echo get_the_author();
    echo "<br>";
}

wp_reset_query();
}
// add_action( 'wp_enqueue_scripts', 'pierre_resources', 9999 );
// add_action( 'admin_enqueue_scripts', 'pierre_resources', 9999 );
// function pierre_resources() {
//     wp_enqueue_script( 'double-checkbox-filter', get_stylesheet_directory_uri() . '/assets/filters/select-checkbox.js', [ 'vuejs', 'c27-main' ], uniqid(), true );
// }

// add_filter( 'mylisting/listing-types/register-filters', function( $filters ){
//     $filters[] = \MyListing\Src\Listing_Types\Filters\Double_Checkbox::class;
//     return $filters;
// }, 99, 1 );

require 'binance/vendor/autoload.php';

add_action( 'init', function() {
    if ( ! isset( $_GET['tests'] ) ) {
        return false;
    }
} );

add_filter( 'mylisting/listing-types/register-fields', function( $fields ) {
    $fields[] = \MyListing\Src\Forms\Fields\Currency_Field::class;
    $fields[] = \MyListing\Src\Forms\Fields\Disabled_Field::class;
    return $fields;
}, 30, 1 );

add_filter( 'mylisting/types/fields/presets', function( $default_fields ) {

    $default_fields['price_converter'] = new \MyListing\Src\Forms\Fields\Currency_Field( [
        'slug'           => 'price_converter',
        'label'          => 'cryptocurrencies to convert',
        'required'       => false,
        'priority'       => 5,
        'is_custom'      => false,
        'options'        => ['BTCEUR' => 'BTC', 'ETHEUR' => 'ETH', 'EURUSDT' => 'USDT']
    ] );

    $default_fields['converted_price'] = new \MyListing\Src\Forms\Fields\Disabled_Field( [
        'slug'           => 'converted_price',
        'label'          => 'Cryptocurrency prices',
        'required'       => false,
        'priority'       => 5,
        'is_custom'      => false
    ] );

    return $default_fields;
}, 99 );

add_filter( 'cron_schedules', 'ml_add_every_thirty_minutes' );
function ml_add_every_thirty_minutes( $schedules ) {
    $schedules['every_thirty_minutes'] = array(
        'interval'  => 1800,
        'display'   => __( 'Every 30 Minutes', 'my-listing' )
    );

    return $schedules;
}
  add_filter( 'acf/settings/show_admin', '__return_true', 50 );

// Schedule an action if it's not already scheduled
if ( ! wp_next_scheduled( 'mylisting/schedule:twicehourly' ) ) {
    wp_schedule_event( time(), 'every_thirty_minutes', 'mylisting/schedule:twicehourly' );
}

// Hook into that action that'll fire every three minutes
add_action( 'mylisting/schedule:twicehourly', 'every_thirty_minutes_event_func' );
function every_thirty_minutes_event_func() {

    $api = new Binance\API("dz24dKoL2KTOafILQ1Xj1q1kMQAZtSMmKWYvjKlHPSiroexJ1jy2NafEvhhzaBOb","XaXf64VQMn3kmk89DcYY1iPIkhr0feqnFup1bpPquDdtpJwbB6OFv7P8ypLKdFy9");

    // BTCEUR 35235.07000000
    $symbols = ['BTCEUR', 'ETHEUR', 'EURUSDT'];
    $prices = $api->prices();

    if ( ! $prices ) {
        return false;
    }

    $price_list = [];
    foreach ( $symbols as $symbol) {
        if ( ! isset( $prices[ $symbol ] ) ) {
            continue;
        }

        $price_list[ $symbol ] = $prices[ $symbol ];
    }

    update_option( '_price_list', $price_list );
}

if ( ! function_exists( 'ml_convert_prices' ) ) {
    function ml_convert_prices( $price, $symbol = 'BTCEUR' ) {
        $price_list = get_option( '_price_list', true );

        if ( ! $price_list ) {
            every_thirty_minutes_event_func();
        }
// print_r( [ $price_list, $price, $symbol ] );exit();
        if ( ! isset( $price_list[ $symbol ] ) ) {
            return false;
        }

        return $price / $price_list[ $symbol ];
    }
}

add_filter( 'mylisting/listing-types/register-blocks', function( $blocks){
    $blocks[] = \MyListing\Src\Listing_Types\Content_Blocks\Currency_Block::class;
    return $blocks;
}, 30, 1 );

add_filter( 'mylisting/types/quick-actions', function( $actions ) {
    $actions[] = [
        'action' => 'currency-converter',
        'label' => 'Converter',
        'icon' => 'mi info_outline',
        'track_custom_btn' => true,
    ];

    return $actions;
}, 99 );

add_action( 'mylisting/single/quick-actions/currency-converter', function( $action, $listing ) {

    $price = $listing->get_field('price');
    if ( empty( $price ) ) {
        $price = $listing->get_field('car-price');
    }

    if ( empty( $price ) ) {
        $price = $listing->get_field('prix-du-produit');
    }

    if ( empty( $price ) ) {
        $price = $listing->get_field('votre-prix');
    }
    
    $field = $listing->get_field('price_converter', true );
    $convert = $listing->get_field('price_converter');
    if ( $field && empty( $convert ) ) {
        $convert = 'BTCEUR';
    }

    if ( empty( $action['label'] ) || empty( $price ) || empty( $convert ) ) {
        return;
    }

    $convert_price = ml_convert_prices( $price, trim( $convert ) );
    $prefix = ' BTC';
    $image = get_stylesheet_directory_uri() . '/assets/btc.png';
    if ( trim( $convert ) == 'ETHEUR' ) {
        $prefix = ' ETH';
        $image = get_stylesheet_directory_uri() . '/assets/eth.png';
    } else if ( trim( $convert ) == 'EURUSDT' ) {
        $prefix = ' USDT';
        $image = get_stylesheet_directory_uri() . '/assets/usdt.png';
    }

    ob_start();
    ?>
        <li id="<?php echo esc_attr( $action['id'] ) ?>" class="<?php echo esc_attr( trim( $action['class'] ) ) ?>">
            <div><img src="<?php echo esc_url( $image ); ?>"></div>
            <span><?php echo number_format( floatval( $convert_price ), 4, '.', '' ) . $prefix; ?></span>
            <span><?php echo sprintf( '(%s%s)', $price, '€' ); ?></span>
        </li>
    <?php

}, 99, 2 );

add_filter('mylisting/type-editor/cover-details/limit', function( $limit ) {
    return 10;
}, 99 );

add_action( 'mylisting/init', function() {
    mylisting()->boot( MyListing\Ext\Author_Review\Author_Review::class );
    mylisting()->boot( MyListing\Ext\Email_Verification\Email_Verification::class );
    mylisting()->boot( MyListing\Ext\Email_Verification\WCE_Public::class );
}, 99 );

add_action( 'mylisting/dashboard/endpoints-init', function( $endpoints ) {
    $endpoints->add_page( [
        'endpoint' => 'change-password',
        'title' => __( 'Password Change', 'my-listing' ),
        'template' => locate_template( 'templates/dashboard/form-password-change.php' ),
        'show_in_menu' => true,
        'order' => 2,
    ] );

    $endpoints->add_page( [
        'endpoint' => 'cryptocurrency-address',
        'title' => __( 'Cryptocurrency Address', 'my-listing' ),
        'template' => locate_template( 'templates/dashboard/form-cryptocurrency-address.php' ),
        'show_in_menu' => true,
        'order' => 2,
    ] );
} );

add_action( 'template_redirect', function() {
    $nonce_value = wc_get_var( $_REQUEST['save-account-password-details-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) ); // @codingStandardsIgnoreLine.

    if ( ! wp_verify_nonce( $nonce_value, 'save_account_password_details' ) ) {
        return;
    }

    if ( empty( $_POST['action'] ) || 'save_account_password_details' !== $_POST['action'] ) {
        return;
    }

    wc_nocache_headers();

    $user_id = get_current_user_id();

    if ( $user_id <= 0 ) {
        return;
    }

    $pass_cur             = ! empty( $_POST['password_current'] ) ? $_POST['password_current'] : '';
    $pass1                = ! empty( $_POST['password_1'] ) ? $_POST['password_1'] : '';
    $pass2                = ! empty( $_POST['password_2'] ) ? $_POST['password_2'] : '';
    $save_pass            = true;

    $current_user       = get_user_by( 'id', $user_id );
    $current_first_name = $current_user->first_name;
    $current_last_name  = $current_user->last_name;
    $current_email      = $current_user->user_email;

    $user               = new stdClass();
    $user->ID           = $user_id;

    if ( ! empty( $pass_cur ) && empty( $pass1 ) && empty( $pass2 ) ) {
        wc_add_notice( __( 'Please fill out all password fields.', 'my-listing' ), 'error' );
        $save_pass = false;
    } elseif ( ! empty( $pass1 ) && empty( $pass_cur ) ) {
        wc_add_notice( __( 'Please enter your current password.', 'my-listing' ), 'error' );
        $save_pass = false;
    } elseif ( ! empty( $pass1 ) && empty( $pass2 ) ) {
        wc_add_notice( __( 'Please re-enter your password.', 'my-listing' ), 'error' );
        $save_pass = false;
    } elseif ( ( ! empty( $pass1 ) || ! empty( $pass2 ) ) && $pass1 !== $pass2 ) {
        wc_add_notice( __( 'New passwords do not match.', 'my-listing' ), 'error' );
        $save_pass = false;
    } elseif ( ! empty( $pass1 ) && ! wp_check_password( $pass_cur, $current_user->user_pass, $current_user->ID ) ) {
        wc_add_notice( __( 'Your current password is incorrect.', 'my-listing' ), 'error' );
        $save_pass = false;
    }

    if ( $pass1 && $save_pass ) {
        $user->user_pass = $pass1;
    }

    $errors = new WP_Error();

    if ( $errors->get_error_messages() ) {
        foreach ( $errors->get_error_messages() as $error ) {
            wc_add_notice( $error, 'error' );
        }
    }

    if ( wc_notice_count( 'error' ) === 0 ) {
        wp_update_user( $user );

        // Update customer object to keep data in sync.
        $customer = new WC_Customer( $user->ID );

        if ( $customer ) {
            $customer->save();
        }

        wc_add_notice( __( 'Password changed successfully.', 'my-listing' ) );

        wp_safe_redirect( wc_get_account_endpoint_url( 'change-password' ) );
        exit;
    }
});

add_action( 'template_redirect', function() {
    $nonce_value = wc_get_var( $_REQUEST['save-crypto-details-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) ); // @codingStandardsIgnoreLine.

    if ( ! wp_verify_nonce( $nonce_value, 'save_crypto_details' ) ) {
        return;
    }

    if ( empty( $_POST['action'] ) || 'save_crypto_details' !== $_POST['action'] ) {
        return;
    }

    wc_nocache_headers();

    $user_id = get_current_user_id();

    if ( $user_id <= 0 ) {
        return;
    }

    $bitcoin    = ! empty( $_POST['bitcoin'] ) ? wc_clean( wp_unslash( $_POST['bitcoin'] ) ) : '';
    $etherum    = ! empty( $_POST['etherum'] ) ? wc_clean( wp_unslash( $_POST['etherum'] ) ) : '';
    $tether     = ! empty( $_POST['tether'] ) ? wc_clean( wp_unslash( $_POST['tether'] ) ) : '';

    $current_user       = get_user_by( 'id', $user_id );
    $current_first_name = $current_user->first_name;
    $current_last_name  = $current_user->last_name;
    $current_email      = $current_user->user_email;

    $user               = new stdClass();
    $user->ID           = $user_id;

    $errors = new WP_Error();

    if ( $errors->get_error_messages() ) {
        foreach ( $errors->get_error_messages() as $error ) {
            wc_add_notice( $error, 'error' );
        }
    }

    if ( wc_notice_count( 'error' ) === 0 ) {
        wp_update_user( $user );

        update_user_meta( $user_id, 'bitcoin', $bitcoin );
        update_user_meta( $user_id, 'etherum', $etherum );
        update_user_meta( $user_id, 'tether', $tether );
        
        // Update customer object to keep data in sync.
        $customer = new WC_Customer( $user->ID );

        if ( $customer ) {
            $customer->save();
        }

        wc_add_notice( __( 'changed successfully.', 'my-listing' ) );

        wp_safe_redirect( wc_get_account_endpoint_url( 'cryptocurrency-address' ) );
        exit;
    }
});

function ml_check_currency_block( $block ) {
    $return = '';

    foreach ( $block as $block_item ) {
        if ( $block_item === 'endcolumn' || empty( $block_item->get_type() ) ) {
            continue;
        }

        if ( $block_item->get_type() == 'currency' ) {
            $return = true;
            break;
        }

    }

    return $return;
}
// function remove_menus(){  

//     if ( is_super_admin( get_current_user_id() ) ) {
//         return false;
//     }
    
//   remove_menu_page( 'index.php' );                  //Dashboard  
//   remove_menu_page( 'edit.php' );                   //Posts  
//   remove_menu_page( 'upload.php' );                 //Media  
//   remove_menu_page( 'edit.php?post_type=page' );    //Pages  
//   remove_menu_page( 'edit-comments.php' );          //Comments  
//   remove_menu_page( 'themes.php' );                 //Appearance  
//   remove_menu_page( 'plugins.php' );                //Plugins  
//   remove_menu_page( 'users.php' );                  //Users  
//   remove_menu_page( 'tools.php' );                  //Tools  
//   remove_menu_page( 'options-general.php' );        //Settings  

// }  
// add_action( 'admin_menu', 'remove_menus' );  
// 

add_action( 'mylisting/submission/done', 'isspam', 999);

function isspam($listing_id){
	$spam = checking($listing_id);
	if ( $spam == 0 ):
		$update_post = array(
			'ID' => $listing_id,
			'post_status' => 'publish'
		);
		$maybepublish = wp_update_post($update_post);
	echo "<script>console.log(\" $maybepublish $listing_id\")</script>";
	endif;

}
function checking($listing_id){
	global $post;
	$file = explode("\n",file_get_contents(WP_CONTENT_DIR.'/bad.txt'));
	$text = get_the_title($listing_id);
	$content_post = get_post($listing_id);
	$content = $content_post->post_content;
	$content = apply_filters('the_content', $content);
	$content = str_replace(']]>', ']]&gt;', $content);
	$text .= $content;
    foreach($file as $line){
        if(stripos($text, $line) !== false) return true;
    }
    return false;


}

