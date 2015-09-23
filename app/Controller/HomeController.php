<?php
namespace App\Controller;

use App\App;
use App\Lib\File\FDir;
use App\Model\BlogModel;

class HomeController extends Controller
{
	function index()
	{
		
		$this->send('title','主页');
		$this->show('index');
	}
	
	function blog()
	{
		$blogs = new BlogModel();
		$data = $blogs->findList('','id,pid,title,udate');
		$this->show('blog',
			array(
				'data' => $data,
				'title' => '博客'
			)
		);
	}
} 