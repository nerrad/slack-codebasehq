<?php
/**
 * Request class for simply receiving the requests
**/
namespace Nerrad\SlackCb\Http;

class Request {

	private $_reqArray;


	public function __construct(  $request ) {
		$this->_reqArray = $request;
	}


	public function get_all() {
		return $this->_reqArray;
	}


	public function get( $var ) {
		return isset( $this->_reqArray[$var] ) ? $this->_reqArray[$var] : null;
	}


	/**
	 * This just returns the required options
	 *
	 * @return array
	 */
	public function get_required() {
		$required = array(
			'token',
			'team_id',
			'channel_id',
			'channel_name',
			'timestamp',
			'user_id',
			'user_name',
			'text',
			'trigger_word'
			);
		$send = array();
		foreach ( $this->_reqArray as $key => $value ) {
			if ( in_array( $key, $required ) ) {
				$send[$key] = $value;
			}
		}
		return $send;
	}
}
