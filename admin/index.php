<?php
	//开启session
	session_start();
	//设置时区
	date_default_timezone_set('PRC');
	//设置错误级别
	error_reporting(E_ALL ^ E_NOTICE);
	//引入配置文件
	include '../public/Config/config.php';
	//引入Model类
	// include './Model/Model.class.php';
	// include './Controller/UserController.class.php';
	// include './Controller/GoodsController.class.php';
	// include './Controller/IndexController.class.php';

	//自动加载类
	function __autoload($className){
		//echo $className;exit;
		//判断是否是controller文件夹里面的类
		if(substr($className,-10)=='Controller'){
			include './Controller/'.$className.'.class.php';
		//判断是否是Model文件夹里面的类	
		}elseif (substr($className,-5)=='Model') {
			include './Model/'.$className.'.class.php';
		//既不是model也不是controller里面的类就加载org文件夹中的内容
		}else{
			include './Org/'.$className.'.class.php';
		}
	}

	//500行引入类

	//c 代表我们要操作的某个类
	$c = empty($_GET['c'])?'Index':$_GET['c'];
	//我们传递过来的内容全部变成小写之后再让首字母大写
	
	$c = ucfirst(strtolower($c));
	//echo $c;exit;

	$controller = $c.'Controller';
	$info = new $controller;

	//a代表我们要操作的某个类的某个方法
	$a = empty($_GET['a'])?'Index':$_GET['a'];
	//var_dump($a);exit;

	$info->$a();




	