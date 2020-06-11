<?php
namespace Module\Shop\Service;

use Module\ModuleController;
use Module\Shop\Domain\Models\CategoryModel;
use Module\Shop\Domain\Repositories\ArticleRepository;
use Module\Shop\Module;

class Controller extends ModuleController {
    public $layout = 'main';

    public function sendWithShare() {
        $categories_tree = CategoryModel::cacheTree();
        $cart = Module::cart();
        $notice_list = ArticleRepository::getNotices();
        $this->send(compact('categories_tree', 'cart', 'notice_list'));
        return $this;
    }

    protected function runActionMethod($action, $vars = array()) {
        return $this->redirect('/');
    }

    public function redirectWithAuth() {
        return $this->redirect(['./member/login', 'redirect_uri' => url()->full()]);
    }
}