<?php
namespace Service\Admin;

use Domain\Model\EmpireModel;
use Zodream\Domain\Model;
use Zodream\Domain\Response\Redirect;
use Zodream\Domain\Routing\Controller as BaseController;
use Zodream\Domain\Routing\UrlGenerator;
use Zodream\Infrastructure\Log;

abstract class Controller extends BaseController {
	protected function rules() {
		return array(
			'*' => '@'
		);
	}


	public function prepare() {
		if (UrlGenerator::hasUri('account')) {
			return;
		}
		/*$model = new MessagesModel();
		$tasks = new TasksModel();
		$this->send(array(
			'usermessages' => $model->findTitle(),
			'noread' => $model->findNoReaded(),
			'newtasks' => $tasks->findNewTasks()
		));*/
	}

	/**
	 * @param string|Model $table
	 * @param string|int $id
	 */
	protected function delete($table, $id) {
		if (is_string($table)) {
			$table = EmpireModel::query($table);
		}
		$row = $table->deleteById($id);
		if (empty($row)) {
			Log::save("未成功删除表{$table->getTable()}中的Id".$id, 'delete');
			Redirect::to(-1, 2, '删除失败！');
		}
		Log::save("成功删除表{$table->getTable()}中的Id".$id, 'delete');
		Redirect::to(-1, 2, '删除成功！');
	}
}