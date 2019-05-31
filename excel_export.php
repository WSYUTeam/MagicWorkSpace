<?php
require_once "inc/config.inc.php";
/**
 * ���ݵ���
* @param array $title   ����������
* @param array $data   ��������
* @param string $fileName �ļ���
* @param string $savePath ����·��  
* @param boolean $isDown �Ƿ�����  false--����   true--����
* @return string   �����ļ�ȫ·��
* @throws PHPExcel_Exception
* @throws PHPExcel_Reader_Exception
*/
function exportExcel($title=array(), $data=array(), $fileName='', $savePath='./', $isDown=false){    
    include('Classes/PHPExcel.php');
    $obj = new PHPExcel();  
    //����Ԫ���ʶ   
    $cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ'); 
    $obj->getActiveSheet(0)->setTitle('sheet1');   //����sheet����    
    $_row = 1;   //��������Ԫ���ʶ 
//     print_r($data);
//     exit;
    if($title){       
        $_cnt = count($title);   
        $obj->getActiveSheet(0)->mergeCells('A'.$_row.':'.$cellName[$_cnt-1].$_row);   //�ϲ���Ԫ��        
        $obj->setActiveSheetIndex(0)->setCellValue('A'.$_row, ('new_sheet'.date("md-his")));  //���úϲ���ĵ�Ԫ������       
        $_row++;        
        $i = 0;        
        foreach($title AS $v){   //�����б���            
            $obj->setActiveSheetIndex(0)->setCellValue($cellName[$i].$_row, $v);            
            $i++;           
        }        
        $_row++;       
    }           
    //��д����    
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
    //�ļ�������   
    if(!$fileName){        
        $fileName = uniqid(time(),true);       
    }    
    $objWrite = PHPExcel_IOFactory::createWriter($obj, 'Excel2007');   
    if($isDown){   //��ҳ����        
        header('pragma:public');        
        header("Content-Disposition:attachment;filename=$fileName.xlsx");        
        $objWrite->save('php://output');exit;        
    }
    $_fileName = iconv("utf-8", "gb2312", $fileName);   //ת��    
    $_savePath = $savePath.$_fileName.'.xlsx';    
    $objWrite->save($_savePath);   
    return $savePath.$fileName.'.xlsx';    
}
//��ʾ��ͷ,�ų�����  �� SHOW COLUMNS FROM  `excel_sheet`
$head = $mysql_excel ->table_columns("excel_sheet", "id");
$str_header = [];
for ($i=0;$i<count($head);$i++){
    $str_header[] = $head[$i]['Field'];
}
//��ȡ������Ϣ
$str_content = [];
$data_all = $mysql_excel ->select("excel_sheet", "", "ORDER BY `title` ASC ");
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