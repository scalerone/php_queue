<?php
/**
 * Created by PhpStorm.
 * User: wuhui
 * Date: 2017/9/11
 * Time: 15:48
 */
//工作队列--消费者
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
//打开一个连接和一个通道，然后声明我们将要消耗的队列
$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();
//为了不让队列消失，需要把队列声明为持久化（durable=true）
$channel->queue_declare('task_queue', false, true, false, false);
echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";
//定义一个PHP回调将接收服务器发送的消息并进行处理
$callback = function($msg){
    echo " [x] Received ", $msg->body, "\n";
    sleep(substr_count($msg->body, '.'));//简单处理：一个点（.）将会耗时1秒钟
    echo " [x] Done", "\n";
    //为了防止消息丢失，RabbitMQ提供了消息响应（acknowledgments）。消费者会通过一个ack（响应），
    //告诉RabbitMQ已经收到并处理了某条消息，然后RabbitMQ就会释放并删除这条消息。
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};
//只有消费者consumer已经处理并确认了上一条message时queue才分派新的message给它
//使用basic.qos方法，并设置prefetch_count=1,告诉RabbitMQ，再同一时刻，不要发送超过1条消息给一个工作者（worker），
//直到它已经处理了上一条消息并且作出了响应。
//这样，RabbitMQ就会把消息分发给下一个空闲的工作者（worker）。
$channel->basic_qos(null, 1, null);
//在接收消息的时候调用$callback函数进行消费消息
$channel->basic_consume('task_queue', '', false, false, false, false, $callback);
//循环等待处理消息
while(count($channel->callbacks)) {
    $channel->wait();
}
$channel->close();
$connection->close();