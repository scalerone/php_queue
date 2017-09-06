<?php
/**
 * Created by PhpStorm.
 * User: wuhui
 * Date: 2017/9/6
 * Time: 10:41
 */
//处理队列表中的订单并进行标记

include '../class/db.php';
$db = DB::getIntance();
//1:先把要处理的订单记录更新为等待处理
$waiting = array('status'=>0);
$lock = array('status'=>2);
$res_lock = $db->update('order_queue',$lock,$waiting,2);
//这里也可以使用文件锁------http://www.phpxs.com/post/5564/--使用非阻塞的文件排他锁


//2：选择刚刚锁定的这些数据，然后进行配送系统的处理
if($res_lock){
    $res = $db->selectAll('order_queue',$lock);
    //选择要处理的订单的内容


    //然后由配货系统进行退货处理，
    //......

//3:处理完成后把订单更新为已处理，
    $success = array(
        'status'=>1,
        'updated_at'=>date('Y-m-d H:i:s',time()),
        );
    $res_last = $db->update('order_queue',$success);
    if($res_last){
        echo "Success:".$res_last;
    }else{
        echo "Fail:".$res_last;
    }


}else{
    echo "All Finished";
}