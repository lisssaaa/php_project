<?php

	class IndexController{
		public function index()
		{
			//include './View/index.html';			
			if(empty($_SESSION['admin'])){
				header('location:./index.php?c=index&a=login');				
			}else{
				//var_dump($_SESSION);				
				//显示后台主页面
				$info = new Model('user_info');
				
				$map['uid'] = $_SESSION['admin']['id'];
				//var_dump($map);
				$list = $info->where($map)->select();
				//var_dump($_SESSION['admin']['username']);
				//var_dump($list);
				
				$data['pic'] = $list[0]['upic'];
				//var_dump($data);
				include './View/index.html';
			}			
		}
		public function login(){
			include './View/Login/login.html';
		}

		public function dologin()
		{
			//开启session
			//session_start();
			//var_dump($_POST);
			//做验证：用户名，密码长度符合规则
			$user = new Model('user');
			$map['username'] = $_POST['username'];
			$map['password'] = md5($_POST['pwd']);
			$map['level'] = array('gt',1);
			$map['status'] = 0;
			//var_dump($map);
			$userinfo = $user->where($map)->select();
			//var_dump($userinfo);exit;
			//如果有值说明登录成功
			//将我们登录成功的用户存储在session中 但是除密码以外
			if(!empty($userinfo)){
				unset($userinfo['password']);
				$_SESSION['admin'] = $userinfo[0];				
				echo'<script>alert("登录成功");location="./index.php?c=index&a=index"</script>';
			}else{
				//如果没有值说明 用户名或者密码出现问题
				echo'<script>alert("用户名或密码错误，请重新登录");location="./index.php?c=index&a=login"</script>';
			}
		}

		public function outlogin()
		{
			
			//销毁session
			unset($_SESSION['admin']);
			//跳转主页面
			header('location:./index.php?c=index&a=login');	
		}
	}

