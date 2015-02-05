<?php

$lang = array(

// Statuses
'paused'     => 'Paused',     // add-on updater
'processing' => 'Processing', // working
'completed'  => 'Completed',  // success
'stopped'    => 'Stopped',    // error

// Install Form
'required' => '✱ Required Fields',

	// Database Server
	'db_hostname'      => 'Database Server Address',
	'db_hostname_note' => 'Commonly <b>localhost</b>, but your host may require something else.',
	'db_name'          => 'Database Name',
	'db_name_note'     => 'Name of the database where you want ExpressionEngine installed.',
	'db_name_warning'  => 'Make sure the database exists, the installer will <b>not</b> create it.',
	'db_username'      => 'Database Server Username',
	'db_username_note' => 'Username used to access the above database.',
	'db_password'      => 'Database Server Password',
	'db_password_note' => 'Password used to access the above database.',
	'db_prefix'        => 'Database Table Prefix',
	'db_prefix_note'   => 'Use <b>exp</b> unless you require/prefer a different prefix.',

	// Account Creation
	'administrator_account' => 'Administrator Account',
	'username'              => 'Username',
	'username_note'         => 'Username you want to use to login to the <abbr title="Control Panel">CP</abbr>.',
	'e_mail'                => 'e-mail',
	'e_mail_note'           => 'e-mail address you want to use for this account.',
	'password'              => 'Password',
	'password_note'         => 'Password you want to use to login to the <abbr title="Control Panel">CP</abbr>.',

	// Default Theme
	'default_theme'              => 'Default theme',
	'install_default_theme'      => 'Install default theme?',
	'install_default_theme_info' => 'When set to <b>yes</b>, ExpressionEngine will install a default theme.',

	'start_installation'         => 'Start Installation',

// Update Form
	'update_title'   => "Update ExpressionEngine %s to %s",
	'start_update'   => 'Start Update',
	'update_note'    => '<b>Please</b> read <a href="https://ellislab.com/expressionengine/user-guide/installation/update.html">Updating ExpressionEngine</a> <strong>before</strong> starting.',
	'update_backup'  => 'Please <b>back up</b> your database before updating ExpressionEngine',
	'updating_title' => "Updating ExpressionEngine %s to %s",
	'updating'       => 'Updating',

'error'               => 'ERROR',
'submit'              => 'Submit',
'error_occurred'      => 'Oops, there was an error',
'version_update_text' => 'Running update...',

// Errors
'invalid_action'                     => 'The action you have requested is not valid.',
'unreadable_config'                  => 'Your config.php file is unreadable. Please make sure the file exists and that the file permissions to 666 (or the equivalent write permissions for your server) on the following file: expressionengine/config/config.php',
'unwritable_config'                  => 'Your config.php file does not appear to have the proper file permissions. Please set the file permissions to 666 (or the equivalent write permissions for your server) on the following file: expressionengine/config/config.php',
'unwritable_cache_folder'            => 'Your cache folder does not appear to have proper permissions. Please set the folder permissions to 777 (or the equivalent write permissions for your server) on the following folder: expressionengine/cache',
'database_no_config'                 => 'Unable to connect to your database using the configuration settings found in the following file: config/config.php file. Please correct the settings so that the update can proceed.',
'database_no_data'                   => 'Unable to locate any database connection information.',
'database_no_connect'                => 'Unable to connect to your database using the configuration settings you submitted.',
'database_prefix_invalid_characters' => 'There are invalid characters in the database prefix. Only 0-9, a-z, A-Z, $, and _ are allowed.',
'database_prefix_contains_exp_'      => 'The database prefix cannot contain the string "exp_".',
'database_prefix_too_long'           => 'The database prefix cannot be longer than 30 characters.',
'unreadable_update'                  => 'Unable to read the contents of your /expressionengine/installer/updates directory. Please check the file permissions and re-run this installation wizard.',
'unreadable_files'                   => 'One of your update files is unreadable. Please make sure all of the files located in this folder are readable: expressionengine/installer/updates/',
'unreadable_language'                => 'The language files needed for your current language selection (%x) are unavailable. Please put the language pack in this folder: expressionengine/language/',
'unreadable_email'                   => 'Unable to locate the file containing your email templates (email_data.php). Make sure you have uploaded all components of this software.',
'unreadable_schema'                  => 'Unable to locate the following folder: expressionengine/installer/schema/ Please upload all components before proceeding.',
'unreadable_dbdriver'                => 'Unable to locate the databae schema file in the following folder: expressionengine/installer/schema/ Please upload all components before proceeding.',
'improper_grants'                    => 'Error: Unable to perform the SQL queries. Please make sure your SQL account has the proper GRANT privileges: CREATE, DROP, ALTER, INSERT, and DELETE',
'empty_fields'                       => 'You must fill out all form fields',
'email_invalid'                      => 'The email address you submitted is not valid',
'username_short'                     => 'Your username must be at least 4 characters in length',
'username_invalid'                   => "Username is invalid. Must be less than 50 characters and cannot include the following characters: %s",
'password_short'                     => 'Your password must be at least 5 characters in length',
'password_no_match'                  => 'Your passwords and password confirmation do not match',
'password_not_unique'                => 'Your password can not be based on the username',
'password_no_dollar'                 => 'Your MySQL password can not contain a dollar sign',
'update_error'                       => 'An unexpected error occurred while performing the update',
'update_step_error'                  => 'An unexpected error occured while performing the update. Could not find update step: %x',
'install_detected_msg'               => 'ExpressionEngine appears to already be installed on your database, even though your config file is blank. If you are attempting to <b><i>update</i></b> ExpressionEngine from a previous version restore your config file first, then run this installation wizard again.',
'version_warning'                    => 'In order to install ExpressionEngine, your server must be running PHP version <mark><b>%s</b></mark> or newer. Your server is current running PHP version: <b>%s</b>. Contact your hosting provider to see if newer software is available for your server.',
'error_installing'                   => 'Error While Installing %s',
'error_updating'                     => 'Error While Updating %s to %s',

// Install/Upgrade Success
'install_success'      => "ExpressionEngine %s Installed",
'install_success_note' => '<b>Yay!</b> ExpressionEngine %s is now installed.',
'update_success'       => "ExpressionEngine Updated to %s",
'update_success_note'  => '<b>Yay!</b> ExpressionEngine is now updated to %s.',
'success_delete'       => 'Please delete the installer folder/directory from your server before proceeding.',
'cp_login'             => 'Control Panel login',

// Surveys
'opt_in_survey'              => 'Opt-in Survey',
'help_with_survey'           => "Please consider helping us make ExpressionEngine better by completing the optional survey for this update.",
'participate_in_survey'      => 'Participate in Survey?',
'send_anonymous_server_data' => 'Send Anonymous Server Data?',
'what_server_data_is_sent'   => '<p>What data is sent? Answering yes to this survey question will transmit the following:</p>
	<ul>
		<li>A one-way hash of your site URL to prevent duplicate submissions (this cannot be used to identify you)</li>
		<li>PHP version and available extensions</li>
		<li>MySQL version, server operating system</li>
		<li>Server software name</li>
		<li>Whether or not you are using forced query strings on your server</li>
		<li>A list of the add-ons in your third party directory</li>
		<li>Whether or not you have Discussion Forums installed</li>
		<li>Whether or not you have Multiple Site Manager installed</li>
	</ul>
	<p>All data is transmitted anonymously and cannot be associated with your local machine, ExpressionEngine user
	account, your site, or your host.</p>',
'show_hide_to_see_server_data' => 'Show/hide the server data that will be sent',
'would_you_recommend'          => 'How likely is it that you will recommend ExpressionEngine to a colleague or friend?',
'unlikely'                     => 'Unlikely',
'highly_likely'                => 'Highly Likely',
'additional_comments'          => 'What could ExpressionEngine or EllisLab do to delight you?',

);
/* End of file installer_lang.php */
/* Location: ./system/expressionengine/installer/language/english/installer_lang.php */
