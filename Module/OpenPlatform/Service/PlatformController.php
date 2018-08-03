<?php
namespace Module\OpenPlatform\Service;


use Module\OpenPlatform\Domain\Model\PlatformModel;




class PlatformController extends Controller {

    public function indexAction() {
        $model_list = PlatformModel::where('user_id', auth()->id())->page();
        return $this->show(compact('model_list'));
    }

    public function createAction() {
        return $this->runMethodNotProcess('edit', ['id' => null]);
    }

    public function editAction($id) {
        $model = PlatformModel::findOrNew($id);
        return $this->show(compact('model'));
    }

    public function saveAction() {
        $id = intval(app('request')->get('id'));
        $data = app('request')->get();
        unset($data['appid']);
        unset($data['secret']);
        if ($id > 0) {
            $model = PlatformModel::where('user_id', auth()->id())
                ->where('id', $id)->one();
        } else {
            $model = new PlatformModel();
            $model->generateNewId();
        }
        if (empty($model)) {
            return $this->jsonFailure('应用不存在');
        }
        if ($model->load() && $model->save()) {
            return $this->jsonSuccess([
                'url' => url('./platform')
            ]);
        }
        return $this->jsonFailure($model->getFirstError());
    }

    public function deleteAction($id) {
        PlatformModel::where('id', $id)->delete();
        return $this->jsonSuccess([
            'url' => url('./platform')
        ]);
    }
}