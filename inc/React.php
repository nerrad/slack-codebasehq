<?php
/**
 * Reacts to what may be a incoming slack request and handles accordingly
 */
namespace Nerrad\SlackCb;

use Nerrad\SlackCb\Http\Request;
use Nerrad\SlackCb\Http\JsonResponse;
use Nerrad\SlackCb\Config;
use CL\Slack\OutgoingWebhook\Request\OutgoingWebhookRequestFactory;
use CL\Slack\OutgoingWebhook\Request\OutgoingWebhookRequest;

class React {

	private $_config = null;

	public function __construct( Request $request ) {
		$this->_config = Config::instance();
		$webhookReqFactory = new OutgoingWebhookRequestFactory($this->_config->webhooks);
		$webhookReq = $webhookReqFactory->create( $request->get_required() );
		$triggerWord = $webhookReq->getTriggerWord();


		//dynamic check for trigger words.
		if ( method_exists($this, $triggerWord ) ) {
			$response = $this->$triggerWord( $webhookReq );
		} else {
			throw new \InvalidArgumentException( sprintf( 'Unknown tirgger-word: %s', $triggerWord) );
		}

		//return json response
		return new JsonResponse( $response );
	}



	public function testaction( OutgoingWebhookRequest $request ) {
		//just a response generated with the incoming data to indicate things went well.
		$response = 'Test succeeded/';
		$response .= 'TeamID:'.$request->getTeamId().'/';
		$response .= 'ChannelD:'.$request->getChannelId().'/';
		$response .= 'ChannelName:'.$request->getChannelName().'/';
		$response .= 'UserID:'.$request->getUserId().'/';
		$response .= 'UserName:'.$request->getUserName().'/';
		$response .= 'Text:'.$request->getText().'/';
		$response .= 'TriggerWord:'.$request->getTriggerWord();
		return array( 'text' => $response );
	}



	/**
	 * Returns some help on how to format tkts.
	 *
	 * @param OutgoingWebhookRequest $request
	 *
	 * @return array
	 */
	public function cbtkthelp( OutgoingWebhookRequest $request ) {

		$response = array(
			'text' => "I've grabbed the latest information for you on how to use the codebase-slack integration:",
			'attachments'  => array(
				'title' => '*Commands:*',
				'text' => 'These are various commands that you can use and their format to trigger various codebase interactions',
				'mrkdwn_in' => array( 'text', 'title' ),
				'color' => 'good',
				'fields' => array(
						array(
							'title' => 'Some Title',
							'value' => 'some value',
							'short' => true
						),
						array(
							'title' => 'Title B',
							'value' => 'another value',
							'short' => true
							)
					),
				)
			);

		return $response;
	}
	public function cbgettkt( OutgoingWebhookRequest $request ) {}
	public function cbposttkt( OutgoingWebhookRequest $request ) {}
	public function cbupdatetkt( OutgoingWebhookRequest $request ) {}
}
