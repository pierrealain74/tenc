<?php

namespace MyListing\Src\Notifications;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Notification_Template {

	public $body = '';

	public function get_body() {
		return $this->body;
	}

	public function add_paragraph( $content ) {
		$this->body .= '<p>'.$content.'</p>';
		return $this;
	}

	public function add_button( $content, $url ) {
		$this->body .= sprintf( '<a href="%s" class="mbtn" target="_blank">%s</a>', esc_url( $url ), $content );
		return $this;
	}

	public function add_primary_button( $content, $url ) {
		$this->body .= sprintf( '<a href="%s" class="mbtn mbtn1" target="_blank">%s</a>', esc_url( $url ), $content );
		return $this;
	}

	public function add_break() {
		$this->body .= '<br>';
		return $this;
	}

	public function add_thematic_break() {
		$this->body .= '<hr>';
		return $this;
	}

	public function add_content( $content ) {
		$this->body .= $content;
		return $this;
	}

}