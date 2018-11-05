<?php

	//分页类
	class Page{
		protected $num;//每页显示数
		protected $total;//总条数
		protected $amount;//总页数
		protected $current;//当前页码数
		protected $offset;//当前偏移量
		protected $limit ;//分页页码
		public function __construct($total,$num){
			//1.每页显示数
			$this->num = $num;
			//2.总条数
			$this->total =$total;
			//3.总页数
			$this->amount = ceil($total/$num);

			//4.当前页码数
			//初始化当前页码
			$this->init();

			//5.偏移量
			$this->offset = ($this->current-1)*$num;

			//6.分页字符串
			$this->limit= "{$this->offset},{$this->num}";

			
		}
		//初始化当前页码
		public function init(){
			$this->current = empty($_GET['page'])?'1':$_GET['page'];
			//判断最小值
			if($this->current<1){
				$this->current=1;
			}
			//判断最大值
			if($this->current>$this->amount){
				$this->current=$this->amount;
			}
		}

		public function __get($key){
			if($key =='limit'){
				return $this->limit;
			}elseif($key == 'offset'){
				return $this->offset;
			}elseif($key == 'amount'){
				return $this->amount;
			}elseif($key == 'current'){
				return $this->current;
			}elseif($key =='total'){
				return $this->total; 
			}
		}

		//获取按钮的方法
		public function getButton(){
			//需要将$_GET这个数组里面的值赋值给变量prev 和next
			//判断第一次进来没有page 时候
			 $_GET['page'] = empty($_GET['page'])?'1':$_GET['page'];
			 //将所有get数组中的值赋值给这个两个变量
			 $prev = $next = $_GET;
			 //var_dump($prev);
			 //var_dump($next);

			 //上一页
			 //显示上一页内容
			 $prev['page']=$prev['page']-1;
			 //判断你的上一页不能超出范围 如果超出使用1
			 if($prev['page']<1){
			 	$prev['page']=1;
			 }

			 //下一页
			 $next['page']=$next['page']+1;
			 //判断范围  如果超出使用最大页码
			 if($next['page']>$this->amount){
			 	$next['page']=$this->amount;
			 }
			 //拼接路径
			 //http://localhost/oto/ss29/A40_MVC/index.php?c=user&a=index&page=1
			 
			// var_dump($_SERVER);
			 $url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'];
			 
			 //将数组中的每个单元以参数的形式拼接在一起
			 //echo join('&',$prev);
			// var_dump($prev);exit;
			$prev= http_build_query($prev);
			$next = http_build_query($next);
			//echo $prev;

			//上一页路径
			$prevpath = $url.'?'.$prev;
			//下一页路径
			$nextpath = $url.'?'.$next;
			//echo $prevpath;
			//echo $nextpath;
			$str ='';
			$str.='<a href="'.$prevpath.'">上一页</a>';
			$str .='<a href="'.$nextpath.'">下一页</a>';
			return $str;
		} 
	}