<?php
/**
 * simply contains the default configuration class for the app.  Note you will need to add all your configuration details in the constructor of this class.
 */
namespace Nerrad\SlackCb;

class Config {
	private static $_instance = NULL;
	public $expected_token = '';

	public function instance() {
		if ( ! self::$_instance instanceof Config ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}


	private function __construct() {
		$this->expected_token = '';
	}
}
