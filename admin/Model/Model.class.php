<?php

	//定义一个操作数据库类
	
	class Model{

		protected $link;//连接数据库对象
		protected $tabName;//存储表名的
		protected $sql='';//存储上一次操作的sql语句
		protected $limit;//用来存储要显示多少条
		protected $order;//用来存储排序信息
		protected $fields='*';//用来存储要查询的字段
		protected $allFields;//存储缓存的数据库字段
		protected $where;//子条件查询

		//构造方法里面是连接数据库
		public function __construct($tabName){
			
			//初始化数据库连接
			$this->getConnect();

			//将你要操作的数据表存储起来
			$this->tabName =$tabName;

			//获取数据库字段
			$this->getFields();

		}

		//增加
		//$data['name']='zhansan';
		//$data['age']=18;
		//$data['sex']=1;
		//$data['city']='背景';
		public function add($data){
			//var_dump($data);exit;
			
			//获取数组的键做为我们sql语句的字段列表
			$key = array_keys($data);

			//得到键之后将数组变为字符串即可
			$keys = join('`,`',$key);

			//获取数组的值做我们sql语句的添加的值
			$value = array_values($data);

			//得到值之后将数组变为字符串即可
			$values = join("','",$value);
			//echo $values;exit;

			//var_dump($values);exit;
			$sql="INSERT INTO {$this->tabName}(`{$keys}`) VALUES('{$values}')";
			//echo $sql;
			return $this->execute($sql);
		}
		//删除
		//传递一个参数 这个参数是id值
		public function delete($id=''){
			//判断你是否是使用where条件
			if(empty($id)){
				//说明他要使用where删除
				$where = $this->where;
			}else{
				//要使用id删除
				$where = 'WHERE id='.$id;
			}
			//确保你使用一种条件删除
			if(empty($where)){
				echo '请输入id或者使用where条件删除否则删除失败';exit;
			}

			$sql="DELETE FROM {$this->tabName} {$where}";

			//echo $sql; exit;
			return $this->execute($sql);
		}
		//修改
		//$data['name']=zhangsan;
		//$data['sex']=1;
		//$data['age']=19;
		//$data['city']=11111;
		//$data['id']=1;
		
		public function update($data){
			//var_dump($data);exit;
			//1.判断$data是不是数组
			if(!is_array($data)){
				return false;
			}
			//1.2判断他是否使用id作为修改条件 或者使用where条件作为是修改内容
			if(!empty($data['id'])){
				//用id作为修改条件
				$where = ' WHERE  id='.$data['id'];
			}else{
				//使用where条件
				$where = $this->where;
			}
			if(empty($where)){
				return false;
			}
			//2.将传递过来的数组让他的键和值拼接在一起
			$result ='';
			foreach($data as $key=>$value){
				//判断id字段
				if($key !='id'){
					$result .="`{$key}`='{$value}',";
				}
			}
			//echo $result;exit;
			//3.将多出来的逗号去掉
			$result = rtrim($result,',');
			//echo $result;exit;
			$sql="UPDATE {$this->tabName} SET {$result} {$where}";
			//echo $sql;exit;
			return $this->execute($sql);
		}
		//查询 所有数据
		public function select(){
			$sql="SELECT {$this->fields} FROM {$this->tabName}  {$this->where} {$this->order} {$this->limit}";
			//echo $sql;exit;
			return $this->query($sql);
		}
		//查询单条数据
		public function find($id){

			$sql="SELECT * FROM {$this->tabName} WHERE id={$id}";
			//echo $sql;
			$list = $this->query($sql);
			//返回一维数组

			return $list[0];
		}
		//查询总共有多少条数据
		public function count(){
			$sql="SELECT COUNT(*) as total FROM {$this->tabName} {$this->where}";
			//echo $sql;exit;
			$list= $this->query($sql);
			//var_dump($list);
			return $list[0]['total'];

		}
		//每页显示多少条
		public function limit($limit){
			$this->limit = ' LIMIT '.$limit;
			//保证连贯操作
			return $this;
			//echo $this->limit;
		}
		//排序
		//order by 字段 ASC |DESC
		public function order($order){

			$this->order = 'ORDER BY '.$order;
			//echo $this->order;
			//保证连贯操作
			return $this;
		}

		//字段筛选
		public function field($field=array()){
			//var_dump($field);
			//判断field是不是数组
			if(!is_array($field)){
				//返回当前对象
				return $this;
			}
			//检测数据库内容 删除没有的字段
			$field = $this->check($field);
			if(empty($field)){
				return $this;
			}
			//拼接字符串得到想要的内容
			$this->fields = '`'.join('`,`',$field).'`';
			//echo $this->fields;
			//保证连贯操作
			return $this;
		}
		//where 条件
		//进来判断data是几维数组  如果是一维直接拼接等于 如果是二维 需要进行判断二维数组的第一个单元是什么方式我们好进行什么方式的拼接操作
		//最终得到一个数组  
		//最后将数组拼接成为字符串 中间用 and
		public function where($data){
			//var_dump($data);
			//判断传递过来的必须是一个数组 而且这个数组不能为空
			if(is_array($data) && !empty($data)){
				
				//说明你得到的变量即是数组又是非空
				//循环遍历数组得到键和值
				$result = array();
				foreach($data as $key=>$value){
					//echo $key;
					//var_dump($value);
					//判断你的值是否是数组 如果他不是数组说明你传递是你要操作的内容为等于
					//如果传递过来的值是数组说明他要其他的操作
					if(is_array($value)){
						//复杂查询
						//var_dump($value);exit;
						switch($value[0]){
							 case 'like':

								$result[]='`'.$key.'` LIKE "%'.$value[1].'%"';
			
							 	break;
							 case 'lt':
							 	$result[]="`{$key}` < '{$value[1]}'";
							 	break;
							 case 'gt':
							 	$result[]="`{$key}` > '{$value[1]}'";
							 	break;
							 case 'in':
							 	$result[] ="`{$key}` in('{$value[1]}')";
							 	break;
						}
					}else{
						//简单的等于查询条件
						
						$result[]="`".$key."`='".$value."'";

					}

				}

				//var_dump($result);exit;

				$where = ' WHERE '.join(' and ',$result);
				//echo $where;exit;
				//where id=20 and sex=1；
				$this->where = $where;

			}else{
				//保证连贯操作
				return $this;
			}

			return $this;
		}


		/******************辅助方法***********************/
		//初始化连接数据库方法
		public function getConnect(){
			$this->link = mysqli_connect(HOST,USER,PWD);
			if(mysqli_connect_errno($this->link)>0){
				echo mysqli_connect_error($this->link);exit;
			}
			mysqli_select_db($this->link,DB);
			mysqli_set_charset($this->link,CHARSET);
		}
		//查询操作
		public function query($sql){
			//将本次操作的sql语句存储在sql属性中
			$this->sql = $sql;
			$result = mysqli_query($this->link,$sql);
			if($result && mysqli_num_rows($result)>0){
				while($row = mysqli_fetch_assoc($result)){
					$list[]=$row;
				}
			}
			return $list;
		}
		//用于添加 删除 修改
		public function execute($sql){
			$this->sql = $sql;
			$result = mysqli_query($this->link,$sql);
			if($result&& mysqli_affected_rows($this->link)>0){
				//判断你是否是添加操作如果是添加操作返回添加操作的id
				if(mysqli_insert_id($this->link)){
					return mysqli_insert_id($this->link);
				}
				return true;
			}else{
				return false;
			}
		}

		//获取数据库字段
		public function getFields(){
			//查看表信息的数据库语句
			//DESC info
			$sql ="DESC {$this->tabName}";

			//发送sql语句 查询
			$result = $this->query($sql);
			//var_dump($result);
			//新建一个数组用来存储数据库字段
			$fields = array();
			foreach($result as $value){
				//var_dump($value['Field']);
				$fields[]= $value['Field'];
			}
//			var_dump($fields);
			//设置为缓存字段
			$this->allFields = $fields;
		}

		//检测字段的方法
		public function check($arr){
			//var_dump($arr);
			//传递进来的数组需要拿到没个单元的值和存储字段数组比较如果存储保留 如果不存在删除
			//遍历传递过来的数组得到里面的键和值
			foreach($arr as $key=>$value){
				//var_dump($value);
				//判断你的值是否存在于缓存字段的数组中
				if(!in_array($value,$this->allFields)){
						unset($arr[$key]);
				}
			}
			return $arr;
		}



		//用于访问成员属性
		public function __get($key){
			return $this->$key;
		}


		//析构方法
		public function __destruct(){
			mysqli_close($this->link);
		}
	}

	