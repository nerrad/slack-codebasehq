<?php
/**
 * An application for recieving messages from slack that in turn triggers a post to codebasehq
 */
namespace Nerrad\SlackCb;
require 'vendor/autoload.php';

//grab request and assign to react class
$request = new Request( $_REQUEST );

//react
$react = new React( $request );

//that's it!
