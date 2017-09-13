# 参考学习
- [windows下 安装 rabbitMQ 及操作常用命令](http://www.cnblogs.com/ericli-ericli/p/5902270.html)
- [windows下的php rabbit mq安装、配置](http://blog.csdn.net/u014071426/article/details/48549835)
- [RabbitMQ 中文文档－PHP版](https://rabbitmq.shujuwajue.com/)

#注意事项
> 发送不成功！
  如果这是你第一次使用RabbitMQ，并且没有看到“Sent”消息出现在屏幕上，你可能会抓耳挠腮不知所以。  
  这也许是因为没有足够的磁盘空间给代理使用所造成的（代理默认需要1Gb的空闲空间），  
  所以它才会拒绝接收消息。查看一下代理的日志确定并且减少必要的限制。   
  [配置文件文档](http://www.rabbitmq.com/configure.html#config-items)会告诉你如何更改磁盘空间限制（disk_free_limit）。

> 如果这是你第二次使用RabbitMQ，使用重启命令重启RabbitMQ服务
在windows下以管理员权限运行cmd使用  

```net stop RabbitMQ && net start RabbitMQ```

