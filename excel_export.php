<?php
require_once "inc/config.inc.php";
/**
 * 数据导出
* @param array $title   标题行名称
* @param array $data   导出数据
* @param string $fileName 文件名
* @param string $savePath 保存路径  
* @param boolean $isDown 是否下载  false--保存   true--下载
* @return string   返回文件全路径
* @throws PHPExcel_Exception
* @throws PHPExcel_Reader_Exception
*/
if(isset($_GET['user_table_name'])) {
    $user_table_name = $_GET['user_table_name'];
} else {
    exit("请正常进入该界面！");
}
function exportExcel($title=array(), $data=array(), $fileName='', $savePath='./', $isDown=false){    
    include('Classes/PHPExcel.php');
    $obj = new PHPExcel();  
    //横向单元格标识   
    $cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ'); 
    $obj->getActiveSheet(0)->setTitle('sheet1');   //设置sheet名称    
    $_row = 1;   //设置纵向单元格标识 
//     print_r($data);
//     exit;
    if($title){       
        $_cnt = count($title);   
        $obj->getActiveSheet(0)->mergeCells('A'.$_row.':'.$cellName[$_cnt-1].$_row);   //合并单元格        
        $obj->setActiveSheetIndex(0)->setCellValue('A'.$_row, ('new_sheet'.date("md-his")));  //设置合并后的单元格内容       
        $_row++;        
        $i = 0;        
        foreach($title AS $v){   //设置列标题            
            $obj->setActiveSheetIndex(0)->setCellValue($cellName[$i].$_row, $v);            
            $i++;           
        }        
        $_row++;       
    }           
    //填写数据    
    if($data){        
        $i = 0;        
        foreach($data AS $_v){           
            $j = 0;          
            foreach($_v AS $_cell){                
                $obj->getActiveSheet(0)->setCellValue($cellName[$j] . ($i+$_row), $_cell);                
                $j++;                
            }            
            $i++;           
        }       
    }        
    //文件名处理   
    if(!$fileName){        
        $fileName = uniqid(time(),true);       
    }    
    $objWrite = PHPExcel_IOFactory::createWriter($obj, 'Excel2007');   
    if($isDown){   //网页下载        
        header('pragma:public');        
        header("Content-Disposition:attachment;filename=$fileName.xlsx");        
        $objWrite->save('php://output');exit;        
    }
    $_fileName = iconv("utf-8", "gb2312", $fileName);   //转码    
    $_savePath = $savePath.$_fileName.'.xlsx';    
    $objWrite->save($_savePath);   
    return $savePath.$fileName.'.xlsx';    
}
//显示表头,排除主键  如 SHOW COLUMNS FROM  `excel_sheet`
$head = $mysql_excel ->table_columns($user_table_name, "id");
$str_header = [];
for ($i=0;$i<count($head);$i++){
    $str_header[] = $head[$i]['Field'];
}
//读取数据信息
$str_content = [];
$data_all = $mysql_excel ->select($user_table_name, "", "ORDER BY `title` ASC ");
for ($i_data=0;$i_data<count($data_all);$i_data++){
//     $str_content_e = [];
    for($content_i=0; $content_i<count($str_header); $content_i++) {
        $str_content[$i_data][] = $data_all[$i_data][$str_header[$content_i]];
    }
//     $str_content[] = $str_content_e[];
}
// echo "<pre>";
// print_r($str_content);
// echo "</pre>";
// echo "<pre>";
// print_r(array(array('a',21),array('b',23)));
// echo "</pre>";

exportExcel($str_header, $str_content, ('new_sheet'.date("md-his")), './', true);//array(array('a',21),array('b',23))