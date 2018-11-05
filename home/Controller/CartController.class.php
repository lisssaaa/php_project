<?php
	class CartController{
		public function index()
		{			
			if(!empty($_SESSION['home']))
			{
				//用于计算总和
				 $total = 0;
				 $num = 0;
				 //显示购物车界面
				 //var_dump($_SESSION);
				include './View/Cart/showCart.html';
			}else{
				echo "<script>alert('请先登录');location='./index.php?c=index&a=login'</script>";
			}
			
		}
		//添加购物车
		public function addCart()
		{
			if(!empty($_SESSION['home']))
			{
				//获取加入的商品id
				if(isset($_GET['id']))
				{
					//判断是否已经添加过了
					if(!empty($_SESSION['cart'][$_GET['id']]))
					{
						$_SESSION['cart'][$_GET['id']]['num'] += 1;
						include './View/Cart/addCart.html';exit;
					}
					//指定商品
					try {
						$dsn = 'mysql:host='.HOST.';dbname='.DB.';charset=utf8';
						//echo $dsn;exit;
						$pdo = new PDO($dsn,'root','zhulisha');
						$pdo->setAttribute(3,1);

						$sql="SELECT * FROM goods WHERE id=:id";
						$stmt=$pdo->prepare($sql);
						$id =$_GET['id'];
						//绑定参数
						$stmt->bindParam(':id',$id);
						//执行sql语句
						$stmt->execute();
						if($stmt->rowCount()){
							$row = $stmt->fetch(2);
						}
						//var_dump($row);
						//添加一个购买数量字段
						$row['num']=1;
						//var_dump($row);
						//将商品添加到购物车
						$_SESSION['cart'][$id]=$row;
						//	var_dump($_SESSION);
						include './View/Cart/addCart.html';

					} catch (PDOException $e) {
						echo $e->getMessage();
					}
				}else{
					//你没有指定商品
					echo "<script>alert('请添加指定商品');location='./index.php?c=index&a=index'</script>";
				}
			}else{
				echo "<script>alert('请先登录');location='./index.php?c=index&a=login'</script>";
			}
					
		}
		//数量增加
		public function jia(){
			$id = $_GET['id'];
			$_SESSION['cart'][$id]['num']+=1;
			//$_SESSION['cart'][$id]['num']=$_SESSION['cart'][$id]['num']+1;
			header('location:index.php?c=cart&a=index');
		}
		//数量减少
		public function jian(){
			$id = $_GET['id'];
			$_SESSION['cart'][$id]['num']-=1;
			//$_SESSION['cart'][$id]['num']=$_SESSION['cart'][$id]['num']-1;
			if($_SESSION['cart'][$id]['num']<1){
				$_SESSION['cart'][$id]['num']=1;
			}
			header('location:index.php?c=cart&a=index');
		}
		//删除某个商品
		public function del(){
			unset($_SESSION['cart'][$_GET['id']]);
			header('location:index.php?c=cart&a=index');
		}
		//清空购物车
		public function delete(){
			unset($_SESSION['cart']);
			header('location:index.php?c=cart&a=index');
		}
		
	}