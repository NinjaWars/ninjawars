<?php
/*
* resources.php template. This file must be included whenever a script is run. It defines constants used throughout the application
*/
define('DATABASE_HOST', __DB_HOST__);		// *** The host to connect to for the database
define('DATABASE_USER', __DB_USER__);		// *** The user that should connect to the database
define('DATABASE_NAME', __DB_NAME__);		// *** The name of the database to connect to
define('OFFLINE', __OFFLINE__);				// *** Controls if remote or local resources are used
define('DEBUG', __DEBUG__);					// *** Shorter debugging constant name, set as false on live.
define('DEBUG_ALL_ERRORS', __DEBUG_ALL__);	// *** Only will turn on if debug is also on.
define('SERVER_ROOT', __SERVER_ROOT__);		// *** The root deployment directory of the game
define('WEB_ROOT', __WWW_ROOT__);			// *** The base URL used to access the game
define('ADMIN_EMAIL', __ADMIN_EMAIL__);		// *** For logs/emailed errors.
define('SUPPORT_EMAIL', __SUPPORT_EMAIL__);	// *** For public questions.
define('SUPPORT_EMAIL_FORMAL_NAME', __SUPPORT_EMAIL_NAME__);
define('SYSTEM_MESSENGER_EMAIL', __SYSTEM_EMAIL__);
define('SYSTEM_MESSENGER_NAME', __SYSTEM_EMAIL_NAME__);


// Seperate, tracked file for derived constants, that changes as they change.
require(SERVER_ROOT."derived_constants.php");
?>
