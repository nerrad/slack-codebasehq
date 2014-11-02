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

		$helptext = file_get_contents( 'cbtkthelp.template.md', FILE_USE_INCLUDE_PATH );

		$response = array(
			'text' => "I've grabbed the latest information for you on how to use the codebase-slack integration:",
			'attachments'  => array(
					array(
						'title' => 'Commands:',
						'text' => $helptext,
						'mrkdwn_in' => array( 'text', 'title' ),
						'color' => 'good'
					)
				)
			);

		return $response;
	}


	public function cbgettkt( OutgoingWebhookRequest $request ) {
		$incoming_text = $request->getText();
		$parsed_text = $this->_parse_params( $incoming_text );
		return array( 'text' => $parsed_text);
	}
	public function cbposttkt( OutgoingWebhookRequest $request ) {}
	public function cbupdatetkt( OutgoingWebhookRequest $request ) {}



	/**
	 * This parses a given strings for params in the format [key:value] and returns an array with the found params
	 * and the original string minus the parsed content.
	 *
	 *
	 * @param string $text_to_parse The incoming string that could have the key value pairs.
	 *
	 * @return array
	 */
	private function _parse_params( $text_to_parse ) {
		preg_match_all( '/\[.+?\]/', $text_to_parse, $matches );
		$parsed_text = $text_to_parse;
		$parsed_matches = array();
		foreach ( $matches[0] as $match ) {
			$parsed = preg_replace( '/\[|\]/', '', $match );
			$parsed = explode( ':', $parsed );
			if ( is_array( $parsed ) && count( $parsed) == 2 ) {
				$parsed_matches[$parsed[0]] = $parsed[1];
			}
			$parsed_text = str_replace( $match, '', $parsed_text );
		}
		return array( 'parsed_text' => $parsed_text, 'parsed_matches' => $parsed_matches );
	}
}
