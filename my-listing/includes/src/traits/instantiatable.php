<?php

namespace MyListing\Src\Traits;

trait Instantiatable {
	protected static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}
}