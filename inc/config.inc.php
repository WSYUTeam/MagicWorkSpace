<?php
header("Content-type:text/html;charset=utf-8");//utf-8
require_once  "Classes/pinyin.php";
require_once "Classes/Pdodb.php";
date_default_timezone_set('PRC');

//链接数据库
$mysql_config=array(
    'dsn'=>'mysql:dbname=excel_db;host=localhost',//数据库服务器地址
    'username'=>'root',
    'password'=>'Wsyu&2018',
    
);
$mysql_excel = new Pdodb($mysql_config);

// if(isset($_SESSION['username']) && basename($_SERVER['PHP_SELF']) != 'login.php') {
// 	session_start();
//     // echo "<h2>您没有权限登陆该空间</h2>";
// } else if(isset($_POST['username']) && basename($_SERVER['PHP_SELF']) == 'choose_upload.php' ) {
// 	session_start();
// } else 
if(basename($_SERVER['PHP_SELF']) != 'login.php' ) {//&& basename($_SERVER['PHP_SELF']) != 'choose_upload.php'
	session_start();
	if(!isset($_SESSION['username']) && !isset($_POST['username'])) {
		exit ("<h2>您没有权限登陆该空间!!</h2>");
	}
}

$upload_database_name = 'upload_file';