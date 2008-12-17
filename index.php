<?php

$dir = dirname($_SERVER["SCRIPT_NAME"]);

define('BASE_URL', 'http://'.$_SERVER['HTTP_HOST']); //the base url of the site
define('DB_PROFILE', 'live'); //which database profile to use
define('DEVMODE', 0); //are we in dev mode?
define('FORCE_PORTAL', 1); // allows us to force a portal selection when server host wont work (in dev environment)
define('INDEX_PAGE', '');
define('APPTYPE', 'dam');

error_reporting(E_ALL); //Error reporting level

//paths to CI Folders

$system_folder = "./CodeIgniter_1.6.1_Patched/system";
$application_folder = "application";
$resource_folder = "./CodeIgniter_1.5.3_Resources"; //contains shared models, controllers etc.

/*
|===============================================================
| END OF USER CONFIGURABLE SETTINGS DO NOT TOUCH ANYTHING BELOW HERE
|===============================================================
*/


/*
|---------------------------------------------------------------
| SET THE SERVER PATH
|---------------------------------------------------------------
|
| Let's attempt to determine the full-server path to the "system"
| folder in order to reduce the possibility of path problems.
|
*/
if (function_exists('realpath') AND @realpath(dirname(__FILE__)) !== FALSE)
{
	$system_folder = str_replace("\\", "/", realpath(dirname(__FILE__))).'/'.$system_folder;
}

/*
|---------------------------------------------------------------
| DEFINE APPLICATION CONSTANTS
|---------------------------------------------------------------
|
| EXT		- The file extension.  Typically ".php"
| FCPATH	- The full server path to THIS file
| SELF		- The name of THIS file (typically "index.php)
| BASEPATH	- The full server path to the "system" folder
| APPPATH	- The full server path to the "application" folder
|
*/
define('EXT', '.'.pathinfo(__FILE__, PATHINFO_EXTENSION));
define('FCPATH', __FILE__);
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
define('BASEPATH', $system_folder.'/');

if (is_dir($application_folder))
{
	define('APPPATH', $application_folder.'/');
}
else
{
	if ($application_folder == '')
	{
		$application_folder = 'application';
	}

	define('APPPATH', BASEPATH.$application_folder.'/');
}

/*
|---------------------------------------------------------------
| DEFINE E_STRICT
|---------------------------------------------------------------
|
| Some older versions of PHP don't support the E_STRICT constant
| so we need to explicitly define it otherwise the Exception class 
| will generate errors.
|
*/
if ( ! defined('E_STRICT'))
{
	define('E_STRICT', 2048);
}

/*
|---------------------------------------------------------------
| LOAD THE FRONT CONTROLLER
|---------------------------------------------------------------
|
| And away we go...
|
*/
require_once BASEPATH.'codeigniter/CodeIgniter'.EXT;
?>
