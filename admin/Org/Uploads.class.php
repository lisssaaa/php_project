<?php

	//文件上传类
		class Uploads{
			protected $upfile;//保存上传文件信息
			protected $error;//保存错误信息
			protected $typelist=array();//允许上传的类型
			protected $savename;//保存图片的名字
			protected $path ;//保存路径
			
			public function __construct($name){
				//后去文件信息
				$this->upfile = $_FILES[$name];
			}
			
			public function upload(){
				$this->path = rtrim($this->path,'/').'/';
				//判断上面所有方法是否都成功 成功返回true 失败返回false
				return $this->checkError() && $this->checkType() && $this->createName() && $this->move();
			}
			
		
			//1.判断错误号
			protected function checkError(){
				//判断错误号
				if($this->upfile['error']>0){
					switch($this->upfile['error']){
						case 1:
							$info='上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值。';
							break;
						case 2:
							$info='上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值。';
							break;
						case 3:
							$info ='文件只有部分被上传';
							break;
						case 4:
							$info='没有文件被上传';
							break;
						case 6:
							$info='找不到临时文件夹。';
							break;
						case 7:
							$info='文件写入失败';
							break;
					}
					//保存错误信息
					$this->error=$info;
					return false;
				}
				return true;
			}
			
			//判断类型 如果成功返回true
			public function checkType(){
				//判断设置了类型
				//var_dump($this->typelist);
				//判断你的文件类型是否是有设置类型
				if(count($this->typelist)){
					//判断你上传的类型是否符合我设置允许上传类型
					if(!in_array($this->upfile['type'],$this->typelist)){
						$this->error='文件类型不符合';
						return false;
					}
				}
				return true;
			}
			//起名字 如果成功返回true
			protected function createName(){
				//获取文件后缀名
				$ext = pathinfo($this->upfile['name'],PATHINFO_EXTENSION);
				
				//保存的名字
				$this->savename = date('Ymd').uniqid().mt_rand(0,9999).'.'.$ext;
				return true;
				
			}
			//移动 如果成功返回true 
			protected function move(){
				//判断是否是post传递
				if(is_uploaded_file($this->upfile['tmp_name'])){
					//判断移动是否成功
					if(move_uploaded_file($this->upfile['tmp_name'],$this->path.$this->savename)){
						return true;
					}else{
						$this->error ='文件移动失败';
						return false;
					}
				}else{
					$this->error='别黑我';
					return false;
				}
			}
			

			
			
			public function __set($key,$value){
				//设置文件上传类型
				if($key=='typelist'){
					$this->$key=$value;
				}
				//设置文件上传路径
				if($key == 'path'){
					$this->$key=$value;
				}
			}
			public  function __get($key){
				//获取文件上传成功的名字
				if($key == 'savename'){
					return $this->$key;
				}
				//获取文件上传失败的错误信息
				if($key=='error'){
					return $this->$key;
				}
				
			}
			
		}