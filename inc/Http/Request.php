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
}
