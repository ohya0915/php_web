<?php
/**
 * 登入成功才能查看本頁面
 */
if(UserVeridator::isLogin(isset($_SESSION['username'])?$_SESSION['username']:'')){
  include('view/header.php'); // 載入共用的頁首
  include('view/home.php');     // 載入登入用的頁面
  include('view/footer.php'); // 載入共用的頁尾
}
else{
  header('Location: logout');
}