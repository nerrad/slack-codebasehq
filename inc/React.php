<?php
/**
 * Reacts to what may be a incoming slack request and handles accordingly
 */
namespace Nerrad\SlackCb;

use Nerrad\SlackCb\Http\Request;
use Nerrad\SlackCb\Http\JsonResponse;
use CL\Slack\OutgoingWebhook\Request\OutgoingWebhookRequest;

class React {
	public function __construct( Request $request ) {
		$webhookReqFactory = new OutgoingWebhookRequestFactory();
		$webhookReq = $webhookReqFactory->create( $request->get_all );
		$triggerWord = $webhookRequest->getTriggerWord();

		//dynamic check for trigger words.
		if ( method_exists($this, $trigger_word ) ) {
			$response = $this->$trigger_word( $webhookReq );
		} else {
			throw new \InvalidArgumentException( sprintf( 'Unknown tirgger-word: %s', $triggerWord) );
		}

		//return json response
		return new JsonResponse( [ 'text' => $response ] );
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
