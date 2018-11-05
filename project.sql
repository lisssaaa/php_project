-- 创建数据库
CREATE DATABASE IF NOT EXISTS ss29_shop;

-- 选择数据库
USE shop;

-- 创建用户表 user
CREATE TABLE IF NOT EXISTS `user`(
	`id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	`username` VARCHAR(255) NOT NULL UNIQUE,
	`password` CHAR(32) NOT NULL,
	`level`  TINYINT NOT NULL DEFAULT 0, -- 0普通用户 1vip用户 2管理员 3超级管理员
	`status`  TINYINT NOT NULL DEFAULT 0, -- 0 开启 1 禁用
	`addtime`  INT UNSIGNED NOT NULL  
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 添加超级管理员权限
INSERT INTO user(id,username,password,level,status,addtime) VALUES(NULL,'admin',md5('123456'),3,0,1534471281)


-- 创建分类表 type
CREATE TABLE IF NOT EXISTS `type`(
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(255) NOT NULL,
	pid INT NOT NULL,
	path VARCHAR(255) NOT NULL,
	display  TINYINT NOT NULL DEFAULT 0
)ENGINE = InnoDB DEFAULT CHARSET=UTF8;

-- 创建商品表 goods	
CREATE TABLE IF NOT EXISTS `goods`(
	`id`  INT  UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	`name` VARCHAR(255) NOT NULL,
	`typeid` INT NOT NULL,
	`price` DECIMAL(10,2) NOT NULL,
	`store` INT UNSIGNED NOT NULL,
	`status` TINYINT NOT NULL DEFAULT 0,
	`pic`  VARCHAR(255) NOT NULL,
	`sales` INT NOT NULL DEFAULT 0,
	`company` VARCHAR(255) NOT NULL,
	`descr`  VARCHAR(255)
 )ENGINE = InnoDB DEFAULT CHARSET=UTF8;
 
-- 创建用户详情表 user_info
 --男0 女1 保密2
 ---- 0 单身 1已婚 2离异 3 丧偶
CREATE TABLE IF NOT EXISTS `user_info`(
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	uid INT UNSIGNED NOT NULL,
	uname VARCHAR(255) NOT NULL,
	sex TINYINT NOT NULL DEFAULT 0,
	age TINYINT(3) UNSIGNED NOT NULL DEFAULT '1',
	phone CHAR(11) NOT NULL,
	address VARCHAR(255) NOT NULL DEFAULT '陕西西安',
	ismarry TINYINT NOT NULL DEFAULT 0,
	upic VARCHAR(255)
	linkname VARCHAR(255) NOT NULL,		  --新添收货人和邮编
	code CHAR(6) NOT NULL
)ENGINE = InnoDB DEFAULT CHARSET=UTF8;

-- 创建广告表 advert
CREATE TABLE IF NOT EXISTS `advert`(
	`id`  INT  UNSIGNED AUTO_INCREMENT PRIMARY KEY,	
	`name` VARCHAR(255) NOT NULL,	
	`status` TINYINT NOT NULL DEFAULT 0,--0显示 1隐藏
	`pic`  VARCHAR(255) NOT NULL	
 )ENGINE = InnoDB DEFAULT CHARSET=UTF8;
 
 --创建订单表
CREATE TABLE IF NOT EXISTS `orders`(
	`id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, --订单id
	`uid` INT UNSIGNED NOT NULL,                  --会员id
	`linkname` VARCHAR(255) NOT NULL,			  --联系人
	`address` VARCHAR(255) NOT NULL,			  --地址
	`phone` CHAR(11) NOT NULL,					  --电话
	`code` CHAR(6) NOT NULL DEFAULT '000000',	  --邮编	
	`total` DECIMAL(10,2) NOT NULL,				  --总金额
	`status` TINYINT NOT NULL DEFAULT 0  --0新订单 1已发货 2已收货 3评论 4订单完成	
)ENGINE = InnoDB DEFAULT CHARSET=UTF8; 
 
 --创建订单详情表
CREATE TABLE IF NOT EXISTS `order_info`(
	`id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,  --订单详情id
	`oid` INT UNSIGNED NOT NULL,				   --订单id
	`gid` INT UNSIGNED NOT NULL,				   --商品id
	`gname` VARCHAR(255) NOT NULL,				   --商品名称
	`price` DECIMAL(10,2) NOT NULL,				   --商品单价
	`gnum` INT NOT NULL							   --商品数量
)ENGINE = InnoDB DEFAULT CHARSET=UTF8; 












