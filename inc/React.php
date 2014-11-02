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
	private $_cbclient = null;

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
		$parsed_item = $this->_parse_params( $incoming_text );

		try {
			$this->_set_client( $request );
		} catch( \Exception $e) {
			return array( 'text' => $e->getMessage() );
		}

		$query = !empty( $parsed_item['parsed_matches']['tkt'] ) ? $parsed_item['parsed_matches']['tkt'] : 'sort:date';

		$project = !empty( $parsed_item['parsed_matches']['project'] ) ? $parsed_item['parsed_matches']['project'] : $this->_config->default_project;

		$ticket_info = $this->_cbclient->tickets($project, $query);
		// grab the first 5 in the returned data
		$chunked = array_chunk( $ticket_info, 5 );
		$ticket_info = $chunked[0];
		$ticket_content = $this->_generate_ticket_display( $ticket_info, $project );
		return $ticket_content;
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



	/**
	 * Fire up the codebase api client
	 */
	private function _set_client( OutgoingWebhookRequest $request ) {
		require_once CB_SLACK_BASE_PATH . '/vendor/nerrad/Codebase-PHP-Wrapper/bin/Codebase.class.php';
		$username = $request->getUserName();
		$cbusername = isset( $this->_config->codebase_map[$username]['username'] ) ? $this->_config->codebase_map[$username]['username'] : null;
		$cbapi = isset( $this->_config->codebase_map[$username]['api'] ) ? $this->_config->codebase_map[$username]['api'] : null;

		if ( empty( $cbusername ) || empty( $cbapi ) ) {
			throw new \Exception( 'The connection with codebase has not been setup correctly.  There is no username or api key in the map' );
		}

		$this->_cbclient = new \Codebase( $cbusername, $cbapi, $this->_config->account );
		return true;
	}




	/**
	 * Just generates ticket content for returning to display
	 *
	 * @param array $ticket_info Array of ticket info returned from codebase
	 * @param string $project   The project for the ticket
	 *
	 * @return array content for slack
	 */
	private function _generate_ticket_display( $tickets, $project ) {
		$template = 'cbtktget.template.php';
		$response['text'] = count( $ticket_info ) > 1 ? 'Here\'s the tickets you requested:' : 'Here is the ticket you requested';
		$response['attachments'] = array();

		foreach ( $tickets as $ticket ) {
			$ticket['ticket_url'] = 'https://' . $this->_config->account . '.codebasehq.com/projects/' . $project . '/tickets/' . $ticket['ticket-id'];
			$response['attachments'][] = array(
				'title' => $ticket['summary'],
				'text' => $this->_parse_template( $ticket, $template ),
				'mrkdwn_in' => array( 'text', 'title' ),
				'color' => $this->_get_color( $ticket['status']['colour'] )
				);
		}

		return $response;
	}




	private function _parse_template( $args, $template ) {
		$args = (array) $args;
		extract( $args );
		ob_start();
		include $template;
		return ob_get_clean();
	}


	private function _get_color( $color_string ) {
		$color_map = array(
			'green' => 'good',
			'blue' => '#2ea2cc',
			'red' => 'danger'
			);
	}
}
