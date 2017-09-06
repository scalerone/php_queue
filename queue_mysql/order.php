<?php
//接受用户订单并写入队列

include '../class/db.php';

if(!empty($_GET['mobile'])){
	//订单中心处理流程
	//
	//数据过滤
	//....
	$order_id = rand(10000,99999);
	//生成订单数据
	$insert_data = array(
		'order_id' => $order_id,
		'mobile' => $_GET['mobile'],
		'created_at' => date('Y-m-d H:i:s',time()),
        'status' =>0,
		);
    //存入队列表中
    $db = DB::getIntance();
    $res = $db->insert('order_queue',$insert_data);
    if($res){
        echo $insert_data['order_id']."保存成功";
    }else{
        echo "保存失败";
    }



}


?>