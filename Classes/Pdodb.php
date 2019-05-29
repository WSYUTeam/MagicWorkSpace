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
            echo '数据库链接成功！ ';
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
        $this->pdo->query($sql);       
    }
    //添加数据库列
    public function add_column($cell, $comment){
        $this->query("ALTER TABLE  `excel_sheet` ADD  `".$cell."` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT  '".$comment."'");
    }
    //
//     public function update($sql){
//         $this->query($sql);
//     } 
}

// $mysql_config=array(
//     'dsn'=>'mysql:dbname=test;host=localhost',//数据库服务器地址    
//     'username'=>'root',
//     'password'=>'123',   
// );
// $mysql_excel = new Pdodb($mysql_config);
// $mysql_excel ->add_column("pinyin", "拼音");
