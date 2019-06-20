<?php
require_once "inc/config.inc.php";
if(!empty($_POST)) {
    //     print_r($_POST);
    $str_post =  implode(' , ', $_POST);
    $str_post = preg_replace('/(\w+)/', '`${1}`', $str_post);
    //判断提交是否非法
    $return_result =$mysql_excel ->sql_save($str_post);
    //     echo "select id from `excel_sheet`  Where id not In (Select MIN(id) from `excel_sheet`   GROUP BY  ".$str_post." ASC)   order by id asc";
    $delete_content = $mysql_excel ->query_sql("select id from `excel_sheet`  Where id not In (Select MIN(id) from `excel_sheet`   GROUP BY  ".$str_post." ORDER BY ".$str_post." ASC)  ");//order by id asc
    //     print_r($delete_content);
    for ($i_data=0;$i_data<count($delete_content);$i_data++){
        //去重
        $mysql_excel ->query_sql("DELETE FROM `excel_sheet` WHERE id = ".$delete_content[$i_data]['id']);
    }
}
//读取数据信息 
$data_all = $mysql_excel ->select("excel_sheet", "", "ORDER BY `title` ASC ");
//计数
$data_column = $mysql_excel ->select("excel_sheet", "", "", "", "1");
//显示表头,排除主键  如 SHOW COLUMNS FROM  `excel_sheet`
$head = $mysql_excel ->table_columns("excel_sheet", "id");

?>
<form method="post" action="">
    <input type="submit"  value="<?php echo iconv('GB2312', 'UTF-8', '排重');?>"/> <?php echo iconv('GB2312', 'UTF-8', '总条数').count($data_all);?>
     <a href="excel_export.php" style="margin-left: 400px;"><?php echo iconv('GB2312', 'UTF-8', '导出');?></a>
    <table border="1" width="100%">
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
                echo '<td height="22"  valign="middle">';
                echo $data_all[$i_data][$str_header[$content_i]];
                echo '</td>';
            }
            echo '</tr>';
        }
        ?>
    </table>
</form>