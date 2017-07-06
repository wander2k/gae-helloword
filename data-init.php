<?php
//require_once('./google-api-php-client/src/Google/autoload.php');
require __DIR__ . '/vendor/autoload.php';
use Google\Cloud\Datastore\DatastoreClient;

session_start();
$client = $_SESSION['client'];

// トークンがセットされていたら
try {
    echo "Google Drive Api 連携完了！<br>";
    $obj = $client->getAccessToken();
    $token = $obj['access_token'];
    //write($token);
    read($token);
} catch (Google_Exception $e) {
    echo $e->getMessage();
}

function write($token) {
    $key = '{スプレッドシートID}';
    $url = "https://spreadsheets.google.com/feeds/list/{$key}/od6/private/full";

    // 書き込みデータ
    $fields = '<entry xmlns="http://www.w3.org/2005/Atom" xmlns:gsx="http://schemas.google.com/spreadsheets/2006/extended">
        <gsx:id>0004</gsx:id>
        <gsx:name>umi</gsx:name>
        <gsx:age>16</gsx:age>
        </entry>';

    $context = stream_context_create(
        array(
            'http' => array(
                'method'=> 'POST',
                'header'=> "Content-Type: application/atom+xml\r\n"."Authorization: Bearer ".$token,
                'content' => $fields
            )
        )
    );

    $response = file_get_contents($url, false, $context);
    // ステータスコードがHTTP/1.1 201なら書き込みOK
    echo 'http status is ' . $http_response_header[0];
}


function read($token) {
    $key = '1mTGN0LoSXrslx5D1lf1K8Tg0JsRsotIOnGlOU6c6Qg8';
    $url = "https://spreadsheets.google.com/feeds/list/{$key}/od6/private/full";
    // age>16条件
    //$url = "https://spreadsheets.google.com/feeds/list/{$key}/od6/private/full?sq=age>16";
    // 一覧取得
    //$url = 'https://spreadsheets.google.com/feeds/spreadsheets/private/full';

    $context = stream_context_create(
        array(
            'http' => array(
                'method'=> 'GET',
                'header'=> "Authorization: Bearer ".$token
            )
        )
    );

    $response = file_get_contents($url, false, $context);
    echo 'http status is ' . $http_response_header[0];
    $obj = simplexml_load_string($response);

    /* 取得データ表示
    echo "<pre>";
    print_r($obj);
    echo "</pre>";
    */

    # Imports the Google Cloud client library
    
    # Your Google Cloud Platform project ID
    $projectId = 'proud-lead-172005';
    # Instantiates a client
    $datastore = new DatastoreClient([
        'projectId' => $projectId
    ]);
    # The kind for the new entity
    $kind = 'Gabage';

    foreach($obj->entry as $data) {
        $gsxNode = $data->children('gsx', true);
        //var_dump($gsxNode);
        //echo 'USER ID:'.$gsxNode->id.' Name:'.$gsxNode->name.' Age:'.$gsxNode->description.' 品目名:'.$gsxNode->品目名.'<br>';
        # The name/ID for the new entity
        //$id = $gsxNode->id;
        
        # The Cloud Datastore key for the new entity
        //$taskKey = $datastore->key($kind, $id);
        
        # Prepares the new entity
        //$gabage = $datastore->entity($taskKey, ['description' => $gsxNode->description, 'name'=> $gsxNode->name, 'name_jp' => $gsxNode->品目名]);z
        $gabage = $datastore->entity($kind);
        // $desc = (string)$gsxNode->name;
        // if (empty($desc)) {
        //     $gabage['description'] = "";
        // } elseif (strlen($desc) > 128) {
        //     $gabage['description'] = substr($desc, 0, 128);
        // } else {
        //     $gabage['description'] = $desc;            
        // }
        
        $gabage['name_en'] = (string)$gsxNode->name;
        $gabage['name_jp'] = (string)$gsxNode->品目名;
        // $tmp = (string)$gsxNode->類似語;
        // if (empty($tmp)) {
        //     $gabage['explain'] = "";
        // } elseif (strlen($tmp) > 128) {
        //     $gabage['explain'] =  substr($tmp, 0, 128);
        // } else {
        //     $gabage['explain'] =  $tmp;
        // }
        //
        $gabage['method_simple'] = (string)$gsxNode->methodsimple;
        $gabage['method_detail'] = (string)$gsxNode->methoddetail;
        $gabage['method_extend'] = (string)$gsxNode->methodextend;
        $gabage['url'] = (string)$gsxNode->url1;
        //var_dump($gabage);

        # Saves the entity
        $datastore->insert($gabage);
    }
    echo "<br>". "Data init finished!!". "<br>";
}