<?php
require_once "inc/config.inc.php";
echo "<h2>".$_SESSION['username']." ---";

if(!isset($_FILES["file"]) ) {
    exit("文件未上传！！");
}
unset($_SESSION['user_table_name']);
// 允许上传的图片后缀
$allowedExts = array("xls", "xlsx");
$temp = explode(".", $_FILES["file"]["name"]);
// echo $_FILES["file"]["size"];
$extension = end($temp);     // 获取文件后缀名
//echo reset($temp);   获取文件名
if(empty($_POST['post_table_name'])) {
    //创建新数据表和新文件夹 ------START
    //获取最大的值,并操作数据库  ---START
    $max_table_number_array = $mysql_excel ->query_sql("SELECT  MAX(SUBSTRING_INDEX(user_table_name,'_',-1)+0) AS max_table_number FROM `create_table_record` where user_table_name LIKE '".$_SESSION['username']."_%'");
    //该用户创建表的最大值  $max_table_number[0]['max_table_number']
    $max_table_number = (int)$max_table_number_array[0]['max_table_number']+1;
    $data_record['user_table_name'] = $_SESSION['username'].'_'.$max_table_number;
    $data_record['user_table_content'] = reset($temp);
    $mysql_excel ->insert("create_table_record", $data_record);
    //创建数据表
    $mysql_excel ->query("CREATE TABLE `".$data_record['user_table_name']."` (  `id` int(12) NOT NULL) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
    //表的索引
    $mysql_excel ->query("ALTER TABLE `".$data_record['user_table_name']."` ADD PRIMARY KEY (`id`);");
    //使用表AUTO_INCREMENT 
    $mysql_excel ->query("ALTER TABLE `".$data_record['user_table_name']."`  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;");

    //创建数据表log
    $mysql_excel ->query("CREATE TABLE `".$data_record['user_table_name']."_log` (  `id` int(12) NOT NULL,`log` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '操作对象',`log_person` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '操作人',`log_date` datetime DEFAULT NULL COMMENT '操作时间') ENGINE=MyISAM DEFAULT CHARSET=utf8;");
    //log表的索引
    $mysql_excel ->query("ALTER TABLE `".$data_record['user_table_name']."_log` ADD PRIMARY KEY (`id`);");
    //使用表AUTO_INCREMENT 
    $mysql_excel ->query("ALTER TABLE `".$data_record['user_table_name']."_log`  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;");
    //获取最大的值,并操作数据库  ---END
    //excel导入的数据表名
    $database_name = $_SESSION['user_table_name'] = $data_record['user_table_name'];
    // 创建文件空间
    $new_position  = "/home/".$_SESSION['username']."/".$max_table_number."/";
    mkdir($new_position, 0777);
    //创建新数据表和新文件夹 ------END
} else {
    $database_name = $_SESSION['user_table_name'] = $_POST['post_table_name'];
    $max_table_numbers = explode("_",$_POST['post_table_name']);
    $max_table_number = $max_table_numbers[1];
    $new_position  = "/home/".$_SESSION['username']."/".$max_table_number."/";
}
echo " 表".$max_table_number."空间 &nbsp; &nbsp; &nbsp; &nbsp; <a href='choose_upload.php'>返回上传文件</a></h2>";// 
// 提交文件 -----START
if ((($_FILES["file"]["type"] == "application/vnd.ms-excel")
    || ($_FILES["file"]["type"] == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"))
    && ($_FILES["file"]["size"] < 204800)   // 小于 200 Mb
    && in_array($extension, $allowedExts)) {
    if ($_FILES["file"]["error"] > 0) {
        echo "错误：: " . $_FILES["file"]["error"] . "<br>";
    } else {
        echo "上传文件名: " . $_FILES["file"]["name"] . "<br>";
//         echo "文件类型: " . $_FILES["file"]["type"] . "<br>";
        echo "文件类型: " . $extension . "<br>";
        echo "文件大小: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
//         echo "文件临时存储的位置: " . $_FILES["file"]["tmp_name"] . "<br>";       
        // 判断当期目录下的 upload_excel 目录是否存在该文件
        // 如果没有 upload_excel 目录，你需要创建它，upload_excel 目录权限为 777
        if (file_exists("$new_position" . $_FILES["file"]["name"])) {
            echo $_FILES["file"]["name"] . " 文件已经存在。";
        } else {
            // 如果 upload 目录不存在该文件则将文件上传到 upload_excel 目录下
            $new_file = mt_rand().date('Ymdhis').".".$extension;
            move_uploaded_file($_FILES["file"]["tmp_name"], "$new_position" . $new_file);
            $data['file_name'] = $_FILES["file"]["name"];
            $data['upload_file_name'] = $new_file;
            $position = "$new_position" . $new_file;
            $data['position'] = $position;
            $data['control_date'] = date("Y-m-d H:i:s");
            $data['control_person'] = $_SESSION['username'];//后期加
            echo "文件存储在: " . $position;
//             print_r($data);
            $mysql_excel ->insert($upload_database_name, $data);
            //临时先自动关联下，自动生成 
            $fileName = "$new_position$new_file";
            require_once "excel_import.php";
        }
    }
} else {
    echo "非法的文件格式";
}
?>