<?php
if (!session_id()) {
    session_start();
}

date_default_timezone_set('Asia/Taipei');

if(isset($_POST))$_POST = GUMP::xss_clean($_POST);

$route = new Router(Request::uri()); //搭配 .htaccess 排除資料夾名稱後解析 URL

$msg = new \Plasticbrain\FlashMessages\FlashMessages();

$fb = new Facebook\Facebook([
    'app_id' => Config::FB_APP_ID, // Replace {app-id} with your app id
    'app_secret' => Config::FB_APP_SECRET,
    'default_graph_version' => 'v2.2',
]);

