<?php
namespace App\Controller;

use App;
use App\Lib\File\FDir;
use App\Model\MethodModel;
use App\Model\KindModel;

class HomeController extends Controller
{
	function indexAction()
	{
		$this->send('title','主页');
		
		$s = App::$request->get('s');
		if(empty($s))
		{
			$this->show('index');			
		}else{
			$kind = new KindModel();
			$kinds = $kind->findList('','id,name');
			$model = new MethodModel();
			$data = $model->findList(array(
				"`keys` like %{$s}%",
				'or' => "title like %{$s}%"
				));
			$this->show('so', array(
					's' => $s,
					'data' => $data,
					'kind' => $kinds
				)
			);			
		}
	}
	
	function methodAction()
	{
		$id = App::$request->get('id');
		$model = new MethodModel();
		$data = $model->findList('id = '.$id);
		$this->show('method',
			array(
				'data' => $data,
				'title' => 'Method'
			)
		);
	}
	
	function createAction()
	{
		if( App::$request->isPost() )
		{
			$post = App::$request->post('title,keys,kind,content');
			$error = $this->validata( $post , array(
				'title' => 'max:40|required',
				'keys' =>'max:40|required',
				'kind' => 'number|required',
				'content' => 'required'
			));
			
			if(!is_bool($error))
			{
				$this->send(array(
					'error' => $error,
					'data' => $post
				));
			}else{
				if( $post['kind'] == 1000 && !empty($name = App::$request->post('other') ))
				{
					$model = new KindModel();
					$post['kind'] = $model -> fill(array('name' => $name ) );
					
				}
				
				if($post['kind'] != 1000)
				{
					$model = new MethodModel();
					$id = $model -> fill( $post );
					App::redirect('?v=method&id='.$id);
				}
			}
		}else {
			$kind = new KindModel();
			$kinds = $kind->findList('','id,name');
			$this->show('create',array(
				'title' => 'Create Method',
				'kind' => $kinds
			));
		}
	}
	
	function editAction()
	{
		$id = App::$request->get('id', 1 );
		if( App::$request->isPost() )
		{
			$post = App::$request->post('title,keys,kind,content');
			$error = $this->validata( $post , array(
				'title' => 'max:40|required',
				'keys' =>'max:40|required',
				'kind' => 'number|required',
				'content' => 'required'
			));
			
			if(!is_bool($error))
			{
				$this->send(array(
					'error' => $error,
					'data' => $post
				));
			}else{
				$model -> update( $post , 'id = '.$id);
				App::redirect('?v=method&id='.$id);
			}
		}else {
			$model = new MethodModel();
			$data = $model->findById($id);
			$kind = new KindModel();
			$kinds = $kind->findList('','id,name');
			$this->show('create',array(
				'title' => 'Create Method',
				'data' => $data,
				'kind' => $kinds
			));
		}
	}
} 