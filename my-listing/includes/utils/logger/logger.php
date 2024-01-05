<?php

namespace MyListing\Utils\Logger;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Logger {
	use \MyListing\Src\Traits\Instantiatable;

	public $active = true;

	public function __construct() {
		if ( ! \MyListing\is_dev_mode() || ! defined( 'MYLISTING_LOG_OUTPUT' ) || MYLISTING_LOG_OUTPUT !== true ) {
			$this->active = false;
			return;
		}

		$this->table_version = '0.77';
		$this->current_version = get_option( 'mylisting_logs_table_version' );
		$this->setup_tables();
	}

	public function log( $content, $type ) {
		if ( ! $this->active ) {
			return;
		}

		global $wpdb;

		// Insert visit to db.
		$wpdb->insert( $wpdb->prefix.'mylisting_logs_demo', [
			'time' => gmdate('Y-m-d H:i:s'),
			'content' => $content,
			'type' => $type,
			'trace' => serialize( debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS ) ),
			'url' => $_SERVER['REQUEST_URI'],
		] );
	}

	public function info( $content ) {
		$this->log( $content, 'info' );
	}

	public function warn( $content ) {
		$this->log( $content, 'warning' );
	}

	public function note( $content ) {
		$this->log( $content, 'notice' );
	}

	public function blue( $content ) {
		$this->log( $content, 'info' );
	}

	public function red( $content ) {
		$this->log( $content, 'warning' );
	}

	public function yellow( $content ) {
		$this->log( $content, 'notice' );
	}

	public function green( $content ) {
		$this->log( $content, 'green' );
	}

	public function purple( $content ) {
		$this->log( $content, 'purple' );
	}

	public function black( $content ) {
		$this->log( $content, 'black' );
	}

	public function orange( $content ) {
		$this->log( $content, 'orange' );
	}

	public function brown( $content ) {
		$this->log( $content, 'brown' );
	}

	public function dump( $expression ) {
		echo '<pre>';
			foreach ( func_get_args() as $expression ) {
				var_dump( $expression );
				echo '<hr>';
			}
		echo '</pre>';
	}

	public function dd() {
		foreach ( func_get_args() as $expression ) {
			$this->dump( $expression );
		}
		die;
	}

	/**
	 * Print out a stack trace from entry point to wherever this function was called.
	 * @param boolean $show_args Show arguments passed to functions? Default False.
	 * @param boolean $for_web Format text for web? Default True.
	 * @param boolean $return Return result instead of printing it? Default False.
	 * @link https://gist.github.com/JaggedJax/3837352
	 */
	public function format_backtrace( $backtrace, $show_args = false ){
		if ( ! is_array( $backtrace ) ) {
			return;
		}

		$before = '<span>';
		$after = '</span>';
		$tab = '&nbsp;&nbsp;&nbsp;&nbsp;';
		$newline = '<br>';
		$output = '';
		$ignore_functions = array( 'include', 'include_once', 'require', 'require_once' );
		$length = count( $backtrace );

		// Start from index 1 to hide redundant line(s).
		for ( $i=1; $i<$length; $i++ ) {
			$function = '';
			$line = '<div class="cts-backtrace-log"><span class="cts-log-index">' . ($i) . '. </span>';
			$skip_args = false;
			$caller = @$backtrace[$i+1]['function'];
			// Display caller function (if not a require or include)
			if ( isset( $caller ) && ! in_array( $caller, $ignore_functions ) ) {
				$function = ' [fn:'.$caller.'()]';
			} else {
				$skip_args = true;
			}

			$line_nr = ! empty( $backtrace[$i]['line'] ) ? $backtrace[$i]['line'] : '(line:n/a)';
			$dir = ! empty( $backtrace[$i]['file'] ) ? dirname( $backtrace[$i]['file'] ) : '(dir:n/a)';
			$file = ! empty( $backtrace[$i]['file'] ) ? basename( $backtrace[$i]['file'] ) : '(file:n/a)';

			$line .= sprintf( '<em>%s<b>/%s:%s</b></em>', $dir, $file, $line_nr );

			$line .= $function.$newline;
			if ($i < $length-1){
				if ($show_args && $backtrace[($i+1)]['args'] && !$skip_args){
					$params = htmlentities(print_r($backtrace[($i+1)]['args'], true));
					$line .= $tab.'Called with params: '.preg_replace('/(\n)/',$newline.$tab,trim($params)).$newline.$tab.'By:'.$newline;
					unset($params);
				}
			}

			$line .= '</div>';
			$output .= $line;
		}

		return $output;
	}

	public function setup_tables() {
		if ( $this->table_version === $this->current_version ) {
			return;
		}

		global $wpdb;
		$table_name = $wpdb->prefix . 'mylisting_logs_demo';
		$sql = "CREATE TABLE $table_name (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			content mediumtext,
			type varchar(256),
			trace mediumtext,
			url varchar(512),
			time datetime NOT NULL,
			PRIMARY KEY  (id)
		);";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		update_option( 'mylisting_logs_table_version', $this->table_version );
	}

}