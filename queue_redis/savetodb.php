<?php

include '../class/db.php';
//1.加载redis组件
$redis = new Redis();
$redis->connect('127.0.0.1',6379);
$redis_name = "miaosha";
$db = DB::getIntance();

//死循环，
while(1){
    //从队列最左侧取出一个值来，
    $user = $redis->lPop($redis_name);
    //然后判断这个值是否存在
    if(!$user||$user=='nil'){
        sleep(2);
        continue;
    }

    //切割出时间和uid
    $user_arr = explode('%',$user);
    $insert_data = array(
        'uid'=> $user_arr[0],
        'time_stamp'=> $user_arr[1],
    );
    //保存到数据库中
    $res =$db->insert('redis_queue',$insert_data);
    // 数据库插入失败时的回滚机制，
    if(!$res){
        $redis->rPush($redis_name,$user);
    }
    sleep(2);
}
//释放一下redis
$redis->close();









