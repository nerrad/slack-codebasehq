<?php
/**
 * Reacts to what may be a incoming slack request and handles accordingly
 */
namespace Nerrad\SlackCb;

use Nerrad\SlackCb\Http\Request;
use Nerrad\SlackCb\Http\JsonResponse;
use Nerrad\SlackCb\Config;
use CL\Slack\OutgoingWebhook\Request\OutgoingWebhookRequestFactory;

class React {

	private $_config = null;

	public function __construct( Request $request ) {
		$this->_config = Config::instance();
		$webhookReqFactory = new OutgoingWebhookRequestFactory();
		$webhookReq = $webhookReqFactory->create( $request->get_all() );
		$triggerWord = $webhookRequest->getTriggerWord();

		$this->_verify_token( $webhookRequest );


		//dynamic check for trigger words.
		if ( method_exists($this, $triggerWord ) ) {
			$response = $this->$triggerWord( $webhookReq );
		} else {
			throw new \InvalidArgumentException( sprintf( 'Unknown tirgger-word: %s', $triggerWord) );
		}

		//return json response
		return new JsonResponse( [ 'text' => $response ] );
	}


	/**
	 * This ensures that the token incoming is what is expected, otherwise nothing gets done and return 501 http
	 * code.
	 *
	 * @param OutgoingWebhookRequest $request
	 *
	 * @return bool|Exception
	 */
	private function _verify_token( OutgoingWebhookRequest $request ) {
		$token = $request->getToken();

		if ( $token != $this->_config->expected_token ) {
			header( 'Unauthorized.', true, 501 );
			exit();
		}

		return true;
	}



	public function testaction( OutgoingWebhookRequest $request ) {
		//just a response generated with the incoming data to indicate things went well.
		$response = 'Test succeeded/';
		$response .= 'TeamID:'.$request->getTeamID().'/';
		$response .= 'ChannelD:'.$request->getChannelD().'/';
		$response .= 'ChannelName:'.$request->getChannelName().'/';
		$response .= 'UserID:'.$request->getUserId().'/';
		$response .= 'UserName:'.$request->getUserName().'/';
		$response .= 'Text:'.$request->getText().'/';
		$response .= 'TriggerWord:'.$request->getTriggerWord();
		return $response;
	}
}
