<?php
namespace Module\WeChat\Service\Admin;

use Module\WeChat\Domain\Model\MediaModel;

class MediaController extends Controller {

    protected function rules() {
        return [
            '*' => 'w'
        ];
    }

    public function indexAction($type = null) {
        $model_list = MediaModel::when(!empty($type), function ($query) use ($type) {
            $query->where('type', $type);
        })->page();
        return $this->show(compact('model_list', 'type'));
    }

    public function createAction() {
        return $this->runMethod('edit', ['id' => null]);
    }

    public function editAction($id) {
        $model = MediaModel::findOrNew($id);
        return $this->show(compact('model'));
    }
}