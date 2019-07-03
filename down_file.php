<?php

// $file = '/home/zhouj/2/168412436520190703110953.xlsx';

$file = $_GET['file'];
if(file_exists($file)){
	header("Content-type:application/octet-stream");
	$filename = basename($file);
	header("Content-Disposition:attachment;filename = ".$filename);
	header("Accept-ranges:bytes");
	header("Accept-length:".filesize($file));
	readfile($file);
}else{
  	header("Content-type:text/html;charset=utf-8");
    echo "下载文件不存在！";
}

?>
