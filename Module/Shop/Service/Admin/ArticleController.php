<?php
namespace Module\Shop\Service\Admin;


use Module\Shop\Domain\Model\ArticleCategoryModel;
use Module\Shop\Domain\Model\ArticleModel;

class ArticleController extends Controller {

    public function indexAction() {
        return $this->show();
    }

    public function createAction() {
        $cat_list = ArticleCategoryModel::all();
        return $this->show(compact('cat_list'));
    }

    public function saveAction() {
        $model = new ArticleModel();
        if ($model->load() && $model->autoIsNew()->save()) {
            return $this->redirectWithMessage($this->getUrl('article'), '保存成功！');
        }
        return $this->redirectWithMessage($this->getUrl('article'), $model->getFirstError());
    }

    public function categoryAction() {
        $cat_list = ArticleCategoryModel::all();
        return $this->show(compact('cat_list'));
    }

    public function createCategoryAction() {
        $cat_list = ArticleCategoryModel::all();
        return $this->show(compact('cat_list'));
    }

    public function saveCategoryAction() {
        $model = new ArticleCategoryModel();
        if ($model->load() && $model->autoIsNew()->save()) {
            return $this->redirectWithMessage($this->getUrl('article/category'), '保存成功！');
        }
        return $this->redirectWithMessage($this->getUrl('article/category'), $model->getFirstError());
    }

    public function deleteLogAction($id) {
        ArticleCategoryModel::where('id', $id)->delete();
        return $this->jsonSuccess([
            'url' => $this->getUrl('category')
        ]);
    }
}