<?php
// 用參數決定載入某頁並讀取需要的資料
// $route = new Router(Request::uri()); //搭配 .htaccess 排除資料夾名稱後解析 URL
$parameter = strtolower($route->getParameter(1));
$controller_array = scandir('controller');
$controller_array = array_change_key_case($controller_array, CASE_LOWER);

if (in_array($parameter.'.php', $controller_array)) {
  include( 'controller/'.$parameter.'.php' );
}else{
  include( 'controller/index.php' );
}