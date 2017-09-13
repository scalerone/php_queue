<?php
/**
 * Created by PhpStorm.
 * User: wuhui
 * Date: 2017/9/13
 * Time: 11:45
 */
//发布／订阅
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
//建立一个连接信道，声明一个可以发送消息的交换机
$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();
//有几个可供选择的交换机类型：直连交换机（direct）, 主题交换机（topic）, （头交换机）headers和 扇型交换机（fanout）。
//我们在这里主要说明最后一个 —— 扇型交换机（fanout）。先创建一个fanout类型的交换机，命名为logs，它把消息发送给它所知道的所有队列
$channel->exchange_declare('logs', 'fanout', false, false, false);
//接收传来的数据
$data = implode(' ', array_slice($argv, 1));
if(empty($data)) $data = "info: Hello World!";

$msg = new AMQPMessage($data);
$channel->basic_publish($msg, 'logs');
echo " [x] Sent ", $data, "\n";

$channel->close();
$connection->close();

