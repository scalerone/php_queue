<?php
/**
 * Created by PhpStorm.
 * User: wuhui
 * Date: 2017/9/13
 * Time: 15:16
 */
//发布／订阅
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
//建立一个连接信道，声明一个可以接收消息的交换机
$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();
$channel->exchange_declare('logs', 'fanout', false, false, false);
//创建临时队列
//在 php-amqplib 客户端，当我们提供队列名称为空字符串时，我们创建了一个具有生成名称的非持久队列：
list($queue_name, ,) = $channel->queue_declare("", false, false, true, false);
//把临时队列绑定到交换机上
$channel->queue_bind($queue_name, 'logs');
echo ' [*] Waiting for logs. To exit press CTRL+C', "\n";
//定义一个PHP回调将接收服务器发送的消息并进行处理
$callback = function($msg){
    echo ' [x] ', $msg->body, "\n";
};
//信道队列在接收消息的时候调用$callback函数进行消费消息
$channel->basic_consume($queue_name, '', false, true, false, false, $callback);
while(count($channel->callbacks)) {
    $channel->wait();
}
$channel->close();
$connection->close();