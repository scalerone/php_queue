<?php
/**
 * Created by PhpStorm.
 * User: wuhui
 * Date: 2017/9/11
 * Time: 14:18
 */
//////////对应send.php//////////
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
//打开一个连接和一个通道，然后声明我们将要消耗的队列
$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();
$channel->queue_declare('hello', false, false, false, false);

echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";


//定义一个PHP回调将接收服务器发送的消息
$callback = function($msg) {
    echo " [x] Received ", $msg->body, "\n";
};

//在接收消息的时候调用$callback函数进行消费消息
$channel->basic_consume('hello', '', false, true, false, false, $callback);
//代码块循环 通道（$channel ）的回调。无论什么时候我们收到消息我们的回调函数（$callback）将传递给接收的消息
while(count($channel->callbacks)) {
    $channel->wait();
}