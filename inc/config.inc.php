<?php
header("Content-type:text/html;charset=utf-8");//utf-8
require_once  "Classes/pinyin.php";
require_once "Classes/Pdodb.php";
date_default_timezone_set('PRC');


//�������ݿ�
$mysql_config=array(
    'dsn'=>'mysql:dbname=excel_db;host=localhost',//���ݿ��������ַ
    'username'=>'root',
    'password'=>'123',
    
);
$mysql_excel = new Pdodb($mysql_config);
$database_name = 'excel_sheet';
$upload_database_name = 'upload_file';