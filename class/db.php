<?php
// 数据库连接类
class DB{
  //私有的属性
  private static $dbcon=false;
  private $host;
  private $port;
  private $user;
  private $pass;
  private $db;
  private $charset;
  private $link;
  //私有的构造方法
  private function __construct(){
    $this->host =  'localhost';
    $this->port =  '3306';
    $this->user =  'root';
    $this->pass =  'root';
    $this->db =  'imooc';
    $this->charset= 'utf8';
    //连接数据库
    $this->db_connect();
    //选择数据库
    $this->db_usedb();
    //设置字符集
    $this->db_charset();
   }
   //连接数据库
   private function db_connect(){
    $this->link=mysqli_connect($this->host.':'.$this->port,$this->user,$this->pass);
    if(!$this->link){
      echo "数据库连接失败<br>";
      echo "错误编码".mysqli_errno($this->link)."<br>";
      echo "错误信息".mysqli_error($this->link)."<br>";
      exit;
    }
   }
   //设置字符集
    private function db_charset(){
     mysqli_query($this->link,"set names {$this->charset}");
    }
    //选择数据库
   private function db_usedb(){
     mysqli_query($this->link,"use {$this->db}");
   }
   //私有的克隆
   private function __clone(){
     die('clone is not allowed');
   }
   //公用的静态方法
   public static function getIntance(){
     if(self::$dbcon==false){
      self::$dbcon=new self;
     }
     return self::$dbcon;
   }
   //执行sql语句的方法
    public function query($sql){
     $res=mysqli_query($this->link,$sql);
     if(!$res){
      echo "sql语句执行失败<br>";
      echo "错误编码是".mysqli_errno($this->link)."<br>";
      echo "错误信息是".mysqli_error($this->link)."<br>";
     }
     return $res;
   }
    //获得最后一条记录id
    public function getInsertid(){
     return mysqli_insert_id($this->link);
    }
   /**
    * 查询某个字段
    * @param
    * @return string or int
    */
    public function getOne($sql){
     $query=$this->query($sql);
      return mysqli_free_result($query);
    }
    //获取一行记录,return array 一维数组
    public function getRow($sql,$type="assoc"){
     $query=$this->query($sql);
     if(!in_array($type,array("assoc",'array',"row"))){
       die("mysqli_query error");
     }
     $funcname="mysqli_fetch_".$type;
     return $funcname($query);
    }
    //获取一条记录,前置条件通过资源获取一条记录
    public function getFormSource($query,$type="assoc"){
    if(!in_array($type,array("assoc","array","row")))
    {
      die("mysqli_query error");
    }
    $funcname="mysqli_fetch_".$type;
    return $funcname($query);
    }
    //获取多条数据，二维数组
    public function getAll($sql){
     $query=$this->query($sql);
     $list=array();
     while ($r=$this->getFormSource($query)) {
      $list[]=$r;
     }
     return $list;
    }

    public function selectAll($table,$where,$fields='*',$order='',$skip=0,$limit=1000)
    {
              if(is_array($where)){
                    foreach ($where as $key => $val) {
                        if (is_numeric($val)) {
                            $condition = $key.'='.$val;
                        }else{
                            $condition = $key.'=\"'.$val.'\"';
                        }
                    }
              } else {
                $condition = $where;
              }
              if (!empty($order)) {
                  $order = " order by ".$order;
              }
              $sql = "select $fields from $table where $condition $order limit $skip,$limit";
              $query = $this->query($sql);
              $list = array();
              while ($r= $this->getFormSource($query)) {
                  $list[] = $r;
              }
              return $list;
    }
     /**
     * 定义添加数据的方法
     * @param string $table 表名
     * @param string orarray $data [数据]
     * @return int 最新添加的id
     */
     public function insert($table,$data){
     //遍历数组，得到每一个字段和字段的值
     $key_str='';
     $v_str='';
     foreach($data as $key=>$v){
     //  if(empty($v)){
     //   die("error");
     // }
        //$key的值是每一个字段s一个字段所对应的值
        $key_str.=$key.',';
        $v_str.="'$v',";
     }
     $key_str=trim($key_str,',');
     $v_str=trim($v_str,',');
     //判断数据是否为空
     $sql="insert into $table ($key_str) values ($v_str)";
     $this->query($sql);
    //返回上一次增加操做产生ID值
     return $this->getInsertid();
   }
   /*
    * 删除一条数据方法
    * @param1 $table, $where=array('id'=>'1') 表名 条件
    * @return 受影响的行数
    */
    public function deleteOne($table, $where){
      if(is_array($where)){
        foreach ($where as $key => $val) {
          $condition = $key.'='.$val;
        }
      } else {
        $condition = $where;
      }
      $sql = "delete from $table where $condition";
      $this->query($sql);
      //返回受影响的行数
      return mysqli_affected_rows($this->link);
    }
    /*
    * 删除多条数据方法
    * @param1 $table, $where 表名 条件
    * @return 受影响的行数
    */
    public function deleteAll($table, $where){
      if(is_array($where)){
        foreach ($where as $key => $val) {
          if(is_array($val)){
            $condition = $key.' in ('.implode(',', $val) .')';
          } else {
            $condition = $key. '=' .$val;
          }
        }
      } else {
        $condition = $where;
      }
      $sql = "delete from $table where $condition";
      $this->query($sql);
      //返回受影响的行数
      return mysqli_affected_rows($this->link);
    }
   /**
    * [修改操作description]
    * @param [type] $table [表名]
    * @param [type] $data [数据]
    * @param [type] $where [条件]
    * @return [type]
    */
   public function update($table,$data,$where,$limit=0){
     //遍历数组，得到每一个字段和字段的值
     $str='';
    foreach($data as $key=>$v){
     $str.="$key='$v',";
    }
    $str=rtrim($str,',');
      if(is_array($where)){
        foreach ($where as $key => $val) {
          if(is_array($val)){
            $condition = $key.' in ('.implode(',', $val) .')';
          } else {
            $condition = $key. '=' .$val;
          }
        }
      } else {
        $condition = $where;
      }

        if (!empty($limit)) {
            $limit = " limit ".$limit;
        }else{
            $limit='';
        }
    //修改SQL语句
    $sql="update $table set $str where $condition $limit";
    $this->query($sql);
    //返回受影响的行数
    return mysqli_affected_rows($this->link);
   }
}
?>
