<?php
namespace Module\CMS\Service\Admin;

use Module\CMS\Domain\Model\CategoryModel;
use Module\ModuleController;


class Controller extends ModuleController {

    public $layout = '/Admin/layouts/main';

    protected function rules() {
        return [
            '*' => '@'
        ];
    }

    public function prepare() {
        $cat_menu = CategoryModel::select('id', 'title', 'parent_id')->where('type', 0)->all();
        $this->send(compact('cat_menu'));
    }

    protected function getUrl($path, $args = []) {
        return url('./admin/'.$path, $args);
    }
}