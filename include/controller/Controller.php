<?php
	/****************************************************
	*控制器基类
	*
	*
	*******************************************************/
	
	
	class Controller{
		
		private $data;
		
		//要传的数据
		function send($key,$value="")
		{
			if(empty($value))
			{
				$this->data['data']=$key;
			}else
			{
				$this->data[$key]=$value;
			}
		}
		
		//加载视图
		function show($name="index")
		{
			if(!empty($this->data))
			{
				extract($this->data);    //从数组导成变量；
			}
			
			header( 'Content-Type:text/html;charset=utf-8 ');
			include(NWAYSVIEW.$name.".php");
			exit;
		} 
		
		//返回JSON数据
		function ajaxJson($data,$type='JSON')
		{
			switch (strtoupper($type)){
	            case 'JSON' :
	                // 返回JSON数据格式到客户端 包含状态信息
	                header('Content-Type:application/json; charset=utf-8');
	                exit(json_encode($data));
	            case 'XML'  :
	                // 返回xml格式数据
	                header('Content-Type:text/xml; charset=utf-8');
	                exit(xml_encode($data));
	            case 'JSONP':
	                // 返回JSON数据格式到客户端 包含状态信息
	                header('Content-Type:application/json; charset=utf-8');
	                $handler  =   isset($_GET['callback']) ? $_GET['callback'] : 'jsonpReturn';
	                exit($handler.'('.json_encode($data).');');  
	            case 'EVAL' :
	                // 返回可执行的js脚本
	                header('Content-Type:text/html; charset=utf-8');
	                exit($data);            
	        }
			
			exit;
		}
	}