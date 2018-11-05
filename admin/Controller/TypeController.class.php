<?php

	//用户管理模块操作类
	
	class TypeController{
		//用户列表
		public function index(){
			/*搜索条件*/			
			//var_dump($_GET);
			//判断是否点击查看子分类
			//判断$_GET['pid']是否有值
			//有值：查看了子分类，无值则没点击子分类
			
			if(empty($_GET['pid'])){
				//点击	
				$map['pid'] = 0;	
			}
			else{		
				$map['pid'] = $_GET['pid'];				
			}

			//1.将所有数据拿出来
			//1.1连接数据库
			$type = new Model('type');					
			//1.2拿出所有数据			
			
			$typelist = $type->where($map)->select();				

			//用来输出编号的
			$i = 1;
			
			include './View/Type/index.html';
		}

		//用户添加
		public function add(){
			//确定添加的是顶级分类还是子分类
			//判断$_GET['pid']是否有值
			//无值添加顶级分类，有值则添加子分类
			//var_dump($_GET['pid']);
			if(empty($_GET['pid'])){
				//添加顶级分类
				$pid = 0;
				$path = '0,';
			}else{
				//添加子分类  
				//var_dump($_GET);				
				$type = new Model('type');
				$pid = $_GET['pid'];
				$typeinfo = $type->find($pid);
				//var_dump($typeinfo);
				//子类的path：父类的path拼接上父类的id拼接上一个逗号
				//得到父类的id 我们可以通过父类的id查询出父类所有信息
				$path = $typeinfo['path'].$pid.',';
			}
			include './View/Type/add.html';
		}

		//处理添加页面
		public function doadd(){
			//var_dump($_POST);			
			$type = new Model('type');			
			$bool = $type->add($_POST);			
			if($bool){
				echo'<script>alert("添加成功");location="./index.php?c=type&a=index"</script>';
			}else{
				echo'<script>alert("添加失败");location="./index.php?c=type&a=add"</script>';
			}
		}

		//删除操作方法
		public function del(){
			var_dump($_GET);
			///////////////////////////////////////////
			//通过id进行查询看他为pid 的时候是否有值 //
			///////////////////////////////////////////
					
			$type = new Model('type');
			$map['pid']=$_GET['pid'];
			$result =$type->where($map)->select();
			if($result){
				echo '请先删除子分类！！';exit;
			}else{
				if($type->delete($_GET['pid'])){
				header('location:index.php?c=type&a=index');
			}else{
				header('location:index.php?c=type&a=index');
			}
			}
			
		}

		//开启和禁用
		public function display(){
			//var_dump($_GET);
			//修改数据
			$data = array();
			$data['id']=$_GET['id'];
			$data['display']=$_GET['display'];

			$type = new Model('type');
			if($type->update($data)){
				header('location:index.php?c=type&a=index');
			}else{
				header('location:index.php?c=type&a=index');
			}
		}

		//用户修改
		public function edit(){
			//var_dump($_GET);
			$type = new Model('type');
			$typelist = $type->find($_GET['id']);
			//var_dump($typelist);
			include './View/Type/edit.html';
		}
		//处理修改页面
		public function doedit(){
			var_dump($_POST);					
			$user = new Model('type');
			if($user->update($_POST)){
				echo '<script>alert("修改成功");location="./index.php?c=type&a=index"</script>';
			}else{
				echo '<script>alert("修改失败");location="./index.php?c=type&a=edit"</script>';

			}

		}

		public function __call($a,$b){
			include './View/404.html';
		}
	}