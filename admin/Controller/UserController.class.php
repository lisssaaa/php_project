<?php

	//用户管理模块操作类
	
	class UserController{
		//用户列表
		public function index(){
			/*搜索条件*/
			//var_dump($_GET);
			if(!empty($_GET['username'])){
				$map['username']=array('like',$_GET['username']);
			}else{
				$map='';
			}

			//1.将所有数据拿出来
			//1.1连接数据库
			$user = new Model('user');
			/*实现分页*/
			//分页1 拿到总条数
			$total = $user->where($map)->count();
			//分页2 得到分页对象
			$page = new Page($total,3);

			/*分页结束*/
			//1.2拿出所有数据
			$userlist = $user->where($map)->limit($page->limit)->select();
			//var_dump($userlist);
			//用来输出编号的
			$i=1;
			include './View/User/index.html';
		}

		//用户添加
		public function add(){
		
			include './View/User/add.html';
		}
		//处理添加页面
		public function doadd(){
			//var_dump($_POST);
			//1.判断你的密码是否正确
			if($_POST['password']!=$_POST['repassword']){
				echo '<a href="index.php?c=user&a=add">两次输入的密码不一致</a>';exit;
			}
			//2.删除多余的密码字段
			unset($_POST['repassword']);

			//3.将密码进行加密
			$_POST['password']=md5($_POST['password']);
			//var_dump($_POST);
			//4.将缺少的数据库字段添加进来
			$_POST['status']=0;
			$_POST['addtime']=time();
			//var_dump($_POST);
			//5.将post数组中的值添加到数据库中
			//5.1得到model类对象
			$user = new Model('user');
			//5.2添加数据
			$bool = $user->add($_POST);
			//5.3判断是否添加成功
			if($bool){
				echo'<script>alert("添加成功");location="./index.php?c=user&a=index"</script>';
			}else{
				echo'<script>alert("添加失败");location="./index.php?c=user&a=add"</script>';
			}
		}

		//删除操作方法
		public function del(){
			//var_dump($_GET);
			//1.接受id
			$id = $_GET['id'];
			//2.连接数据库进行操作
			$user = new Model('user');
			if($user->delete($id)){
				header('location:index.php?c=user&a=index');
			}else{
				header('location:index.php?c=user&a=index');
			}
		}

		//开启和禁用
		public function status(){
			//var_dump($_GET);
			//修改数据
			$data = array();
			$data['id']=$_GET['id'];
			$data['status']=$_GET['status'];

			$user = new Model('user');
			if($user->update($data)){
				header('location:index.php?c=user&a=index');
			}else{
				header('location:index.php?c=user&a=index');
			}
		}

		//用户修改
		public function edit(){
			//var_dump($_GET);
			$user = new Model('user');
			$userlist = $user->find($_GET['id']);
			//var_dump($userlist);
			include './View/User/edit.html';
		}
		//处理修改页面
		public function doedit(){
			//var_dump($_POST);					
			$user = new Model('user');
			if($user->update($_POST)){
				echo '<script>alert("修改成功");location="./index.php?c=user&a=index"</script>';
			}else{
				echo '<script>alert("修改失败");location="./index.php?c=user&a=edit"</script>';

			}

		}
		//用户详情页
		public function info()
		{	//var_dump($_GET['uid']);
			
			$map['uid'] = $_GET['uid'];
			$info = new Model('user_info');			
			$infolist = $info->where($map)->select();
			//var_dump($infolist[0]);
			//判断是否有用户详情
			if(empty($infolist[0])){
				//没有,添加用户详情
				include './View/User/info_add.html';
			}
			else{		
				//存在用户详情，编辑
				include './View/User/info_edit.html';				
			}
			
		}
		//添加用户详情
		public function info_add()
		{	
			//var_dump($_POST);exit;
			//验证是否填写完整
			foreach($_POST as $value){
				if($value ==''){
					echo'<script>alert("请填写所有内容！");location="./index.php?c=user&a=info_add"</script>';exit;
				}
			}
			//做验证：符合规则!!!
			
			//保存成功上传的文件名到$_POST['upic']中
			$_POST['upic'] = $this->upload();
			//var_dump($_POST);exit;

			//进行添加到数据库操作
			$info = new Model('user_info');			
			$bool = $info->add($_POST);			
			if($bool){
				echo'<script>alert("添加成功");location="./index.php?c=user&a=index"</script>';
			}else{
				//删除文件上传的图片
				//拼接路径删除图片
				unlink('../public/users/'.$_POST['upic']);
				echo'<script>alert("添加失败");location="./index.php?c=user&a=info"</script>';
			}
		}

		//编辑用户详情
		public function info_edit()
		{	
			//var_dump($_POST);		
			//var_dump($_FILES);	
			//判断是否上传新头像，而是否更新文件
			$info = new Model('user_info');
			if($_FILES['upic']['name'] == '')
			{
				if($info->update($_POST))
				{							
					echo'<script>alert("修改成功");location="./index.php?c=user&a=index"</script>';
				}else{					
					echo'<script>alert("修改失败");location="./index.php?c=user&a=info"</script>';
				}
			}else
			{	
				$upic1 = $_POST['upic'];//保存旧文件以便后面删除
				$upic2 = $this->upload();//接收新文件，在修改失败时删除
				$_POST['upic'] = $upic2;
				//var_dump($_POST);						
				if($info->update($_POST))
				{
					//删除旧头像
					// echo $info->sql;	
					unlink('../public/users/'.$upic1);		
					echo'<script>alert("修改成功");location="./index.php?c=user&a=index"</script>';
				}else{
					//删除文件上传的图片
					//拼接路径删除图片
					//echo $info->sql;
					unlink('../public/users/'.$upic2);
					echo'<script>alert("修改失败");location="./index.php?c=user&a=info"</script>';
				}
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
				echo'<script>alert("头像上传失败，原因：'.$upload->error.'");location="./index.php?c=user&a=index"</script>';exit;
			}
			//返回上传的文件名
			return $upload->savename;
			//var_dump($_POST);exit;			
		}

		public function __call($a,$b){
			include './View/404.html';
		}
	}