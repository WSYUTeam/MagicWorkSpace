<?php
require_once "inc/config.inc.php";
//select($table, $fields="*", $sqlwhere="", $orderby="")
//读取数据信息 
$data_all = $mysql_excel ->select("excel_sheet");
//计数
$data_column = $mysql_excel ->select("excel_sheet", "", "", "", "1");
//显示表头,排除主键  如 SHOW COLUMNS FROM  `excel_sheet`
$head = $mysql_excel ->table_columns("excel_sheet", "id");
?>
<table border="1" width="100%">
   <tr>
        <?php 
        $str_header = [];
        for ($i=0;$i<count($head);$i++){   
            echo '<th height="22" align="center" valign="middle">';
            echo $head[$i]['Field'];
            $str_header[] = $head[$i]['Field'];
            echo '</th>';
            
        }
        ?>
  </tr>
  <?php
  for ($i_data=0;$i_data<count($data_all);$i_data++){ 
        echo '<tr>';
        for($content_i=0; $content_i<count($str_header); $content_i++) {
            echo '<td height="22" align="center" valign="middle">';
            echo $data_all[$i_data][$str_header[$content_i]];
            echo '</td>';
        }
        echo '</tr>';
    }
    ?>
</table>
