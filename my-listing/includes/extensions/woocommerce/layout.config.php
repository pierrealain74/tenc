<?php

return [
	// Apply block wrappers.
	'block' => [
		'woocommerce_before_cart_table' => [
	        'start' => 'woocommerce_before_cart_table',
	        'end' => 'woocommerce_after_cart_table',
	        'classes' => 'element',
            'icon' => 'mi shopping_basket',
            'title' => __( 'Cart', 'my-listing' ),
		],
	],

	// Apply column wrappers.
	'column' => [
		'checkout/form-coupon.php' => [
	        'start' => 'mylisting/woocommerce/templates/checkout/form-coupon.php/before',
	        'end' => 'mylisting/woocommerce/templates/checkout/form-coupon.php/after',
	        'classes' => 'c27-form-coupon-wrapper',
		],

		'woocommerce_before_checkout_form' => [
	        'start' => 'woocommerce_before_checkout_form',
	        'end' => 'woocommerce_after_checkout_form',
	        'classes' => '',
		],
	],

	// Apply section wrappers.
	'section' => [
		'cart' => [
			'start' => 'woocommerce_before_cart',
			'end' => 'woocommerce_after_cart',
			'title' => '',
			'icon' => 'icon-shopping-basket-1',
			'columns' => 'col-md-12',
			'classes' => 'cart-products-27',
		],

		'thankyou' => [
			'start' => 'case27_woocommerce_before_thankyou_template',
            'end' => 'case27_woocommerce_after_thankyou_template',
            'title' => '',
            'icon' => 'icon-shopping-basket-1',
            'title' => _x( 'Order received', 'Dashboard page title', 'my-listing' ),
		],

		'myaccount/form-lost-password.php' => [
            'start' => 'mylisting/woocommerce/templates/myaccount/form-lost-password.php/before',
            'end' => 'mylisting/woocommerce/templates/myaccount/form-lost-password.php/after',
            'title' => __( 'Lost your password?', 'my-listing' ),
            'icon' => 'mi lock_outline',
            'columns' => 'col-md-4 col-md-offset-4',
            'classes' => 'i-section form-lost-pw',
		],

		'myaccount/form-reset-password.php' => [
            'start' => 'mylisting/woocommerce/templates/myaccount/form-reset-password.php/before',
            'end' => 'mylisting/woocommerce/templates/myaccount/form-reset-password.php/after',
            'title' => __( 'Reset your password', 'my-listing' ),
            'icon' => 'mi lock_outline',
            'columns' => 'col-md-4 col-md-offset-4',
            'classes' => 'i-section form-lost-pw form-reset-pw',
		],

		'myaccount/lost-password-confirmation.php' => [
            'start' => 'mylisting/woocommerce/templates/myaccount/lost-password-confirmation.php/before',
            'end' => 'mylisting/woocommerce/templates/myaccount/lost-password-confirmation.php/after',
            'title' => __( 'Reset your password', 'my-listing' ),
            'icon' => 'mi lock_outline',
            'columns' => 'col-md-4 col-md-offset-4',
            'classes' => 'i-section form-lost-pw form-confirm-pw',
		],

		'cart/cart-empty.php' => [
            'start' => 'mylisting/woocommerce/templates/cart/cart-empty.php/before',
            'end' => 'mylisting/woocommerce/templates/cart/cart-empty.php/after',
            'title' => '',
            'icon' => 'icon-shopping-basket-1',
            'columns' => 'col-md-12',
            'classes' => 'i-section empty-cart-wrapper',
        ],

		'checkout/cart-errors.php' => [
            'start' => 'mylisting/woocommerce/templates/checkout/cart-errors.php/before',
            'end' => 'mylisting/woocommerce/templates/checkout/cart-errors.php/after',
            'title' => '',
            'icon' => 'icon-shopping-basket-1',
            'columns' => 'col-md-12',
            'classes' => 'i-section cart-errors-wrapper',
        ],

		'myaccount/form-edit-address.php' => [
	        'start' => 'mylisting/woocommerce/templates/myaccount/form-edit-address.php/before',
	        'end' => 'mylisting/woocommerce/templates/myaccount/form-edit-address.php/after',
	        'title' => _x( 'Addresses', 'Dashboard page title', 'my-listing' ),
	        'icon' => 'mi map',
            'columns' => 'col-md-8 col-md-offset-2',
            'classes' => 'i-section',
		],

		'myaccount/downloads.php' => [
	        'start' => 'mylisting/woocommerce/templates/myaccount/downloads.php/before',
	        'end' => 'mylisting/woocommerce/templates/myaccount/downloads.php/after',
	        'title' => _x( 'Downloads', 'Dashboard page title', 'my-listing' ),
	        'icon' => 'mi file_download',
            'columns' => 'col-md-8 col-md-offset-2',
            'classes' => 'i-section',
		],

		'myaccount/orders.php' => [
	        'start' => 'mylisting/woocommerce/templates/myaccount/orders.php/before',
	        'end' => 'mylisting/woocommerce/templates/myaccount/orders.php/after',
	        'title' => _x( 'Orders', 'Dashboard page title', 'my-listing' ),
	        'icon' => 'mi shopping_basket',
            'columns' => 'col-md-8 col-md-offset-2',
            'classes' => 'i-section',
		],

		'myaccount/view-order.php' => [
	        'start' => 'mylisting/woocommerce/templates/myaccount/view-order.php/before',
	        'end' => 'mylisting/woocommerce/templates/myaccount/view-order.php/after',
	        'title' => _x( 'View Order', 'Dashboard page title', 'my-listing' ),
	        'icon' => 'mi shopping_basket',
            'columns' => 'col-md-8 col-md-offset-2',
            'classes' => 'i-section',
		],

		'myaccount/payment-methods.php' => [
	        'start' => 'mylisting/woocommerce/templates/myaccount/payment-methods.php/before',
	        'end' => 'mylisting/woocommerce/templates/myaccount/payment-methods.php/after',
	        'title' => _x( 'Payment Methods', 'Dashboard page title', 'my-listing' ),
	        'icon' => 'mi payment',
            'columns' => 'col-md-8 col-md-offset-2',
            'classes' => 'i-section',
		],

		'myaccount/my-subscriptions.php' => [
	        'start' => 'mylisting/woocommerce/templates/myaccount/my-subscriptions.php/before',
	        'end' => 'mylisting/woocommerce/templates/myaccount/my-subscriptions.php/after',
	        'title' => _x( 'Subscriptions', 'Dashboard page title', 'my-listing' ),
	        'icon' => 'mi monetization_on',
            'columns' => 'col-md-8 col-md-offset-2',
            'classes' => 'i-section',
		],

		'myaccount/view-subscription.php' => [
	        'start' => 'mylisting/woocommerce/templates/myaccount/view-subscription.php/before',
	        'end' => 'mylisting/woocommerce/templates/myaccount/view-subscription.php/after',
	        'title' => _x( 'Subscriptions', 'Dashboard page title', 'my-listing' ),
	        'icon' => 'mi monetization_on',
            'columns' => 'col-md-8 col-md-offset-2',
            'classes' => 'i-section',
		],

		'simple-products/published' => [
	        'start' => 'case27_woocommerce_account_products_published_before',
	        'end' => 'case27_woocommerce_account_products_published_after',
	        'title' => _x( 'Published Products', 'Dashboard page title', 'my-listing' ),
	        'icon' => 'mi view_list',
            'columns' => 'col-md-8 col-md-offset-2',
            'classes' => 'i-section',
		],

		'simple-products/pending' => [
	        'start' => 'case27_woocommerce_account_products_pending_before',
	        'end' => 'case27_woocommerce_account_products_pending_after',
	        'title' => _x( 'Pending Products', 'Dashboard page title', 'my-listing' ),
	        'icon' => 'mi view_list',
            'columns' => 'col-md-8 col-md-offset-2',
            'classes' => 'i-section',
		],

		'simple-products/add-product' => [
	        'start' => 'case27_woocommerce_account_add_product_before',
	        'end' => 'case27_woocommerce_account_add_product_after',
	        'title' => _x( 'Add a Product', 'Dashboard page title', 'my-listing' ),
	        'icon' => 'mi note_add',
            'columns' => 'col-md-8 col-md-offset-2',
            'classes' => 'i-section',
		],

		'myaccount/form-add-payment-method.php' => [
		    'start'   => 'mylisting/woocommerce/templates/myaccount/form-add-payment-method.php/before',
		    'end'     => 'mylisting/woocommerce/templates/myaccount/form-add-payment-method.php/after',
		    'title'   => _x( 'Add payment method', 'Dashboard page title', 'my-listing' ),
		    'icon'    => 'mi payment',
		    'columns' => 'col-md-8 col-md-offset-2',
		    'classes' => 'i-section',
		],
	],

	'div' => [
		'woocommerce_checkout_before_customer_details' => [
	        'start' => 'woocommerce_checkout_before_customer_details',
	        'end' => 'woocommerce_checkout_after_customer_details',
	        'classes' => 'ts-billing-details element',
		],
	],
];
