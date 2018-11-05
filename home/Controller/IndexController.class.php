<?php

	class IndexController{
		public function index()
		{
			//var_dump($_POST);
			//1.获取导航栏信息（types：遍历顶级分类  pid：遍历子分类）
			$types = $this->nav();
			$pid = $types;
			//2.获取商品栏信息(根据表单传入  type表的id对应goods表的uid  获取对应分类的数据)
			try {
		 		
		 		$dsn = 'mysql:host='.HOST.';dbname='.DB.';charset=utf8';		 		
		 		$pdo =new PDO($dsn,'root','zhulisha');		 		
		 		$pdo->setAttribute(3,1);

		 		
		 		if(!empty($_POST['search'])){  //检验是否搜索
		 			$p = '%'.$_POST['search'].'%';
		 			$sql = 'SELECT * FROM goods WHERE `status`=0  AND name LIKE :n LIMIT 9';
		 			$stmt = $pdo->prepare($sql);		 			
		 			$stmt->bindparam(':n',$p);
		 			//var_dump($stmt);
		 		}else if(empty($_GET['typeid']))//检验用户是否点击导航栏
		 		{
		 			$sql = "SELECT * FROM goods WHERE `status`=0  LIMIT 9";
		 			$stmt = $pdo->prepare($sql);
		 		}
		 		else
		 		{
		 			$sql = "SELECT * FROM goods WHERE `status`=0 AND typeid=:typeid  LIMIT 9";
		 	 		$stmt = $pdo->prepare($sql);
		 	 		$stmt->bindparam(':typeid',$_GET['typeid']);
		 	 		//var_dump($stmt);
		 	 		$type1 = $_GET['type1'];
		 	 		$type2 = $_GET['type2'];		 	 		
		 	 	}	

		 		$stmt->execute();
		 	 	if($stmt->rowCount()){
		 		 	$goods = $stmt->fetchAll(2);		 	 		
		 	 		
		 	 		//var_dump($goods);exit;
		 	 	}
		 		// var_dump($types);
			 } catch (PDOException $e) {
		 			echo $e->getMessage();
			 }

			include './View/index.html';
						
		}
		//导航方法
		public function nav()
		{
			try {
		 		//1.dsn
		 		$dsn = 'mysql:host='.HOST.';dbname='.DB.';charset=utf8';
		 		//2.得到对象
		 		$pdo =new PDO($dsn,'root','zhulisha');
		 		//3.设置错误
		 		$pdo->setAttribute(3,1);
		 		//echo $dsn;
		 		//准备预处理
		 		$sql = "SELECT id,name,pid,path,display FROM type WHERE display=0 ORDER BY concat(path,id,',') ASC";
		 		//echo $sql;
		 		 //将sql语句模版发送出去预处理
		 	 	$stmt = $pdo->prepare($sql);
		 	 	//执行sql语句
		 		 $stmt->execute();
		 	 	if($stmt->rowCount()){
		 		 	$types = $stmt->fetchAll(2);
		 	 		//用于遍历子类
		 	 		//$pid= $types;
		 	 		//echo $stmt->rowCount();//13;
		 	 	}
		 		// var_dump($types);
			 } catch (PDOException $e) {
		 			echo $e->getMessage();
			 }
			 return $types;
		}

		public function detail()
		{
			try {
		 		//1.dsn
		 		$dsn = 'mysql:host='.HOST.';dbname='.DB.';charset=utf8';
		 		//2.得到对象
		 		$pdo =new PDO($dsn,'root','zhulisha');
		 		//3.设置错误
		 		$pdo->setAttribute(3,1);
		 		//echo $dsn;
		 		//准备预处理
		 		$sql = "SELECT * FROM goods WHERE id=:id AND status=0";
		 		//echo $sql;
		 		 //将sql语句模版发送出去预处理
		 	 	$stmt = $pdo->prepare($sql);
		 	 	$stmt->bindparam(':id',$_GET['id']);		 	 	
		 	 	//执行sql语句
		 		$stmt->execute();
		 	 	if($stmt->rowCount()){
		 		 	$goods = $stmt->fetchAll(2);
		 	 		//用于遍历子类
		 	 		//$pid= $types;
		 	 		//echo $stmt->rowCount();//13;
		 	 	}
		 		//var_dump($goods);
			 } catch (PDOException $e) {
		 			echo $e->getMessage();
			 }
			 include './View/detail.html';
		}

		public function login(){
			//var_dump($_POST);
			include './View/login.html';
		}

		public function dologin()
		{
			//var_dump($_POST);
			//echo strcmp($_POST['code'],'RmCC');//exit;
			if(!strcmp($_POST['code'],'RmCC')==0)
			{
				echo'<script>alert("验证码输入错误，请重新输入！");location="./index.php?c=index&a=login"</script>';
			}else{
			//准备批量绑定数组
			$data = array(':name'=>$_POST['username'],':pwd'=>md5($_POST['password']),':status'=>0);					
			try {
		 		//1.dsn
		 		$dsn = 'mysql:host='.HOST.';dbname='.DB.';charset=utf8';
		 		//2.得到对象
		 		$pdo =new PDO($dsn,'root','zhulisha');
		 		//3.设置错误
		 		$pdo->setAttribute(3,1);
		 		//echo $dsn;
		 		//准备预处理
		 		$sql = "SELECT * FROM user WHERE username=:name AND password=:pwd AND status=:status";		 		
		 		//echo $sql;
		 		 //将sql语句模版发送出去预处理
		 	 	$stmt = $pdo->prepare($sql);		 	 			 	 	
		 	 	//执行sql语句
		 		$stmt->execute($data);
		 	 	if($stmt->rowCount()){
		 		 	$user = $stmt->fetchAll(2);	
		 		 	//var_dump($user);exit;
		 		 	unset($user['password']);
					$_SESSION['home'] = $user[0];	
					//var_dump($_SESSION);exit;			
					echo'<script>alert("登录成功");location="./index.php?c=index&a=index"</script>';	 	 		
		 	 	}else{
		 	 		echo'<script>alert("用户名或密码错误！");location="./index.php?c=index&a=index"</script>';	 	 	
		 	 	}
		 		
			 } catch (PDOException $e) {
		 			echo $e->getMessage();
			 }
			}
		}

		public function outlogin()
		{
			
			//销毁session
			
			unset($_SESSION['home']);
			//跳转主页面
			header('location:./index.php?c=index&a=index');	
		}

		public function userinfo()
		{			
			//var_dump($_SESSION);
			//var_dump($_SESSION['home']['id']);

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
		 		 }	  		
			 } catch (PDOException $e) {
		 			echo $e->getMessage();
			 }
			 //判断新用户
		 	if(empty($info))
		 	{
		 		//var_dump($info);
		 		include './View/Info/info_add.html'; exit;
		 	}

		 	//判断是否点击编辑	
		 	if(!empty($_GET['id']))
		 	{		 		 		
		 		include './View/Info/info_edit.html'; exit;	
		 	}	
			 //var_dump($info);exit;
			 include './View/Info/self_info.html'; 	
		}		

		public function doadd()
		{
			foreach($_POST as $value){
				if($value ==''){
					echo'<script>alert("请填写所有内容！");location="./index.php?c=index&a=userinfo"</script>';exit;
				}
			}
			try {		 		
		 		$dsn = 'mysql:host='.HOST.';dbname='.DB.';charset=utf8';		 		
		 		$pdo =new PDO($dsn,'root','zhulisha');		 		
		 		$pdo->setAttribute(3,1);
		 		$upic = $this->upload();
			 
				$data = array(':uid'=>$_SESSION['home']['id'],':upic'=>$upic,':uname'=>$_POST['uname'],':sex'=>$_POST['sex'],':age'=>$_POST['age'],':ismarry'=>$_POST['ismarry'],':linkname'=>$_POST['linkname'],':phone'=>$_POST['phone'],':address'=>$_POST['address'],':code'=>$_POST['code']);
				$sql = 'INSERT INTO user_info(`id`,`uid`,`upic`,`uname`,`sex`,`age`,`ismarry`,`linkname`,`phone`,`address`,`code`) VALUES(NULL,:uid,:upic,:uname,:sex,:age,:ismarry,:linkname,:phone,:address,:code)';		 		
		 	 	$stmt = $pdo->prepare($sql);
		 	 		
				$stmt->execute($data);
					
		 	 	if($stmt->rowCount()){
		 		 	header('location:./index.php?c=index&a=userinfo');	
		 		 		//var_dump($info);						 		 	 		 	
		 		}else{
				//删除文件上传的图片
				//拼接路径删除图片
					unlink('../public/users/'.$upic);
					echo'<script>alert("添加失败");location="./index.php?c=index&a=userinfo"</script>';
				}		 				 			 		
			} catch (PDOException $e) {
		 		echo $e->getMessage();
			}	
			

		}
		public function doedit()
		{
			//var_dump($_POST);//exit;			
			try {		 		
		 		$dsn = 'mysql:host='.HOST.';dbname='.DB.';charset=utf8';		 		
		 		$pdo =new PDO($dsn,'root','zhulisha');		 		
		 		$pdo->setAttribute(3,1);
		 		//未修改图片
			 	if($_FILES['upic']['name'] == '')
				{
					$data = array(':uname'=>$_POST['uname'],':sex'=>$_POST['sex'],':age'=>$_POST['age'],':ismarry'=>$_POST['ismarry'],':linkname'=>$_POST['linkname'],':phone'=>$_POST['phone'],':address'=>$_POST['address'],':code'=>$_POST['code'],':id'=>$_POST['id']);
					$sql = 'UPDATE user_info SET `uname`=:uname,`sex`=:sex,`age`=:age,`ismarry`=:ismarry,`linkname`=:linkname,`phone`=:phone,`address`=:address,`code`=:code WHERE id=:id';		 		
		 	 		$stmt = $pdo->prepare($sql);
		 	 		//var_dump($sql);exit;	
		 	 		//var_dump($_POST);exit;	
					$stmt->execute($data);
					//$stmt->debugDumpParams();
		 	 		if($stmt->rowCount()){
		 		 		header('location:./index.php?c=index&a=userinfo');	
		 		 		//var_dump($info);						 		 	 		 	
		 			 } 		 				 			 		
				}else
				{	
					$upic1 = $_POST['upic'];//保存旧文件以便后面删除
					$upic2 = $this->upload();//接收新文件，在修改失败时删除
					
					$data = array(':upic'=>$upic2,':uname'=>$_POST['uname'],':sex'=>$_POST['sex'],':age'=>$_POST['age'],':ismarry'=>$_POST['ismarry'],':linkname'=>$_POST['linkname'],':phone'=>$_POST['phone'],':address'=>$_POST['address'],':code'=>$_POST['code'],':id'=>$_POST['id']);
					$sql = 'UPDATE user_info SET `upic`=:upic,`uname`=:uname,`sex`=:sex,`age`=:age,`ismarry`=:ismarry,`linkname`=:linkname,`phone`=:phone,`address`=:address,`code`=:code WHERE id=:id';		 		
		 	 		$stmt = $pdo->prepare($sql);
		 	 			
					$stmt->execute($data);
		 	 		if($stmt->rowCount()) 		 	 		 	 			
					{
						//删除旧头像
						// echo $info->sql;	
						unlink('../public/users/'.$upic1);		
						header('location:./index.php?c=index&a=userinfo');	
					}else{
						//删除文件上传的图片
						//拼接路径删除图片
						//echo $info->sql;
						unlink('../public/users/'.$upic2);
						header('location:./index.php?c=index&a=userinfo');	
					}
				}	 	 	
		 					 			 		 
		 		 	 		
			 } catch (PDOException $e) {
		 			echo $e->getMessage();
			 }		
		}

		//头像上传函数
		public function upload()
		{
			//文件上传
			$upload = new Uploads('upic');
			//var_dump($upload);exit;
			//初始化允许上传类型
			$upload->typelist = array('image/x-png','image/pjpeg','image/jpeg','image/gif','image/png');
			//初始化保存路径
			$upload->path = '../public/users/';
			//上传开始
			if(!$upload->upload())
			{				
				echo'<script>alert("头像上传失败，原因：'.$upload->error.'");location="./index.php?c=index&a=userinfo"</script>';exit;
			}
			//返回上传的文件名
			return $upload->savename;
			//var_dump($_POST);exit;			
		}		


		public function register()
		{
			include './View/register.html';
		}

		public function doreg()
		{

			//var_dump($_POST);//exit;
			//var_dump(strcmp($_POST['code'],'RmCC'));exit;
			if(!strcmp($_POST['code'],'RmCC')==0)
			{
				echo'<script>alert("验证码输入错误，请重新输入！");location="./index.php?c=index&a=register"</script>';exit;
			}
			if($_POST['password']!=$_POST['repassword'])
			{
				//判断你的密码是否正确			
				echo'<script>alert("两次输入的密码不一致！");location="./index.php?c=index&a=register"</script>';;exit;		
			}		

			//准备批量绑定数组
			// $_POST['password']=md5($_POST['password']);	
			// $_POST['level'] = 0;
			// $_POST['status'] = 0;	
			// $_POST['addtime']=time();
			$data = array(':username'=>$_POST['username'],':password'=>md5($_POST['password']),':level'=>0,':status'=>0,':addtime'=>time());	
			//var_dump($data);exit;		
			try {
		 		//1.dsn
		 		$dsn = 'mysql:host='.HOST.';dbname='.DB.';charset=utf8';
		 		//2.得到对象
		 		$pdo =new PDO($dsn,'root','zhulisha');
		 		//3.设置错误
		 		$pdo->setAttribute(3,1);
		 		//echo $dsn;
		 		//准备预处理
		 		$sql = "INSERT INTO user(`id`,`username`,`password`,`level`,`status`,`addtime`) VALUES(NULL,:username,:password,:level,:status,:addtime)";		 		
		 		//echo $sql;
		 		 //将sql语句模版发送出去预处理
		 	 	$stmt = $pdo->prepare($sql);		 	 			 	 	
		 	 	//执行sql语句
		 		$stmt->execute($data);
		 	 	if($stmt->rowCount()){	 			
		 		 	echo'<script>alert("注册成功，点击“确定”进入登录页面");location="./index.php?c=index&a=login"</script>';
		 	 	}
		 		
			} catch (PDOException $e) {
		 		echo $e->getMessage();
		    }
		}
		
	}

