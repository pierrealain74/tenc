<?php

namespace MyListing\Ext\ical;

if ( ! defined('ABSPATH') ) {
	exit;
}

class iCalendar {
	use \MyListing\Src\Traits\Instantiatable;

    private $props = [];
    private $allowed_props = [
        'description',
        'htmldescription',
        'dtend',
        'dtstart',
        'location',
        'summary',
        'url'
    ];

    public function __construct() {
        add_action( 'mylisting_ajax_download_ical_calendar', [ $this, 'download_ical_calendar' ] );
        add_action( 'mylisting_ajax_nopriv_download_ical_calendar', [ $this, 'download_ical_calendar' ] );
    }

    public function download_ical_calendar() {
        // security check
        check_ajax_referer( 'c27_ajax_nonce', 'security' );

        $listing_id = ! empty( $_POST['listing_id'] ) ? (int) $_POST['listing_id'] : false;
        $eventindex = ! empty( $_POST['eventindex'] ) ? (int) $_POST['eventindex'] : false;
        $eventCount = ! empty( $_POST['eventCount'] ) ? (int) $_POST['eventCount'] : false;
        $eventPastCount = ! empty( $_POST['eventPastCount'] ) ? (int) $_POST['eventPastCount'] : false;
        $eventField = ! empty( $_POST['eventField'] ) ? sanitize_text_field( $_POST['eventField'] ) : false;

        if ( ! ( $listing_id && $eventField ) ) {
            return wp_send_json( [
                'status' => 'error',
                'message' => __( 'Please fill in all the necessary data.', 'my-listing' )
            ] );
        }

        $listing = \MyListing\Src\Listing::get( $listing_id );

        if ( ! ( $listing && $listing->type ) ) {
            return false;
        }

        $field = $listing->get_field_object( $eventField );
        if ( ! $field ) {
            return false;
        }

        $dates = $this->get_dates( $field, $eventCount, $eventPastCount );

        if ( ! is_array( $dates ) || empty( $dates ) ) {
            return false;
        }

        if ( ! isset( $dates[$eventindex] ) ) {
            return false;
        }

        $date = $dates[$eventindex];

        if ( ! isset( $date['start'] ) ) {
            return false;
        }

        // generate a description
        if ( $tagline = $listing->get_field( 'tagline' ) ) {
            $description = wp_kses( $tagline, [] );
        } else {
            $description = wp_kses( $listing->get_field( 'description' ), [] );
            $description = mb_strimwidth( $description, 0, 150, '...' );
        }

        if ( ! empty( $description ) ) {
            $description .= ' ';
        }

        if ( ! isset( $date['end'] ) ) {
            // if no end date, just duplicate the start date as the link
            // doesn't work with just a start date
            $date['end'] = $date['end'];
        }

        $ics = \MyListing\Ext\ical\iCalendar::instance();
        $instance = $ics->set_prop( [
            'location'          => $listing->get_field('location'),
            'description'       => $description,
            'dtstart'           => $date[ 'start' ],
            'dtend'             => $date['end'],
            'summary'           => $listing->get_name(),
            'htmldescription'   => $description,
            'url'               => $listing->get_link()
        ]);

        return wp_send_json( [
            'status' => 'success',
            'data'   => $ics->to_string(),
            'name'   => $listing->get_name()
        ] );
    }

     public function get_dates( $field, $eventCount, $eventPastCount ) {
        $dates = [];
        $now = date_create('now');
        if ( $field->get_type() === 'date' ) {
            $date = $field->get_value();
            if ( ! empty( $date ) && strtotime( $date ) && $now->getTimestamp() < strtotime( $date, $now->getTimestamp() ) ) {
                $dates[] = [
                    'start' => $date,
                    'end' => '',
                ];
            }
        }

        if ( $field->get_type() === 'recurring-date' ) {
            $dates = array_merge(
                \MyListing\Src\Recurring_Dates\get_previous_instances( $field->get_value(), $eventPastCount ),
                \MyListing\Src\Recurring_Dates\get_upcoming_instances( $field->get_value(), $eventCount )
            );

            foreach ( $dates as $key => $date ) {
                $dates[$key]['start'] = $date['start'];
                $dates[$key]['end'] = $date['end'];
            }
        }

        return $dates;
    }

	public function set_prop( $prop_name, $prop_value = null ) {
		if ( ! is_array( $prop_name ) ) {
			$prop_name = [ $prop_value ];
		}

		foreach ( $prop_name as $prop => $value ) {
			if ( ! in_array( $prop, $this->allowed_props ) ) {
				continue;
			}

			$this->props[ $prop ] = $this->sanitize_value( $prop, $value );
		}
	}

    public function to_string() {
        $rows = $this->generate_props();
        return implode("\r\n", $rows);
    }

    private function generate_props() {

        // Build ICS properties - add header
        $ics_props = array(
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//hacksw/handcal//NONSGML v1.0//EN',
            'CALSCALE:GREGORIAN',
            'BEGIN:VEVENT'
        );

        // Build ICS properties - add header
        $props = [];
        foreach( $this->props as $prop_name => $prop_value ) {
            if ( $prop_name !== 'htmldescription' ) {
                $props[ strtoupper( $prop_name . ( $prop_name === 'url' ? ';VALUE=URI' : '' ) ) ] = $prop_value;
            } else {
                $props['X-ALT-DESC;FMTTYPE=text/html'] = '<!DOCTYPE HTML PUBLIC ""-//W3C//DTD HTML 3.2//EN""><HTML><BODY>' . $prop_value . '</BODY></HTML>';
            }
        }

        // Set some default values
        $props['DTSTAMP'] = $this->format_timestamp('now');
        $props['UID'] = esc_attr( 'ics__' . uniqid() );

        // Append properties
        foreach ( $props as $k => $v ) {
            $ics_props[] = "$k:$v";
        }

        // Build ICS properties - add footer
        $ics_props[] = 'END:VEVENT';
        $ics_props[] = 'END:VCALENDAR';

        return $ics_props;
    }

	private function sanitize_value( $prop_name, $value ) {

        switch( $prop_name ) {
            case 'dtend':
            case 'dtstamp':
            case 'dtstart':
                $value = $this->format_timestamp( $value );
            break;

            default:
	            $value = sanitize_text_field( $value );
        	break;
        }

        return $value;
    }

    private function format_timestamp($timestamp) {
        return date( 'Ymd\THis', strtotime( $timestamp ) );
    }
}