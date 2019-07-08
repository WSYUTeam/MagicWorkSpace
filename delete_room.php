<?php
require_once "inc/config.inc.php";
if(!isset($_SESSION['username'])) {
    exit ("<h2>您没有权限登陆该空间!</h2>");
} 
echo "<h2>".strtolower($_SESSION['username'])."空间</h2>";
$file_paths = explode('_', $_GET['del_user_table_name']);

$file_path = '../upload_excel/'.$file_paths[0].'/'.$file_paths[1].'/';
$files = scandir($file_path);
foreach ($files as $key => $value) {
	// echo "../upload_excel/".$file_paths[0]."/".$file_paths[1]."/".$value.'<br>';
	unlink ("../upload_excel/".$file_paths[0]."/".$file_paths[1]."/".$value);
}
rmdir($file_path);

//DROP TABLE ` zoetan_2 `
$mysql_excel ->query_sql("UPDATE `create_table_record` SET `room` = '1' WHERE `create_table_record`.`user_table_name` = '".$_GET['del_user_table_name']."';");
$mysql_excel ->query_sql("DROP TABLE `".$_GET['del_user_table_name']."`");
$mysql_excel ->query_sql("INSERT INTO `".$_GET['del_user_table_name']."_log` (`id`, `log`, `log_person`, `log_date`) VALUES (NULL, '该空间已删除', '".$_SESSION['username']."', '".date('Y-m-d H:i:s')."');");
// echo "<h2>该子空间删除成功！</h2>";
header('Location: choose_upload.php');
?>