<?php
/**
 * Created by PhpStorm.
 * User: wuhui
 * Date: 2017/9/11
 * Time: 15:33
 */
//工作队列-生产者

//////////对应worker.php//////////
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
//建立一个连接信道，声明一个可以发送消息的队列task_queue
$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();
//声明队列时为了不让队列消失，需要把队列声明为持久化（durable=true）
$channel->queue_declare('task_queue', false, true, false, false);
//cmd运行：php new_task.php "A very hard task which takes two seconds.."
//接收传来的数据："A very hard task which takes two seconds.."
$data = implode(' ', array_slice($argv, 1));
if(empty($data)) $data = "Hello World!";
//定义消息
$msg = new AMQPMessage($data,
    array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT) //使消息持久化
);
//消息入队发布消息
$channel->basic_publish($msg, '', 'task_queue');
echo " [x] Sent ", $data, "\n";
$channel->close();
$connection->close();
