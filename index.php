<?php
/**
 * Created by PhpStorm.
 * User: Lucas
 * Date: 18.03.2018
 * Time: 11:49
 */

//Debug
session_start();
set_time_limit(10);
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('SCRIPT_PATH', dirname($_SERVER["SCRIPT_FILENAME"]));
define('VENDOR_PATH', SCRIPT_PATH.'/vendor');
define('INCLUDE_PATH', SCRIPT_PATH.'/include');
define('CONTROLLER_PATH', SCRIPT_PATH.'/controller');

require_once VENDOR_PATH."/autoload.php";
require_once INCLUDE_PATH."/common.inc.php";

$common = new common(false);
$notifications = new notifications($common);

switch ($common->do) {
    case 'add':
        new PageAdd($common);
        break;
    case 'edit':
        new PageEdit($common);
        break;
    case 'delete':
        new PageDelete($common);
        break;
   /* case 'scan':
        new PageScan($common);
        break;
   */
    case 'login':
        $common->page_login('search');
        break;
    case 'export':
        $common->exportPDF();
        break;
    case 'calendar':
        $common->page_calendar();
        break;
    default:
        new PageSearch($common);
}

$common->page_output();