<?php
// ライブラリ読み込み
//require_once('./google-api-php-client/src/Google/autoload.php');
require __DIR__ . '/vendor/autoload.php';

// セッションスタート
session_start();
$client = new Google_Client();
// クライアントID
$client->setClientId('1052469165840-gjm95bd1kft9bb9e64ur1nhk3b976jfj.apps.googleusercontent.com');
// クライアントシークレット
$client->setClientSecret('UHqIdm51S4ZVEv_4YXSSypzS');
// リダイレクトURI
$client->setRedirectUri('https://proud-lead-172005.appspot.com/oauth-sample');

// 許可されてリダイレクトされると URL に code が付加されている
// code があったら受け取って、認証する
if (isset($_GET['code'])) {
    // 認証
    $client->authenticate($_GET['code']);
    $_SESSION['token'] = $client->getAccessToken();
    // リダイレクト GETパラメータを見えなくするため（しなくてもOK）
    header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
    exit;
}

// セッションからアクセストークンを取得
if (isset($_SESSION['token'])) {
    // トークンセット
    $client->setAccessToken($_SESSION['token']);
}

// トークンがセットされていたら
if ($client->getAccessToken()) {
    try {
        echo "Google Drive Api 連携完了！<br>";
        $_SESSION['client'] = $client;
    } catch (Google_Exception $e) {
        echo $e->getMessage();
    }
} else {
    // 認証用URL取得
    $client->setScopes(Google_Service_Drive::DRIVE);
    $authUrl = $client->createAuthUrl();
    echo '<a href="'.$authUrl.'">アプリケーションのアクセスを許可してください。</a>';
    exit;
}

?>
<a href="list">ファイル一覧</a><br>
<a href="data-init">データ投入</a>