<?php

	//用户管理模块操作类
	
	class OrdersController{
		//用户列表
		public function index(){
			try {		 		
		 		$dsn = 'mysql:host='.HOST.';dbname='.DB.';charset=utf8';		 		
		 		$pdo =new PDO($dsn,'root','zhulisha');		 		
		 		$pdo->setAttribute(3,1);		 		
		 		//判断是否点击查看订单详情
		 		if(empty($_GET['oid'])){
		 			$sql = "SELECT * FROM orders";
		 	 		$stmt = $pdo->prepare($sql);
		 	 		$stmt->execute();
		 	 		if($stmt->rowCount()){
		 		 		$orders = $stmt->fetchAll(2);
		 	 			include './View/Orders/index.html';
		 	 		}
		 		}else{
		 			//根据订单表的id查询订单详情表的oid
							 	
				 		$dsn = 'mysql:host='.HOST.';dbname='.DB.';charset=utf8';		 		
				 		$pdo =new PDO($dsn,'root','zhulisha');		 		
				 		$pdo->setAttribute(3,1);		 		
				 		$sql = "SELECT * FROM order_info WHERE oid=:oid";		 		
				 	 	$stmt = $pdo->prepare($sql);
				 	 	$stmt->bindparam(':oid',$_GET['oid']);	 	
				 	 	$stmt->execute();
				 	 	if($stmt->rowCount()){
				 		 	$info = $stmt->fetchAll(2);	 	
				 		 		 		
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
				 		 	 	$info_list[] = $list;		 	 			 	 	 		 	 	
				 		 	} 
				 		}
				 		//var_dump($info_list);
				 		include './View/Orders/info.html';	 
				 	}			 	 		
			} catch (PDOException $e) {
		 			echo $e->getMessage();
			}	
			
		
	}
		

		//删除订单操作方法
		public function del(){
			//var_dump($_GET);exit;
			try {		 		
		 		$dsn = 'mysql:host='.HOST.';dbname='.DB.';charset=utf8';		 		
		 		$pdo =new PDO($dsn,'root','zhulisha');		 		
		 		$pdo->setAttribute(3,1);
				$sql = "DELETE FROM orders WHERE id=:id";
		 	 	$stmt = $pdo->prepare($sql);
		 	 	$stmt->bindparam(':id',$_GET['id']);	 
		 	 	$stmt->execute();
		 	 	if($stmt->rowCount()){
		 		 	//删除对应订单详情信息
		 		 	$dsn = 'mysql:host='.HOST.';dbname='.DB.';charset=utf8';		 		
			 		$pdo =new PDO($dsn,'root','zhulisha');		 		
			 		$pdo->setAttribute(3,1);
					$sql = "DELETE FROM order_info WHERE `oid`=:id";
			 	 	$stmt = $pdo->prepare($sql);
			 	 	$stmt->bindparam(':id',$_GET['id']);	 
			 	 	$stmt->execute();
			 	 	if($stmt->rowCount()){
			 		 	header('location:./index.php?c=orders&a=index');			 	 		
			 	 	}		 	 		
		 	 	}
			} catch (PDOException $e) {
		 		echo $e->getMessage();
			}
			
		}

		

		//修改订单信息
		public function edit(){
			try {		 		
		 		$dsn = 'mysql:host='.HOST.';dbname='.DB.';charset=utf8';		 		
		 		$pdo =new PDO($dsn,'root','zhulisha');		 		
		 		$pdo->setAttribute(3,1);
				$sql = "SELECT * FROM orders WHERE id=:id";
		 	 	$stmt = $pdo->prepare($sql);
		 	 	$stmt->bindparam(':id',$_GET['id']);	 
		 	 	$stmt->execute();
		 	 	if($stmt->rowCount()){
		 		 	$order = $stmt->fetchAll(2);
		 	 		
		 	 	}
			} catch (PDOException $e) {
		 		echo $e->getMessage();
			}
			//var_dump($order);
			include './View/Orders/edit.html';
		}
		//处理修改页面
		public function doedit(){
			try {		
				$data = array(':total'=>$_POST['total'],':linkname'=>$_POST['linkname'],':address'=>$_POST['address'],':phone'=>$_POST['phone'],':code'=>$_POST['code'],':status'=>$_POST['status'],':id'=>$_POST['id']); 		
		 		$dsn = 'mysql:host='.HOST.';dbname='.DB.';charset=utf8';		 		
		 		$pdo =new PDO($dsn,'root','zhulisha');		 		
		 		$pdo->setAttribute(3,1);
				$sql = "UPDATE orders SET `total`=:total,`linkname`=:linkname,`address`=:address,`phone`=:phone,`code`=:code,`status`=:status WHERE id=:id";
		 	 	$stmt = $pdo->prepare($sql); 	 	
		 	 			 	 		 
		 	 	$stmt->execute($data);
		 	 	if($stmt->rowCount()){
		 		 	
		 	 		echo '<script>alert("修改成功");location="./index.php?c=orders&a=index"</script>';
		 	 	}
		 	 	else{
					echo '<script>alert("修改失败");location="./index.php?c=orders&a=index"</script>';

				}
			} catch (PDOException $e) {
		 		echo $e->getMessage();
			}
		}

		//删除订单信息操作方法
		public function info_del(){
			//var_dump($_GET);exit;
			try {		 		
		 		$dsn = 'mysql:host='.HOST.';dbname='.DB.';charset=utf8';		 		
		 		$pdo =new PDO($dsn,'root','zhulisha');		 		
		 		$pdo->setAttribute(3,1);
				$sql = "DELETE FROM order_info WHERE id=:id";
		 	 	$stmt = $pdo->prepare($sql);
		 	 	$stmt->bindparam(':id',$_GET['id']);	 
		 	 	$stmt->execute();
		 	 	if($stmt->rowCount()){		 		 	
			 		 header('location:./index.php?c=orders&a=index');			 		 	 		
		 	 	}
			} catch (PDOException $e) {
		 		echo $e->getMessage();
			}
			
		}
		//修改订单详情信息
		public function info_edit(){
			try {
			//var_dump($_GET);	
			//var_dump($_POST);//exit;

			//$data = array(`:gnum`=>$_POST['gnum'],`:id`=>$_POST['id']);	 		
		 	$dsn = 'mysql:host='.HOST.';dbname='.DB.';charset=utf8';		 		
		 	$pdo =new PDO($dsn,'root','zhulisha');		 		
		 	$pdo->setAttribute(3,1);
		 	$sql = "UPDATE order_info SET `gnum`=:gnum WHERE id=:id";
			//$sql = "UPDATE order_info SET `gnum`=:gnum WHERE id=:id";
		 	$stmt = $pdo->prepare($sql);
		 	$stmt->bindparam(':gnum',$_POST['gnum']);
		 	$stmt->bindparam(':id',$_POST['id']);
		 	$stmt->execute();
		 	//var_dump($stmt);
		 	if($stmt->rowCount()){
		 		header('location:./index.php?c=orders&a=index&oid='.$_GET['oid']);	 	 		
		 	}
			} catch (PDOException $e) {
		 		echo $e->getMessage();
			}
			//var_dump($info);
			
		}


		// public function info_doedit(){
		// 	try {		
		// 		$data = array(`:gnum`=>$_POST['gnum'],`:id`=>$_POST['id']);					
		//  		$dsn = 'mysql:host='.HOST.';dbname='.DB.';charset=utf8';		 		
		//  		$pdo =new PDO($dsn,'root','zhulisha');		 		
		//  		$pdo->setAttribute(3,1);
		// 		$sql = "UPDATE orders SET `gnum`=:gnum WHERE id=:id";
		//  	 	$stmt = $pdo->prepare($sql); 	 	
		 	 			 	 		 
		//  	 	$stmt->execute($data);
		//  	 	if($stmt->rowCount()){		 		 	
		//  	 		 header('location:./index.php?c=orders&a=inde');
		//  	 	}
		//  	 	else{
		// 			echo '<script>alert("修改失败");location="./index.php?c=orders&a=index"</script>';

		// 		}
		// 	} catch (PDOException $e) {
		//  		echo $e->getMessage();
		// 	}
		// }
		


		public function __call($a,$b){
			include './View/404.html';
		}
	}