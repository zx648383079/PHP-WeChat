<?php
namespace Service\Home;

use Domain\Model\EmpireModel;
use Zodream\Domain\Response\ResponseResult;
use Zodream\Domain\Routing\Controller as BaseController;
use Zodream\Infrastructure\EventManager\EventManger;
use Zodream\Infrastructure\Factory;
use Zodream\Infrastructure\Request;
use Zodream\Infrastructure\Traits\AjaxTrait;

abstract class Controller extends BaseController {
	use AjaxTrait;
	public function prepare() {
		$data = EmpireModel::query('option')->findAll(array(
			'where' => array(
				'autoload' => 'yes'
			)
		));
		foreach ($data as $item) {
			$this->send($item['name'], $item['value']);
		}
	}

	/**
	 * 执行缓存
	 * @param $id
	 */
	public function runCache($id) {
		$id = md5($id);
		$update = Request::get('cache', false);
		if (empty($update) && ($cache = Factory::cache()->get($id))) {
			return ResponseResult::make($cache);
		}
		EventManger::getInstance()->add('showView', function ($content) use ($id) {
			Factory::cache()->set($id, $content, 12 * 3600);
		});
	}
}