<?php
//Redis中的watch来实现秒杀功能


$redis = new redis();
$result = $redis->connect('127.0.0.1', 6379);
echo $mywatchkey = $redis->get("mywatchkey");

/*
  //插入抢购数据
 if($mywatchkey>0)
 {
     $redis->watch("mywatchkey");
  //启动一个新的事务。
    $redis->multi();
   $redis->set("mywatchkey",$mywatchkey-1);
   $result = $redis->exec();
   if($result) {
      $redis->hSet("watchkeylist","user_".mt_rand(1,99999),time());
      $watchkeylist = $redis->hGetAll("watchkeylist");
        echo "抢购成功！<br/>";
        $re = $mywatchkey - 1;
        echo "剩余数量：".$re."<br/>";
        echo "用户列表：<pre>";
        print_r($watchkeylist);
   }else{
      echo "手气不好，再抢购！";exit;
   }
 }else{
     // $redis->hSet("watchkeylist","user_".mt_rand(1,99999),"12");
     //  $watchkeylist = $redis->hGetAll("watchkeylist");
        echo "fail！<br/>";
        echo ".no result<br/>";
        echo "用户列表：<pre>";
      //  var_dump($watchkeylist);
 }*/


$rob_total = 100;   //抢购数量
if($mywatchkey<=$rob_total){
    $redis->watch("mywatchkey");
    $redis->multi(); //在当前连接上启动一个新的事务。
    //插入抢购数据
    $redis->set("mywatchkey",$mywatchkey+1);
    $rob_result = $redis->exec();
    if($rob_result){
        $redis->hSet("watchkeylist","user_".mt_rand(1, 9999),$mywatchkey);
        $mywatchlist = $redis->hGetAll("watchkeylist");
        echo "抢购成功！<br/>";

        echo "剩余数量：".($rob_total-$mywatchkey-1)."<br/>";
        echo "用户列表：<pre>";
        var_dump($mywatchlist);
    }else{
        $redis->hSet("watchkeylist","user_".mt_rand(1, 9999),'meiqiangdao');
        echo "手气不好，再抢购！";exit;
    }
}