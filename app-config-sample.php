<?php
/**
 * This is a sample configuration file that you use for setting up various configuration for this app.  Simply save this as app-config.php with your own settings (it's added to .gitignore so will not be included in any repo pushes).
 */

/**
 * This is the map for slack username to codebase credentials.
 * Change this to match your own map
 */
$map = array(
	'slack_user_a' => array(
		'api' => 'your_api_key_from_codebase', //http://support.codebasehq.com/kb
		'username' => 'your_codebase_user_name'
		)
	);


/**
 * This is settting some defaults for the codebase projects etc.
 */
$default_project = 'short-name-for-default-codebase-project'; //slug for project
$account = 'account_name_for_the_projects'; //slug for account

/**
 * This allows you to map an array of project "keys" (used with the related triggers) to specific project slugs in codebase.  If a given project is not defined then whatever the value of $default_project gets used.
 *
 * @var array
 */
$project_map = array(
	'ee' => 'my_ee_project',
	'saas' => 'my_saas_project'
	);

/**
 * Please add here the token for your slack outgoing webhook integration.
 * You will want to add the following triggertext strings to your integration in slack,
 *
 * cbgetkt
 * cbtkthelp
 * cbposttkt
 * cbupdatetkt
 *
 * @var string
 */
$slack_hook_token = '';
