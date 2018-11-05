<?php
	class GoodsController{
		public function index()
		{
			/*搜索条件*/
			//var_dump($_GET);
			if(!empty($_GET['name'])){
				$map['name']=array('like',$_GET['name']);
			}else{
				$map='';
			}
		
			$goods = new Model('goods');

			/*实现分页*/
			//分页1 拿到总条数
			$total = $goods->where($map)->count();
			//分页2 得到分页对象
			$page = new Page($total,3);
			/*分页结束*/

			$goodslist = $goods->where($map)->limit($page->limit)->select();
			//var_dump($goodslist);
			//通过商品的typeid在type表中查询分类名称存储在typeid中
			$type = new Model('type');
			if(!empty($goodslist)){
				foreach ($goodslist as $key => $value) {

					$typelist = $type->find($value['typeid']);
					//var_dump($typelist);
					$goodslist[$key]['typeid'] = $typelist['name'];
				}	
			}	
			$i = 1;	
			include './View/Goods/index.html';
		}
		//添加商品
		public function add()
		{
			//echo '商品添加';
			//查询分类表中的路径信息并遍历到添加页面的商品分类下拉栏
			$type = new Model('type');
			//使用CONCAT()字段拼接以便排序
			$result = $type->order('CONCAT(path,id,",") ASC')->select();
			//var_dump($result);		

			include './View/Goods/add.html';
		}
		//处理添加页面
		public function doadd(){
			//var_dump($_POST);	
			//var_dump($_FILES);
			//验证是否填写完整
			foreach($_POST as $value){
				if($value ==''){
					echo'<script>alert("请填写所有内容！");location="./index.php?c=goods&a=add"</script>';exit;
				}
			}
			//文件上传
			$upload = new Uploads('pic');
			//var_dump($upload);exit;
			//初始化允许上传类型
			$upload->typelist = array('image/x-png','image/pjpeg','image/jpeg','image/gif','image/png');
			//初始化保存路径
			$upload->path = '../public/goods/';
			//上传开始
			if(!$upload->upload())
			{
				//var_dump($upload->error);
				//var_dump($_FILES['type']);
				echo'<script>alert("商品图片上传失败，原因：'.$upload->error.'");location="./index.php?c=goods&a=add"</script>';exit;
			}
			//保存成功上传的文件名到$_POST['pic']中
			$_POST['pic'] = $upload->savename;
			//var_dump($_POST);
			//var_dump($_FILES);
			//进行添加到数据库操作
			$goods = new Model('goods');			
			$bool = $goods->add($_POST);			
			if($bool){
				echo'<script>alert("添加成功");location="./index.php?c=goods&a=index"</script>';
			}else{
				//删除文件上传的图片
				//拼接路径删除图片
				unlink('../public/goods/'.$_POST['pic']);
				echo'<script>alert("添加失败");location="./index.php?c=goods&a=add"</script>';
			}
		}

		//删除操作方法
		public function del(){
			//var_dump($_GET);
					
			$goods = new Model('goods');
			$goodslist = $goods->find($_GET['id']);			
			if($goods->delete($_GET['id'])){
				unlink('../public/goods/'.$goodslist['pic']);
				header('location:index.php?c=goods&a=index');
			}else{
				header('location:index.php?c=goods&a=index');
			}
		}
		//修改商品信息
		public function edit(){
			//var_dump($_GET);
			
			//获取该商品信息
			$goods = new Model('goods');
			$goodslist = $goods->find($_GET['id']);
			//var_dump($goodslist);

			$type = new Model('type');
			//查询分类表中的路径信息并遍历到添加页面的商品分类下拉栏	
			$result = $type->order('CONCAT(path,id,",") ASC')->select();
			//var_dump($result);
			
			include './View/Goods/edit.html';
		}

		//处理修改页面
		public function doedit(){
			//var_dump($_POST);	exit;				
			$goods = new Model('goods');
			if($_FILES['pic']['name'] == '')
			{
				if($goods->update($_POST))
				{							
					echo'<script>alert("修改成功");location="./index.php?c=goods&a=index"</script>';
				}else{					
					echo'<script>alert("修改失败");location="./index.php?c=goods&a=edit"</script>';
				}
			}else
			{
				$pic1 = $_POST['pic'];//保存旧文件以便后面删除
				$pic2 = $this->upload();//接收新文件，在修改失败时删除
				$_POST['pic'] = $pic2;
				
				//var_dump($_POST);						
				if($goods->update($_POST))
				{
					//删除旧图片
					// echo $info->sql;	
					unlink('../public/goods/'.$pic1);		
					echo'<script>alert("修改成功");location="./index.php?c=goods&a=index"</script>';
				}else{
					//删除文件上传的图片
					//拼接路径删除图片
					//echo $info->sql;
					unlink('../public/goods/'.$pic2);
					echo'<script>alert("修改失败");location="./index.php?c=goods&a=edit"</script>';
				}
			}
		}	

		//商品图片上传函数
		public function upload()
		{
			//文件上传
			$upload = new Uploads('pic');
			//var_dump($upload);exit;
			//初始化允许上传类型
			$upload->typelist = array('image/x-png','image/pjpeg','image/jpeg','image/gif','image/png');
			//初始化保存路径
			$upload->path = '../public/goods/';
			//上传开始
			if(!$upload->upload())
			{				
				echo'<script>alert("头像上传失败，原因：'.$upload->error.'");location="./index.php?c=goods&a=index"</script>';exit;
			}
			//返回上传的文件名
			return $upload->savename;
			//var_dump($_POST);exit;			
		}		


		//上架和下架
		public function status(){
			//var_dump($_GET);
			//修改数据
			$data = array();
			$data['id']=$_GET['id'];
			$data['status']=$_GET['status'];

			$goods = new Model('goods');
			if($goods->update($data)){
				header('location:index.php?c=goods&a=index');
			}else{
				header('location:index.php?c=goods&a=index');
			}
		}
	}