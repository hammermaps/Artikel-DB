<?php
/**
 * Created by PhpStorm.
 * User: Lucas
 * Date: 18.03.2018
 * Time: 11:49
 */

//Debug
session_start();
set_time_limit(15);
error_reporting(0);
ini_set('display_errors', 0);

define('SCRIPT_PATH', dirname($_SERVER["SCRIPT_FILENAME"]));
define('VENDOR_PATH', SCRIPT_PATH.'/vendor');
define('INCLUDE_PATH', SCRIPT_PATH.'/include');

require_once VENDOR_PATH."/autoload.php";
require_once INCLUDE_PATH."/common.inc.php";

$common = new common();
$requestData= $_POST;

$query = $common->database->query("SELECT * FROM `artikel` WHERE `ean` = ".intval($_SESSION['ean']).";");
$rows = $query->fetchAll();

$data = [];
foreach ($rows as $row) {  // preparing an array
    $row["tags"] = preg_replace('/,/',', ',$row["tags"]);
    $row["name"] = preg_replace('/{blank}/',' ',trim($row["name"]));
    $row["tags"] = preg_replace('/{blank}/',' ',trim($row["tags"]));

    $nestedData=[];
    $row["ean"] = substr($row["ean"],0,3).'.'.substr($row["ean"],3,6);
    $nestedData[] = utf8_encode('<span style="line-height: 3;font-size: 14px;display: flex;align-items: center;justify-content: center;">'.utf8_decode($row["ean"]).'</span>');
    $nestedData[] = utf8_encode('<span>'.preg_replace('/(' . strtolower($search_text) . ')/Usi', '<span class="result">\\1</span>',utf8_decode(html_entity_decode($row["name"]))).'</span><br>'.
        '<span style="font-size: 12px;">[ '.preg_replace('/(' . strtolower($search_text) . ')/Usi', '<span class="result">\\1</span>',utf8_decode(html_entity_decode($row["tags"]))).' ]</span>');
    $data[] = $nestedData;
}

$json_data = array(
    "draw"            => 1,   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
    "recordsTotal"    => 0,  // total number of records
    "recordsFiltered" => 0, // total number of records after searching, if there is no searching then totalFiltered = totalData
    "data"            => $data   // total data array
);

header('Content-type: application/x-javascript');
$jsonp = preg_match('/^[$A-Z_][0-9A-Z_$]*$/i', $_GET['callback']) ? $_GET['callback'] : false;
if ( $jsonp ) {
    echo $jsonp.'('.json_encode($json_data).');';
}