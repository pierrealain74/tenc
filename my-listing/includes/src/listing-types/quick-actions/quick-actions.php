<?php
/**
 * Quick actions
 *
 * @since 2.0
 */

$actions = apply_filters( 'mylisting/types/quick-actions', [

	/* Get Directions */
	[
		'action' => 'get-directions',
		'label' => 'Get directions',
		'icon' => 'icon-location-pin-add-2',
		'track_custom_btn' => true,
	],

	/* Call Now */
	[
		'action' => 'call-now',
		'label' => 'Call now',
		'icon' => 'icon-phone-outgoing',
		'track_custom_btn' => true,
	],

	/* Direct Message */
	[
		'action' => 'direct-message',
		'label' => 'Direct message',
		'icon' => 'icon-chat-bubble-square-add',
		'track_custom_btn' => true,
	],

	/* Leave Review */
	[
		'action' => 'leave-review',
		'label' => 'Leave a review',
		'icon' => 'icon-chat-bubble-square-1',
		'track_custom_btn' => true,
	],

	/* Bookmark */
	[
		'action' => 'bookmark',
		'label' => 'Bookmark',
		'active_label' => 'Bookmarked',
		'icon' => 'mi favorite_border',
		'track_custom_btn' => true,
	],

	/* Share */
	[
		'action' => 'share',
		'label' => 'Share',
		'icon' => 'mi share',
		'track_custom_btn' => true,
	],

	/* Claim Listing */
	[
		'action' => 'claim-listing',
		'label' => 'Claim listing',
		'icon' => 'icon-location-pin-check-2',
		'track_custom_btn' => true,
	],

	/* Report */
	[
		'action' => 'report-listing',
		'label' => 'Report',
		'icon' => 'mi error_outline',
		'track_custom_btn' => true,
	],

	/* Visit Website */
	[
		'action' => 'visit-website',
		'label' => 'Website',
		'icon' => 'fa fa-link',
		'track_custom_btn' => true,
	],

	/* Send Email */
	[
		'action' => 'send-email',
		'label' => 'Send an email',
		'icon' => 'icon-email-outbox',
		'track_custom_btn' => true,
	],

	/* Plain */
	[
		'action' => 'plain',
		'label' => 'Display a field',
		'icon' => 'mi info_outline',
		'track_custom_btn' => true,
	],

	/* Custom */
	[
		'action' => 'custom',
		'label' => 'Custom action',
		'link' => '',
		'icon' => 'mi info_outline',
		'track_custom_btn' => true,
		'open_new_tab' => true,
	],
] );

// Convert list of actions to an associative array,
// using the action name as key.
$actions = array_combine( array_column( $actions, 'action' ), $actions );

// Include data that will be the same for all actions by default.
$actions = array_map( function( $action ) {
	$action['title_l10n'] = ['locale' => 'en_US'];
	$action['class'] = '';
	$action['id'] = '';

	return $action;
}, $actions );

return $actions;