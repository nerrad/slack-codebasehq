<?php
/**
 * simply contains the default configuration class for the app.  Note you will need to add all your configuration details in the constructor of this class.
 */
namespace Nerrad\SlackCb;

class Config {
	private static $_instance = NULL;
	public $webhooks = array();
	public $codebase_map = array();
	public $default_project = '';
	public $account = '';
	public $project_map = array();

	public function instance() {
		if ( ! self::$_instance instanceof Config ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}


	private function __construct() {
		include 'app-config.php';
		$this->codebase_map = $map;

		$hooks = array(
			'testaction',
			'cbgettkt',
			'cbtkthelp',
			'cbposttkt',
			'cbupdatetkt'
			);
		foreach ( $hooks as $hook ) {
			$this->webhooks[$hook]['token'] = $slack_hook_token;
		}

		$this->default_project = $default_project;
		$this->account = $account;
		$this->project_map = $project_map;
	}
}
