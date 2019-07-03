<?php
header("Content-type:text/html;charset=utf-8");//utf-8
class Pdodb
{
    protected $pdo;
    protected $res;
    protected $config;
     /*构造函数*/
    function __construct($config){
        $this->Config = $config;
        $this->connect();
    }
    /*数据库连接*/
    public function connect(){
        try {            
            $this->pdo= new PDO($this->Config['dsn'], $this->Config['username'], $this->Config['password']);
            //$dbh = new PDO('mysql:host=localhost;dbname=test', $user, $pass);          
            $this->pdo->query("set names utf8");          
//             echo '数据库链接成功！ ';
        }catch(Exception $e){
            echo '数据库连接失败,详情: ' . $e->getMessage () . ' 请在配置文件中数据库连接信息';
            exit ();            
        }
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);//属性名 属性值 数组以关联数组返回
    }
    /*数据库关闭*/  
    public function close(){       
        $this->pdo = null; 
        echo "执行完毕！";
    }
    //用于有记录结果返回的操作
    public function query($sql){
        $this->res = $this->pdo->query($sql);       
    }
    //添加数据库列
    public function add_column($table, $cell, $comment){
        $this->query("ALTER TABLE  $table ADD  `".$cell."` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT  '".$comment."'");
    }
    public function fetchAll(){
        return $this->res->fetchAll();        
    }
    public function fetchColumn(){
        return $this->res->fetchColumn();       
    }
    //显示数据
    //$sqlwhere 此可为数组
    public function select($table, $sqlwhere="", $orderby="", $fields="*", $mode=0){
        if(is_array($sqlwhere)){
            $sqlwhere = implode(' and ', $sqlwhere);
            $sqlwhere = preg_replace('/(\w+)/', '${1}', $sqlwhere);
            $sqlwhere = ' and '.$sqlwhere;     
        }
        if($mode==1) {//统计行数
            $this->query("select count(*) from $table where 1 $sqlwhere");
            $return_result = $this->fetchColumn();
        } else {
            // echo "select $fields from $table where 1 $sqlwhere $orderby";
            $this->query("select $fields from $table where 1 $sqlwhere $orderby");
            $return_result = $this->fetchAll();
        }
        return $return_result;
    }
    //显示数据2  , 用在复杂语句上
    public function query_sql($sql){
        $this->query($sql);
        $content = $this->fetchAll();
        return  $content;
    }   
    //数据表，表头
    public function table_columns($table, $Field ='') {
        $this->query("SHOW COLUMNS FROM  `".$table."` WHERE Field != '".$Field."'");
        $columns = $this->fetchAll();         
        return  $columns;
    }
    //防止sql注入
    public function sql_save($value) {
        $value = addslashes($value);
        $return_val = preg_match('/select|insert|and|create|update|delete|script|alter|=|\(|count|#|\'|\/\*|\*|\.\.\/|\.\/|join|like|union|into|load_file|outfile/i', $value); // 进行过滤
        if($return_val) {
            exit('提交的参数非法！');
        } else {
            return $return_val;
        }
    }
    //插入数据
    //$filed, $value 均为数组
    public function insert($table, $filed){
//         if(is_array($filed)) {            
//             $table = implode(', ', $table);
            
//         }
//         $this->query($sql);
//         $content = $this->fetchAll();
//         return  $content;
//         echo $table;  INSERT INTO `upload_file` VALUES (NULL, 'file_name', NULL, NULL, NULL, NULL);
        $str_sql = "INSERT INTO `".$table."` (";
        $str_field = implode(", ", array_keys($filed));
        $str_sql .= preg_replace('/(\w+)/', '`${1}`', $str_field);
        $str_sql .= ")";
        $str_sql .= " VALUES (\"";
        $str_sql .= preg_replace('/(\w+)/', '${1}', implode("\", \"", $filed));
        // $str_sql .= implode(", ", $filed);
        $str_sql .= "\" )";
        // echo $str_sql;
        $this->query($str_sql);
//         return "";
    }
}
//测试可以打开
// $mysql_config=array(
//     'dsn'=>'mysql:dbname=test;host=localhost',//数据库服务器地址    
//     'username'=>'root',
//     'password'=>'123',   
// );
// $mysql_excel = new Pdodb($mysql_config);
// $mysql_excel ->add_column("pinyin", "拼音");
