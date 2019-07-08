<?php
require_once "inc/config.inc.php";
if(!isset($_POST['username']) && !isset($_SESSION['username'])) {
    exit ("<h2>您没有权限登陆该空间!</h2>");
} 
if(!empty($_SESSION['username'])) {
	$_POST['username'] = $_SESSION['username'];
}
$_POST['username'] = pinyin($_POST['username']);
$_POST['username'] = str_replace(" ","",$_POST['username']);
echo "<h2>".strtolower($_POST['username'])."空间</h2>";
$mysql_excel ->sql_save($_POST['username']);
$_SESSION['username'] = strtolower($_POST['username']);
if(!is_dir('../upload_excel/'.$_SESSION['username'])) {
	mkdir(("../upload_excel/".$_SESSION['username']), 0777);
}
?>
<html>
<head>
<meta charset="utf-8">
<title>数据清洗平台</title>
</head>
<body>

<form action="upload_file.php" method="post" enctype="multipart/form-data">
	<label for="file">已存在空间：</label>
	<select size="10" name="post_table_name" >
		<option style="color: blue">请选择存储空间：</option>
	  <?php
			//读取数据信息 
	        $where[] = " room IS NULL AND user_table_name LIKE '".$_SESSION['username']."_%'";
			$data_space = $mysql_excel ->select("create_table_record", $where, "ORDER BY `record_id` ASC ");
			for ($i_data=0;$i_data<count($data_space);$i_data++){ 
				echo '<option value ="'.$data_space[$i_data]['user_table_name'].'">&nbsp; &nbsp; &nbsp;'.$data_space[$i_data]['user_table_content'].'</option>';
		    }
		?>
	</select><p>
	<label for="file">文件名：</label>
	<input type="file" name="file" id="file"><br>
	<input type="submit" name="submit" value="提交">
</form>
<hr>
<table  cellpadding="20">
	<tr>
		<td >
			<table  cellpadding="10">
				<!-- <tr><th nowrap>存储空间<br>（第一次提交的表名）</th><th nowrap>查看导入数据</th><th nowrap>查看导入文件</th></tr> -->
				<?php
					//读取数据信息 
			        $where[] = " room IS NULL AND user_table_name LIKE '".$_SESSION['username']."_%'";
					$data_space = $mysql_excel ->select("create_table_record", $where, "ORDER BY `record_id` ASC ");
					for ($i_data=0;$i_data<count($data_space);$i_data++){ 
						$user_num = explode("_", $data_space[$i_data]['user_table_name']);
						// print_r($user_num);
						$url_user = '../upload_excel/'.$user_num[0].'/'.$user_num[1].'/';
						echo '<tr><td nowrap>'.$data_space[$i_data]['user_table_content'].' </td>
						<td nowrap><a href="excel_table.php?user_table_name='.$data_space[$i_data]['user_table_name'].'">查看处理后数据</a></td>
						<td nowrap><a href="?paichong='.$data_space[$i_data]['user_table_name'].'_log">排重参照对象</a></td>
						<td nowrap><a style="color:red" onclick="if(confirm(\'确定要删除--'.$data_space[$i_data]['user_table_content'].'\')){return true;}else{return false;}" href="delete_room.php?del_user_table_name='.$data_space[$i_data]['user_table_name'].'">删除</a></td>
						<td nowrap> <a href="?file_name='.$url_user.'"> 查看源文件 </a> </td>
						</tr>';
				    }
				?>
			</table>
		</td>
		<td>
			<table border="1"   cellpadding="5">
				<?php
					//读取数据信息 
					if(isset($_GET['file_name'])) {
				        $where1[] = "position LIKE '".$_GET['file_name']."%'";
						$data_space1 = $mysql_excel ->select("upload_file", $where1, "ORDER BY `upload_id` DESC ");
						echo '<tr>
							<th nowrap>原始提交文件名</th>
							<th nowrap>上传后的文件名</th>
							<!--<th nowrap>文件存储路径</th>-->
							<th nowrap>操作人</th>
							<th nowrap>操作时间</th>
							<!--<th nowrap>导入文件</th>-->
							</tr>';
						for ($ii_data=0;$ii_data<count($data_space1);$ii_data++){ 
							// print_r($data_space1[$ii_data]);
							echo '<tr>
							<td>'.$data_space1[$ii_data]['file_name'].' </td>
							<td>'.$data_space1[$ii_data]['upload_file_name'].' <a href="down_file.php?file='.$data_space1[$ii_data]['position'].'">下载</a></td>
							<td>'.$data_space1[$ii_data]['control_person'].' </td>
							<td>'.$data_space1[$ii_data]['control_date'].' </td>
							<!--<td> <a href="excel_import.php?file_name='.$data_space1[$ii_data]['position'].'">导入</a> </td>-->
							</tr>';
					    }
				    }
				?>
			</table>
		</td>
		<td>
			<table border="1"   cellpadding="5">
				<?php
					//读取数据信息 
					if(isset($_GET['paichong'])) {
						$data_space1 = $mysql_excel ->select($_GET['paichong'], "", " ORDER BY ".$_GET['paichong'].".`id` DESC ");
						echo '<tr>
							<th nowrap>排重参照对象</th>
							<th nowrap>操作人</th>
							<th nowrap>操作时间</th>
							</tr>';
						for ($ii_data=0;$ii_data<count($data_space1);$ii_data++){ 
							// print_r($data_space1[$ii_data]);
							echo '<tr>
							<td>'.$data_space1[$ii_data]['log'].' </td>
							<td>'.$data_space1[$ii_data]['log_person'].' </td>
							<td>'.$data_space1[$ii_data]['log_date'].' </td>
							</tr>';
					    }
				    }
				?>
			</table>
		</td>
	</tr>
	<tr>
		<td align="center">存储空间为（第一次提交的表名）</td>
		<td></td>	
	</tr>
</table>
</body>
</html>