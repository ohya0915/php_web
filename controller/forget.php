<?php
//if logged in redirect to members page
if(UserVeridator::isLogin(isset($_SESSION['username'])?$_SESSION['username']:'')){
  header('Location: home'); 
  exit();
}

//define page title
$title = 'Forget';
include('view/header.php'); // 載入共用的頁首
include('view/forget.php');    // 載入忘記密碼的頁面
include('view/footer.php'); // 載入共用的頁尾