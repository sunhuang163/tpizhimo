<?php
/***
 * @Date 	2015/08/13
 * @Author 	banfg56
 * @@ 自定义登陆页面
***/
session_start();
header('Cache-control:private,must-revalidate');
$_SESSION['_from_admin_login_']   = TRUE ;
$location = "/index.php/admin/login/index";
header("Location:".$location);
?>