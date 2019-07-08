<?php
// header("Content-type:text/html;charset=utf-8");//utf-8
// include  "Classes/pinyin.php";
// require_once "Classes/Pdodb.php";
require_once "inc/config.inc.php";
ini_set('memory_limit', '-1');
// echo pinyin('定义和用法');
if (!isset($fileName)) { 
    $fileName = "";
}
// if (isset($_GET['file_name'])) { 
//     $fileName = $_GET['file_name'];
// }
//此句临时加的，需要分开写，不要合并
if (!file_exists($fileName)) {
    echo "文件不存在!";
    return ;
}

// //链接数据库
// $mysql_config=array(
//     'dsn'=>'mysql:dbname=excel_db;host=localhost',//数据库服务器地址
//     'username'=>'root',
//     'password'=>'123',

// );
// $mysql_excel = new Pdodb($mysql_config);

// 引入PHPExcel
require_once dirname(__FILE__) . "/Classes/PHPExcel/IOFactory.php";
// 载入当前文件
$phpExcel = PHPExcel_IOFactory::load($fileName);
// 设置为默认表
$phpExcel->setActiveSheetIndex(0);
// 获取表格数量
// $sheetCount = $phpExcel->getSheetCount();
// 获取行数
$row = $phpExcel->getActiveSheet()->getHighestRow();
// 获取列数
$column = $phpExcel->getActiveSheet()->getHighestColumn();
// echo  "表格数目为：$sheetCount" . "表格的行数：$row" . "列数：$column";
        // echo  PHPExcel_Cell::columnIndexFromString($column);  //将列数转换为数字 列数大于Z的必须转  A->1  AA->27 ."<br>";

$data = [];
// 行数循环
for ($i = 1; $i <= $row; $i++) {
    $line_data = [];
    // 列数循环
    $str_sub = 0;
    $str_sql_field = "INSERT INTO `$database_name` (";
    $str_sql_value = " VALUES (";
    // for ($c = 'A'; $c <= $column; $c++) {
    for ($c = 'A'; $c <= 1; $c++) {//写1可以无限制的循环，在结尾让其跳出
        $get_cell = $phpExcel->getActiveSheet()->getCell($c . $i);
        // 获取excel 的文本
         $cell = $get_cell->getValue();
        //是否为date类型，是将转换为时间格式
        if($get_cell->getDataType()==PHPExcel_Cell_DataType::TYPE_NUMERIC){
            $cellstyleformat=$get_cell->getStyle($get_cell->getCoordinate())->getNumberFormat();
            //             $cellstyleformat=$get_cell->getParent()->getStyle( $get_cell->getCoordinate() )->getNumberFormat();
            $formatcode = $cellstyleformat->getFormatCode();
            if (preg_match('/^(\[\$[A-Z]*-[0-9A-F]*\])*[hmsdy]/i', $formatcode)) {
                $cell = gmdate("Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($cell));
            } else {
                $cell =\PHPExcel_Style_NumberFormat::toFormattedString($cell,$formatcode);
            }
            //echo PHPExcel_Style_NumberFormat::toFormattedString($value,$formatcode);
        }
        // 开始格式化
        if(is_object( $cell))  $cell= $cell->__toString();
        //对表头的处理
        if($i==1) { 
            $comment = $cell;
            $cell = pinyin($cell);
            $cell = str_replace(" ","_",$cell);
            //"ALTER TABLE  `excel_sheet` ADD  `".$cell."` VARCHAR( 22 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT  '".$comment."'";
            //插入列
            $mysql_excel ->add_column("$database_name", $cell, $comment);
            $line_data[] = $cell;
        } else {
//             echo $str_sub.'<br>';
            $line_data[$data[0][$str_sub]] = $cell;
            $str_sql_field .= ('`'.$data[0][$str_sub].'`');
            $str_sql_value .= ("'".$line_data[$data[0][$str_sub]]."'");
            //echo count($data[0]);
            //末尾的判断 如：INSERT INTO `excel_sheet` (`fulltext`, `type`, `yu_yan`, `title`, `year`, `media`, `issn`, `author`, `keywords`, `fund`, `abstract`, `quote`)
            if((count($data[0])-1) != $str_sub) {
                $str_sql_field .= ', ';
                $str_sql_value .= ', ';
            } else {
                $str_sql_field .= ') ';
                $str_sql_value .= ') ';
            }
//             echo $line_data[$data[0][$str_sub]].'<br>';
            $str_sub++;
        }     
        if($c=='IV') {
        	break;
        }
    }
    //组装的sql语句，调试时打开
//     echo $str_sql_field.$str_sql_value.'<p>';
    //插入数据
    $mysql_excel ->query($str_sql_field.$str_sql_value);
    $data[] = $line_data;
//     echo "<pre>";
//     print_r($line_data);
//     echo "</pre>";
}
// echo $str_sub;
// echo "<pre>";
// print_r($data[0]);
// // echo $data[0][1];
// echo "</pre>";
echo "<p>";
$mysql_excel ->close();
?>
<p><a href="excel_table.php" style="margin-left: 400px;">查看</a>