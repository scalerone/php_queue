CREATE TABLE `order_queue` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单的id号',
  `order_id` int(11) NOT NULL,
  `mobile` varchar(20) NOT NULL COMMENT '用户的手机号',
  `address` varchar(100) NOT NULL COMMENT '用户的地址',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '订单创建的时间',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '处理完成的时间',
  `status` tinyint(2) NOT NULL COMMENT '当前状态，0 未处理，1 已处理，2处理中',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
