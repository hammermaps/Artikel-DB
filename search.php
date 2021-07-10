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
define('CONTROLLER_PATH', SCRIPT_PATH.'/controller');

require_once VENDOR_PATH."/autoload.php";
require_once INCLUDE_PATH."/common.inc.php";

$common = new common(true);
$requestData= $_POST;
$is_edit_mode = false;
if(isset($_GET['type']) && $_GET['type'] === 'edit'
    && $common->getUsers()->is_logged()) {
    $is_edit_mode = true;
}

// getting total number records without any search
$sql = "SELECT `id` FROM artikel;";
$CachedString = $common->getCache()->getItem(sha1($sql));
if ($is_edit_mode || is_null($CachedString->get())) {
    $query = $common->database->query($sql);
    $totalData = $query->getRowCount();
    $CachedString->set($totalData)->expiresAfter(30)->addTag('data');
    $common->cache->save($CachedString);
    $totalData = $totalFiltered = $CachedString->get();
} else {
    $totalData = $totalFiltered = $CachedString->get();
}

$query = $common->database->query($sql);
$totalData = $totalFiltered = $query->getRowCount();

if(!array_key_exists('search',$requestData)) {
    $requestData['search']['value'] = '';
}

if(!array_key_exists('start',$requestData)) {
    $requestData['start'] = 0;
    $requestData['length'] = 10;
}

if(!array_key_exists('draw',$requestData)) {
    $requestData['draw'] = 1;
}

if(!array_key_exists('order',$requestData)) {
    $requestData['order'][0]['column'] = 1;
    $requestData['order'][0]['dir'] = 'asc';
}

if(!empty($requestData['search']['value']) ) {
    $requestData['search']['value'] = str_replace(['/','\\'],'-',$requestData['search']['value']);
    $sort_by = $requestData['order'][0]['column'] ? 'ean' : 'name';
    $sql = "SELECT *";
    $sql.=" FROM `artikel`";
    $sql.=" WHERE `tags` LIKE '%".htmlentities(str_replace(' ','{blank}',$requestData['search']['value']), ENT_COMPAT)."%'";
    $sql.=" OR `name` LIKE '%".htmlentities(str_replace(' ','{blank}',$requestData['search']['value']), ENT_COMPAT)."%'";
    //$sql.=" OR MATCH (`tags`, `name`) AGAINST ('".htmlentities(str_replace(' ','{blank}',$requestData['search']['value']), ENT_COMPAT)."' IN NATURAL LANGUAGE MODE)";
    $sql.=" ORDER BY `".$sort_by."` ".strtoupper($requestData['order'][0]['dir'])." LIMIT ".intval($requestData['start']).",".intval($requestData['length']).";";

    $query = $common->database->query($sql);
    $totalFiltered = $query->getRowCount();

    $sql.=" LIMIT ".$requestData['start'].",".$requestData['length'];

    $CachedString = $common->cache->getItem(sha1($sql));
    if ($is_edit_mode || is_null($CachedString->get())) {
        $query = $common->database->query($sql);
        $rows = $query->fetchAll();
        $CachedString->set(serialize($rows))->expiresAfter(10);
        $common->cache->save($CachedString);
        $rows = unserialize($CachedString->get());
    } else {
        $rows = unserialize($CachedString->get());
    }

    $search_text =  utf8_decode(html_entity_decode($requestData['search']['value']));
} else {
    $sort_by = $requestData['order'][0]['column'] ? 'ean' : 'name';
    $sql = "SELECT * FROM `artikel` ORDER BY `".$sort_by."` ".strtoupper($requestData['order'][0]['dir'])." LIMIT ".intval($requestData['start']).",".intval($requestData['length']).";";
    $CachedString = $common->cache->getItem(sha1($sql));
    if ($is_edit_mode || is_null($CachedString->get())) {
        $query = $common->database->query($sql);
        $rows = $query->fetchAll();
        $CachedString->set(serialize($rows))->expiresAfter(10);
        $common->cache->save($CachedString);
        $rows = unserialize($CachedString->get());
    } else {
        $rows = unserialize($CachedString->get());
    }
    $search_text = '';
}

$data = [];
//$data[] = [0=>'',1=>'<pre>'.print_r($rows,true)];
foreach ($rows as $row) {  // preparing an array
    $row["tags"] = preg_replace('/,/',', ',$row["tags"]);
    $row["tags"] = preg_replace('/{blank}/',' ',trim($row["tags"]));
    $row["name"] = preg_replace('/{blank}/',' ',trim($row["name"]));

    $nestedData=[];
    $row["ean"] = substr($row["ean"],0,3).'.'.substr($row["ean"],3,6);
    $nestedData[] = utf8_encode('<span style="line-height: 3;font-size: 14px;display: flex;align-items: center;justify-content: center;">'.utf8_decode($row["ean"]).'</span>');
    $nestedData[] = utf8_encode('<span>'.preg_replace('/(' . strtolower($search_text) . ')/Usi', '<span class="result">\\1</span>',utf8_decode(html_entity_decode($row["name"]))).'</span><br>'.
        '<span style="font-size: 12px;">[ '.preg_replace('/(' . strtolower($search_text) . ')/Usi', '<span class="result">\\1</span>',utf8_decode(html_entity_decode($row["tags"]))).' ]</span>');

    if($is_edit_mode) {
        $nestedData[] = '<div class=“btn-group buttons”><form role="form" action="edit_'.utf8_decode($row["id"]).'.html" method="post" ><span style="margin-left: 20px;">'.
            '<button id="target" class="btn btn-default" type="submit"><i aria-hidden="true" class="fa fa-pencil-alt"></i></button></form></div>';

        $nestedData[] = '<div class=“btn-group buttons”><form role="form" action="delete_'.utf8_decode($row["id"]).'.html" method="post" ><span style="margin-left: 20px;">'.
            '<button class="btn btn-danger" type="submit"><i aria-hidden="true" class="fa fa-times"></i></button></span></form></div>';
    }

    $data[] = $nestedData;
}

$json_data = array(
    "draw"            => intval($requestData['draw']),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
    "recordsTotal"    => intval($totalData),  // total number of records
    "recordsFiltered" => intval($totalFiltered), // total number of records after searching, if there is no searching then totalFiltered = totalData
    "data"            => $data   // total data array
);

header('Content-type: application/x-javascript');
$jsonp = preg_match('/^[$A-Z_][0-9A-Z_$]*$/i', $_GET['callback']) ? $_GET['callback'] : false;
if ( $jsonp ) {
    echo $jsonp.'('.json_encode($json_data).');';
}