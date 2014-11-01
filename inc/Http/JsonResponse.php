<?php
/**
 * Just ensures data is sent as a json response
 */
namespace Nerrad\SlackCb\Http;

class JsonResponse {
	public function __construct( $response ) {
		$response = (array) $response;

		// make sure there are no php errors or headers_sent.  Then we can set correct json header.
		if ( NULL === error_get_last() || ! headers_sent() ) {
			header('Content-Type: application/json; charset=UTF-8', true, 200);
		}

		echo json_encode( $response );
		exit();
	}
}
