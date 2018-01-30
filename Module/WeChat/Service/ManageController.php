<?php
namespace Module\WeChat\Service;

use Module\ModuleController;
use Module\WeChat\Domain\Model\WeChatModel;

class ManageController extends ModuleController {
    public function indexAction() {
        $model_list = WeChatModel::all();
        return $this->show(compact('model_list'));
    }
}