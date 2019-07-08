<?php
require_once "inc/config.inc.php";
echo "<h2>".$_SESSION['username']." ---";
if(isset($_GET['user_table_name'])) {
	$_SESSION['user_table_name'] = $_GET['user_table_name'];
}
echo " 表".$_SESSION['user_table_name']."空间 &nbsp; &nbsp; &nbsp; &nbsp; <a href='choose_upload.php'>返回上传文件</a></h2>";
if(!empty($_POST)) {
    //     print_r($_POST);
    $str_post = $str_post_log =  implode(' , ', $_POST);
    $str_post = preg_replace('/(\w+)/', '`${1}`', $str_post);
    //判断提交是否非法
    $return_result =$mysql_excel ->sql_save($str_post);
    //     echo "select id from `excel_sheet`  Where id not In (Select MIN(id) from `excel_sheet`   GROUP BY  ".$str_post." ASC)   order by id asc";
    $delete_content = $mysql_excel ->query_sql("select id from `".$_SESSION['user_table_name']."`  Where id not In (Select MIN(id) from `".$_SESSION['user_table_name']."`   GROUP BY  ".$str_post." ORDER BY ".$str_post." ASC)  ");//order by id asc
    //     print_r($delete_content);
    for ($i_data=0;$i_data<count($delete_content);$i_data++){
        //去重
        $mysql_excel ->query_sql("DELETE FROM `".$_SESSION['user_table_name']."` WHERE id = ".$delete_content[$i_data]['id']);
    }
    if(count($delete_content)>0) {
        $str_post_log = preg_replace('/(\w+)/', '${1}', $str_post_log);
        $mysql_excel ->query("INSERT INTO `".$_SESSION['user_table_name']."_log` (`id`, `log`, `log_person`, `log_date`) VALUES (NULL, '".$str_post_log."', '".$_SESSION['username']."', '".date('Y-m-d H:i:s')."');");
    }
}
//读取数据信息 
$data_all = $mysql_excel ->select($_SESSION['user_table_name'], "", "ORDER BY `id` ASC ");
//计数
$data_column = $mysql_excel ->select($_SESSION['user_table_name'], "", "", "", "1");
//显示表头,排除主键  如 SHOW COLUMNS FROM  `excel_sheet`
$head = $mysql_excel ->table_columns($_SESSION['user_table_name'], "id");

?>
<form method="post" action="">
    <input type="submit"  value="排重<?php //echo iconv('GB2312', 'UTF-8', '排重');?>"/> 总条数<?php //echo iconv('GB2312', 'UTF-8', '总条数');?><?php echo count($data_all);?>
     <a href="excel_export.php?user_table_name=<?php echo $_SESSION['user_table_name'];?>" style="margin-left: 400px;">导出<?php //echo iconv('GB2312', 'UTF-8', '导出');?></a>
    <table border="1" width="100%" style="font-size: 12px">
       <tr>
            <?php 
            $str_header = [];
            for ($i=0;$i<count($head);$i++){   
                echo '<th height="22" align="center" valign="middle" nowrap>';
                echo $head[$i]['Field'];
                $str_header[] = $head[$i]['Field'];
                echo '<input type="checkbox" name="'.$head[$i]['Field'].'" value="'.$head[$i]['Field'].'" /> ';//checked="checked" 
                echo '</th>';
                
            }
            ?>
      </tr> 
      <?php
      for ($i_data=0;$i_data<count($data_all);$i_data++){ 
            echo '<tr>';
            for($content_i=0; $content_i<count($str_header); $content_i++) {
                echo '<td   valign="middle">';
                echo $data_all[$i_data][$str_header[$content_i]];
                echo '</td>';
            }
            echo '</tr>';
        }
        ?>
    </table>
</form>