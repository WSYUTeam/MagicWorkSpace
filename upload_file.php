<?php
require_once "inc/config.inc.php";
if(!isset($_FILES["file"]) ) {
    exit("文件未上传！！");
}
// 允许上传的图片后缀
$allowedExts = array("xls", "xlsx");
$temp = explode(".", $_FILES["file"]["name"]);
// echo $_FILES["file"]["size"];
$extension = end($temp);     // 获取文件后缀名
// echo $_FILES["file"]["type"];
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
        if (file_exists("upload_excel/" . $_FILES["file"]["name"])) {
            echo $_FILES["file"]["name"] . " 文件已经存在。";
        } else {
            // 如果 upload 目录不存在该文件则将文件上传到 upload_excel 目录下
            $new_file = mt_rand().date('Ymdhis').".".$extension;
            move_uploaded_file($_FILES["file"]["tmp_name"], "upload_excel/" . $new_file);
            $data['file_name'] = '"'.$_FILES["file"]["name"].'"';
            $data['upload_file_name'] = '"'.$new_file.'"';
            $position = "upload_excel/" . $new_file;
            $data['position'] = '"'.$position.'"';
            $data['control_date'] = '"'.date("Y-m-d H:i:s").'"';
            $data['control_person'] = '"'.'无名氏'.'"';//后期加
            echo "文件存储在: " . $position;
//             print_r($data);
            $mysql_excel ->insert($upload_database_name, $data);
            //临时先自动关联下，自动生成 
            $fileName = "upload_excel/$new_file";
            require_once "excel_import.php";
        }
    }
} else {
    echo "非法的文件格式";
}
?>