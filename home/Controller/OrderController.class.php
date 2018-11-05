<?php

	class OrderController{
		//用户列表
		public function index()
		{
			//var_dump($_SESSION);exit;
			try {		 		
		 		$dsn = 'mysql:host='.HOST.';dbname='.DB.';charset=utf8';		 		
		 		$pdo =new PDO($dsn,'root','zhulisha');		 		
		 		$pdo->setAttribute(3,1);		 		
		 		
		 		$sql = "SELECT * FROM orders WHERE `uid`=:uid";
		 	 	$stmt = $pdo->prepare($sql);
		 	 	$stmt->bindparam(':uid',$_SESSION['home']['id']);
		 	 			 	 	
		 		$stmt->execute();
		 	 	if($stmt->rowCount()){
		 		 	$orders = $stmt->fetchAll(2);	 	 		
		 	 		//var_dump($orders);exit;
		 	 	}
		 	 } catch (PDOException $e) {
		 			echo $e->getMessage();
			 }

			include './View/Order/showOrder.html';
		}
		//展示付款页面
		public function pay($id)
		{
			//var_dump($id);exit;
			try {		 		
		 		$dsn = 'mysql:host='.HOST.';dbname='.DB.';charset=utf8';		 		
		 		$pdo =new PDO($dsn,'root','zhulisha');		 		
		 		$pdo->setAttribute(3,1);		 		
		 		
		 		$sql = "SELECT * FROM orders WHERE `id`=:id";
		 	 	$stmt = $pdo->prepare($sql);
		 	 	$stmt->bindparam(':id',$id);
		 	 			 	 	
		 		$stmt->execute();
		 	 	if($stmt->rowCount()){
		 		 	$order = $stmt->fetchAll(2);	 	 		
		 	 		//var_dump($orders);exit;
		 	 	}
		 	 } catch (PDOException $e) {
		 			echo $e->getMessage();
			 }
			return $order;
		}

		//用户添加
		public function addOrder()
		{
			if(empty($_SESSION['cart']))
			{
				echo'<script>alert("购物车为空！");location="./index.php?c=cart&a=index"</script>';	
			}else{
				//var_dump($_GET);
				$info = $this->get_userinfo();
				//var_dump($info);exit;
				if(empty($info))
		 		 	{
		 		 		echo'<script>alert("请完善用户信息再下单！");location="./index.php?c=index&a=userinfo"</script>';exit;
		 		 	}	
				$this->to_orders($info,$_GET['total']);	
				//提交订单后清空购物车
				unset($_SESSION['cart']);		
			}
			
		}
		//获取user_info表信息
		public function get_userinfo()
		{
			try {		 	
		 		$dsn = 'mysql:host='.HOST.';dbname='.DB.';charset=utf8';		 		
		 		$pdo =new PDO($dsn,'root','zhulisha');		 		
		 		$pdo->setAttribute(3,1);		 		
		 		$sql = "SELECT * FROM user_info WHERE uid=:uid";		 		
		 	 	$stmt = $pdo->prepare($sql);
		 	 	$stmt->bindparam(':uid',$_SESSION['home']['id']);	 	
		 	 	$stmt->execute();
		 	 	if($stmt->rowCount()){
		 		 	$user_info = $stmt->fetchAll(2);
		 		 	$info = $user_info[0];	
		 		 	//var_dump($info);exit;			 		 	 		 	
		 		 } 			 				 		
			 } catch (PDOException $e) {
		 			echo $e->getMessage();
			 }
			 return $info;
		}
		

		//添加数据到orders表
		public function to_orders($info,$total)
		{			
			$data = array(':uid'=>$_SESSION['home']['id'],':linkname'=>$info['linkname'],':address'=>$info['address'],':phone'=>$info['phone'],':code'=>$info['code'],':total'=>$total,':status'=>0);
			//var_dump($data);exit;
			try {		 		
		 		$dsn = 'mysql:host='.HOST.';dbname='.DB.';charset=utf8';		 		
		 		$pdo =new PDO($dsn,'root','zhulisha');		 		
		 		$pdo->setAttribute(3,1);		 		
		 		
		 		$sql = "INSERT INTO orders(`id`,`uid`,`linkname`,`address`,`phone`,`code`,`total`,`status`) VALUES(NULL,:uid,:linkname,:address,:phone,:code,:total,:status)";
		 	 	$stmt = $pdo->prepare($sql);	
		 	 	$bool = $stmt->execute($data);	
		 	 	//var_dump($bool);exit;
		 		if($bool){
		 			$oid = $pdo->lastInsertId();
		 			$this->to_orderinfo($oid);//添加数据到订单详情
		 			//var_dump($oid);exit;
		 		 	//header('location:index.php?c=order&a=index');
		 	 	}
		 	 } catch (PDOException $e) {
		 			echo $e->getMessage();
			 }
		}
		//添加数据到order_info表
		public function to_orderinfo($oid)
		{
			//传入订单id作为详情表的oid字段
			try {		
			 	//var_dump($order);//exit;		 			 		 	
		 		//var_dump($_SESSION);exit;

		 		//遍历session中的每一个商品信息插入到order_info表中
		 		foreach ($_SESSION['cart'] as $value) {
		 		 	$data = array(':oid'=>$oid,':gid'=>$value['id'],':gname'=>$value['name'],':price'=>$value['price'],':gnum'=>$value['num']);
					//var_dump($data);exit;
					//添加数据到订单详情表 		
		 			$dsn = 'mysql:host='.HOST.';dbname='.DB.';charset=utf8';		 		
		 			$pdo =new PDO($dsn,'root','zhulisha');		 		
		 			$pdo->setAttribute(3,1);	 				
		 			$sql = "INSERT INTO order_info(`id`,`oid`,`gid`,`gname`,`price`,`gnum`) VALUES(NULL,:oid,:gid,:gname,:price,:gnum)";
		 	 		$stmt = $pdo->prepare($sql);			 	 				
		 	 		//var_dump($bool);exit;
		 			if($stmt->execute($data)){		 					
		 		 		$order = $this->pay($oid);
		 		 		include './View/pay.html';
		 	 		}
		 		}	 				 				 		
		    } catch (PDOException $e) {
		 			echo $e->getMessage();
			 }			 
		}
		
		//订单详情
		public function order_info()
		{		
			//根据订单表的id查询订单详情表的oid
			try {		 	
		 		$dsn = 'mysql:host='.HOST.';dbname='.DB.';charset=utf8';		 		
		 		$pdo =new PDO($dsn,'root','zhulisha');		 		
		 		$pdo->setAttribute(3,1);		 		
		 		$sql = "SELECT * FROM order_info WHERE oid=:oid";		 		
		 	 	$stmt = $pdo->prepare($sql);
		 	 	$stmt->bindparam(':oid',$_GET['oid']);	 	
		 	 	$stmt->execute();
		 	 	if($stmt->rowCount()){
		 		 	$info = $stmt->fetchAll(2);	 	
		 		 	//var_dump($info);//exit;		 		
		 		 	foreach ($info as $value) {
		 		 				 	
		 				$dsn = 'mysql:host='.HOST.';dbname='.DB.';charset=utf8';		 		
		 				$pdo =new PDO($dsn,'root','zhulisha');		 		
		 				$pdo->setAttribute(3,1);		 		
		 				$sql = "SELECT `pic` FROM goods WHERE id=:gid";		 		
		 	 			$stmt = $pdo->prepare($sql);
		 	 			$stmt->bindparam(':gid',$value['gid']);	 	
		 	 			$stmt->execute();
		 	 			if($stmt->rowCount()){
		 		 			$good = $stmt->fetchAll(2);	 
		 		 			$pic = $good[0]['pic'];	
		 		 			//var_dump($pic);exit;		 		 			 		 	
		 				}
				 	
		 		 	 	$list = array();
		 		 	 	$list = $value;
		 		 	 	$list['pic'] = $pic;
		 		 	 	$order_info[] = $list;
		 		 	 	//var_dump($list);		 		 	 	 		 	 	
		 		 	} 	
		 		 	//var_dump($order_info);//exit;	
		 		 include './View/Order/showOrderinfo.html';	 		 	
		 		} 			 				 		
			 } catch (PDOException $e) {
		 		echo $e->getMessage();
			 }			
		}

	}