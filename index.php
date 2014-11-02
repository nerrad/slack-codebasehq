<?php
/**
 * An application for recieving messages from slack that in turn triggers a post to codebasehq
 */
namespace Nerrad\SlackCb;
require 'vendor/autoload.php';
require 'configuration.php';

use Nerrad\SlackCb\Http\Request;

define( 'CB_SLACK_BASE_PATH', dirname( __FILE__ ) );


//grab request and assign to react class
$request = new Request( $_REQUEST );

//react
try {
	$react = new React( $request );
} catch ( \Exception $e){
	$msg = $e->getMessage();
	header( $msg, true, 501);
	exit();
}

//that's it!
