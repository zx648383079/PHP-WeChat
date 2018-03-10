<?php
namespace Module\WeChat\Service\Admin;

use Module\ModuleController;
use Zodream\Service\Factory;
use Zodream\Service\Routing\Url;

class Controller extends ModuleController {

    protected function rules() {
        return [
            '*' => '@'
        ];
    }

    protected function getUrl($path, $args = []) {
        return (string)Url::to('./admin/'.$path, $args);
    }

    public function weChatId($id = null) {
        static $wid;
        if (!is_null($id)) {
            Factory::session([
                'wid' => $id
            ]);
            return $wid = $id;
        }
        if (empty($wid)) {
            $wid = Factory::session('wid');
        }
        return $wid;
    }

    protected function processRule($rule) {
        if ($rule == 'w') {
            return !empty($this->weChatId()) ?: $this->redirect($this->getUrl('manage'));
        }
        return parent::processRule($rule);
    }
}