<?php
//require_once('./google-api-php-client/src/Google/autoload.php');
//require_once('./google-api-php-client/src/Google/Service/Drive.php');
require __DIR__ . '/vendor/autoload.php';

//$client変数の型を明示させるためsession_start()はライブラリ読み込みの後に記述
session_start();

$client = $_SESSION['client'];
$service = new Google_Service_Drive($client);

try {
    // 親フォルダ
    // root でマイドライブ, root 以外は名前ではなく ID を指定
    $parents = 'root';
    if (isset($_GET['parents'])) {
        $parents = htmlspecialchars($_GET['parents'], ENT_QUOTES);
    }
    // 次ページに移動する場合に渡すトークン
    $pageToken = null;
    if (isset($_GET['pageToken'])) {
        $pageToken = $_GET['pageToken'];
    }
    $parameters = array('q' => "'{$parents}' in parents", 'pageSize' => 20);
    if ($pageToken) {
        $parameters['pageToken'] =$pageToken;
    }
    // ファイルリスト取得, Google_Service_Drive_FileList のオブジェクトが返ってくる
    $files = $service->files->listFiles($parameters);
    // ファイルの一覧データ
    $results = $files->getFiles();
    // 次ページのトークン取得, ない場合は NULL
    $pageToken = $files->getNextPageToken();
    // 結果表示
    foreach ($results as $result) {
        if ($result->mimeType == 'application/vnd.google-apps.folder') {
            echo 'フォルダ ：<a href="http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?parents='.urlencode($result->id).'">'.$result->title.'</a> ID:'.$result->id.'<br />';
        } else {
            echo 'ファイル ：<a href="read.php?id='.$result->id.'"">'.$result->title.'</a> ID：'.$result->id.' name:'.$result->name.'<br />';
        }
    }
    // pageToken があったら次ページヘのリンク表示
    if ($pageToken) {
        echo '<a href="http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?parents='.urlencode($parents).'&pageToken='.urlencode($pageToken).'">次ページ</a>';
    }
}catch(Google_Exception $e){
    echo $e->getMessage();
}