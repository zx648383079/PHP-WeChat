<?php
	namespace App\Controller;
	
	
	class AdminController extends Controller{
		
		protected $rules = array(
			'*' => '2'
		);
		
		function index(){
			//Auth::user()?"":redirect("/?c=auth");
			$this->send('title','后台');
			$this->show('admin');
		}
		
		function wechat()
		{
			$this->send('title','微信管理');
			$this->show('wechat');
		}
	} 