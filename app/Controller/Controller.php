<?php
	/****************************************************
	*控制器基类
	*
	*
	*******************************************************/
	namespace App\Controller;
	
	use App\Main;
	use App\Lib\Lang;
	use App\Lib\Validation;

	class Controller{
		function __construct()
		{
			Main::$data = Main::config('app');
			Main::$data['lang'] = Lang::$language;
		}
		

		/**
		 * 验证数据
		 *
		 * @param $request array 要验证的数据
		 * @param $param array  验证的规则
		 * @return array
         */
		function validata($request,$param)
		{
			$_vali = new Validation();
			$result = $_vali->make($request,$param);
			
			if(!$result)
			{
				$result = $_vali->error;
			}
			
			return $result;
		}
		
		//要传的数据
		function send($key , $value = "")
		{
			if(empty($value))
			{
				Main::$data['data'] = $key;
			}else
			{
				Main::$data[$key] = $value;
			}
		}
		
		
		
		//加载视图
		function show($name = "index",$data = array())
		{
			if(!empty($data))
			{
				Main::$data = array_merge(Main::$data , $data);
			}
			
			if ( APP_API )
			{
				$this->ajaxJson(Main::$data);
			}else{
				 if (extension_loaded('zlib')) { 
					if (  !headers_sent() AND isset($_SERVER['HTTP_ACCEPT_ENCODING']) && 
						strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE) 
					//页面没有输出且浏览器可以接受GZIP的页面 
					{ 
						ob_start('ob_gzhandler'); 
					} 
				} 
				header( 'Content-Type:text/html;charset=utf-8 ');
				Main::extend($name);
				ob_end_flush();
				exit;
			}
			
		} 
		
		//返回JSON数据
		function ajaxJson($data,$type = 'JSON')
		{
			switch (strtoupper($type)){
	            case 'JSON' :
	                // 返回JSON数据格式到客户端 包含状态信息
	                header('Content-Type:application/json; charset=utf-8');
	                exit(json_encode($data));
	            case 'XML'  :
	                // 返回xml格式数据
	                header('Content-Type:text/xml; charset=utf-8');
	                exit($this->xml_encode($data));
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

		/**
		 * 数组转XML
		 *
		 * @param array $data 要转的数组
		 * @param string $rootNodeName
		 * @param null $xml
		 * @return mixed
         */
		function xml_encode($data, $rootNodeName = 'data', $xml=null)
		{
			// turn off compatibility mode as simple xml throws a wobbly if you don't.
			if (ini_get('zend.ze1_compatibility_mode') == 1)
			{
				ini_set ('zend.ze1_compatibility_mode', 0);
			}

			if ($xml == null)
			{
				$xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$rootNodeName />");
			}

			// loop through the data passed in.
			foreach($data as $key => $value)
			{
				// no numeric keys in our xml please!
				if (is_numeric($key))
				{
					// make string key...
					$key = "unknownNode_". (string) $key;
				}

				// replace anything not alpha numeric
				$key = preg_replace('/[^a-z]/i', '', $key);

				// if there is another array found recrusively call this function
				if (is_array($value))
				{
					$node = $xml->addChild($key);
					// recrusive call.
					$this->xml_encode($value, $rootNodeName, $node);
				}
				else
				{
					// add single node.
					$value = htmlentities($value);
					$xml->addChild($key,$value);
				}

			}
			// pass back as string. or simple xml object if you want!
			return $xml->asXML();
		}
		
		function showImg($img)
		{
			header('Content-type:image/png');
			imagepng($img);
			imagedestroy($img);
		}
	}